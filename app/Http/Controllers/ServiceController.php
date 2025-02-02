<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\ServiceEquip;
use App\Models\ServiceSub;
use App\Models\Column;
use App\Models\User;
use App\Models\Notification;
use App\Models\EquipmentPrice;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ServiceModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ServiceController extends Controller
{
    private function formatServicesByYearMonth($services_by_year_month)
    {
        $month_names = [
            '01' => 'jan',
            '02' => 'feb',
            '03' => 'mar',
            '04' => 'apr',
            '05' => 'may',
            '06' => 'jun',
            '07' => 'jul',
            '08' => 'aug',
            '09' => 'sep',
            '10' => 'oct',
            '11' => 'nov',
            '12' => 'dec',
        ];

        $formatted_data = [];

        foreach ($services_by_year_month as $service) {
            $year = $service->year;
            $month = $month_names[$service->month] ?? $service->month; 
            $count = $service->count;

            if (!isset($formatted_data[$year])) {
                $formatted_data[$year] = [
                    'year' => $year,
                    'months' => []
                ];
            }

            // Add the count for the specific month
            $formatted_data[$year]['months'][$month] = $count;
        }

        return array_values($formatted_data); 
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
    
        $inActiveServices = Service::with([
            'subservices.equipment.category',
            'subservices.equipment.size',
            'service.equipment.category',
            'service.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->where('active', 0)->get()->groupBy('contragent_id');
    
        $activeServices = Service::with([
            'mainServices.serviceSubs.equipment.category',
            'mainServices.serviceSubs.equipment.size',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'equipment.category',
            'equipment.size'
                ])->where('active', 1)->get()->groupBy('contragent_id');
    
        $paginatedInactive = $this->paginateCollection($inActiveServices, $perPage, $request);
        $paginatedActive = $this->paginateCollection($activeServices, $perPage, $request);
    
        $count_services_active = Service::where('active', 1)->count();
        $count_services_inactive = Service::where('active', 0)->count();
        $contragents_names = Contragents::pluck('name', 'id');
    
        return Inertia::render('Services/Index', [
            'services' => $paginatedInactive,
            'activeServices' => $paginatedActive,
            'contragents_names' => $contragents_names,
            'count_services_active' => $count_services_active,
            'count_services_inactive' => $count_services_inactive
        ]);
    }
    
    private function paginateCollection(Collection $collection, $perPage, $request)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        return new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
    

    public function create()
    {
        $equipment = Equipment::with('category', 'size')->get();
        $equipment_all = Equipment::all();
        $contragents = Contragents::all();

        // Transform the equipment data into the desired format
        $equipmentFormatted = $equipment->map(function ($item) {
            return [
                'id' => $item->id,
                'display' => $item->category->name . ' ' . $item->size->name . ' ' . $item->series,
            ];
        });
        return Inertia::render("Services/Create", ['equipmentFormatted' => $equipmentFormatted, 'contragents' => $contragents, 'equipment' => $equipment]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);

        // Validate request data
        $request->validate([
            'contragent_id' => 'required|int|exists:contragents,id',
            'service_number' => 'required|string',
            'service_date' => 'required|date',
            'active' => 'required|boolean',
            'hyperlink' => 'nullable|string',
            'equipment' => 'required|array',
            'equipment.*.equipment_id' => 'nullable|int|exists:equipment,id',
            'equipment.*.commentary' => 'nullable|string',
            'equipment.*.period_start_date' => 'nullable|date',
            'equipment.*.return_date' => 'nullable|date',
            'equipment.*.period_end_date' => 'nullable|date',
            'equipment.*.store' => 'nullable|string',
            'equipment.*.operating' => 'nullable|string',
            'equipment.*.income' => 'nullable|numeric',
            'equipment.*.subEquipment' => 'array|nullable',
            'equipment.*.subEquipment.*.subequipment_id' => 'nullable|int',
            'equipment.*.subEquipment.*.shipping_date' => 'nullable|date',
            'equipment.*.subEquipment.*.period_start_date' => 'nullable|date',
            'equipment.*.subEquipment.*.return_date' => 'nullable|date',
            'equipment.*.subEquipment.*.period_end_date' => 'nullable|date',
            'equipment.*.subEquipment.*.commentary' => 'nullable|string',
            'equipment.*.subEquipment.*.income' => 'nullable|numeric'
        ]);
        // Create the Service
        $service = Service::create([
            'contragent_id' => $request->contragent_id,
            'service_date' => $request->service_date,
            'service_number' => $request->service_number,
            'active' => $request->active,
        ]);

        // Loop through Equipment List
        foreach ($request->equipment as $equipmentData) {
            // Create ServiceEquipment
            $serviceEquipment = ServiceEquip::create([
                'service_id' => $service->id,
                'equipment_id' => $equipmentData['equipment_id'],
                'shipping_date' => $equipmentData['shipping_date'] ?? null,
                'period_start_date' => $equipmentData['period_start_date'] ?? null,
                'return_date' => $equipmentData['return_date'] ?? null,
                'period_end_date' => $equipmentData['period_end_date'] ?? null,
                'store' => $equipmentData['store'] ?? null,
                'operating' => $equipmentData['operating'] ?? null,
                'commentary' => $equipmentData['commentary'] ?? null,
                'income' => $equipmentData['income'] ?? null,
            ]);

            // Loop through SubEquipment (if exists)
            if (!empty($equipmentData['subEquipment'])) {
                foreach ($equipmentData['subEquipment'] as $subEquipmentData) {
                    ServiceSub::create([
                        'service_id' => $service->id,
                        'service_equipment_id' => $serviceEquipment->id,
                        'subequipment_id' => $subEquipmentData['subequipment_id'],
                        'shipping_date' => $subEquipmentData['shipping_date'] ?? null,
                        'period_start_date' => $subEquipmentData['period_start_date'] ?? null,
                        'return_date' => $subEquipmentData['return_date'] ?? null,
                        'period_end_date' => $subEquipmentData['period_end_date'] ?? null,
                        'commentary' => $subEquipmentData['commentary'] ?? null,
                        'income' => $subEquipmentData['income'] ?? null,
                    ]);
                }
            }
        }

        // Create Notification
        Notification::create([
            'type' => "Пользователь {$user->name} создал новую аренду №{$service->id}",
            'data' => ['service_id' => $service->id],
            'user_id' => $user_id
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }


    public function storeHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string'
        ]);

        $service = Service::find($id);

        $service->hyperlink = $request->input('hyperlink');

        $service->save();
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $equipment = Equipment::where('id', $service->equipment_id)
            ->with(['category', 'size'])
            ->first();
        $contragents = Contragents::all();

        if ($equipment) {
            $equipment->category_name = $equipment->category ? $equipment->category->name : null;
            $equipment->size_name = $equipment->size ? $equipment->size->name : null;
        }

        $subservices = ServiceSub::where('service_id', '=', $service->id)->with(['equipment', 'equipment.category', 'equipment.size'])->get();
        $serviceEquip = ServiceEquip::where('service_id', $service->id)->with('equipment', 'equipment.category', 'equipment.size', 'serviceSubs.equipment', 'serviceSubs.equipment.category', 'serviceSubs.equipment.size')->get();
        return Inertia::render('Services/Edit', ['serviceEquip' => $serviceEquip, 'service' => $service, 'equipment' => $equipment, 'subservices' => $subservices, 'contragents' => $contragents]);
    }

    public function createIncident($id)
    {
        $service = Service::findOrFail($id);
        $user_id = Auth::id();
        $equipment = Equipment::findOrFail($service->equipment_id)->value('id');
        $contragent_id = Contragents::findOrFail($service->contragent_id)->value('id');
        $position = Column::max('position') + 1;
        $column = Column::create(['position' => $position, 'type' => 'adv']);

        $position = $column->blocks()->max('position') + 1;

        $block = $column->blocks()->create([
            'type' => 'customer',
            'contragent_id' => $contragent_id,
            'equipment_id' => $equipment,
            'position' => $position
        ]);
        if ($service->subequiment_id !== null) {
            $block_subequipment = $block->subequipment()->create([
                'block_id' => $block->id,
                'subequipment_id' => $service->subequipment_id
            ]);
        }


        return redirect()->route('incident.index')->with('success', 'Column updated successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $user_id = Auth::id();
        $fullIncome = 0;

        $request->validate([
            'contragent_id' => 'nullable|int|exists:contragents,id',
            'service_number' => 'nullable|string',
            'service_date' => 'nullable|date',
            'active' => 'nullable|boolean',
            'hyperlink' => 'nullable|string',
            'equipment' => 'nullable|array',
            'equipment.*.equipment_id' => 'nullable|int|exists:equipment,id',
            'equipment.*.commentary' => 'nullable|string',
            'equipment.*.period_start_date' => 'nullable|date',
            'equipment.*.return_date' => 'nullable|date',
            'equipment.*.period_end_date' => 'nullable|date',
            'equipment.*.store' => 'nullable|string',
            'equipment.*.operating' => 'nullable|string',
            'equipment.*.income' => 'nullable|numeric',
            'equipment.*.subEquipment' => 'array|nullable',
            'equipment.*.subEquipment.*.subequipment_id' => 'nullable|int',
            'equipment.*.subEquipment.*.shipping_date' => 'nullable|date',
            'equipment.*.subEquipment.*.period_start_date' => 'nullable|date',
            'equipment.*.subEquipment.*.return_date' => 'nullable|date',
            'equipment.*.subEquipment.*.period_end_date' => 'nullable|date',
            'equipment.*.subEquipment.*.commentary' => 'nullable|string',
            'equipment.*.subEquipment.*.income' => 'nullable|numeric'
        ]);

        $isChangingToInactive = $service->active && !$request->get('active', true);

        $service->update(array_merge(
            $request->only([
                'contragent_id',
                'shipping_date',
                'service_number',
                'service_date',
                'period_start_date',
                'return_date',
                'period_end_date',
                'store',
                'operating',
                'return_reason',
                'active'
            ]),
            ['full_income' => 0] 
        ));

        if ($isChangingToInactive) {
            Notification::create([
                'type' => 'Пользователь ' . User::find($user_id)->name . ' закрыл аренду №' . $service->id,
                'data' => ['service_id' => $service->id],
                'user_id' => $user_id
            ]);
        }

        foreach ($request->equipment as $equipmentData) {
            $equipment_id = $equipmentData['equipment_id'];
            $category = Equipment::where('id', $equipment_id)->value('category_id');
            $size = Equipment::where('id', $equipment_id)->value('size_id');

            $store_price = EquipmentPrice::where('category_id', $category)->where('size_id', $size)->where('archive', false)->value('store_price');
            $operation_price = EquipmentPrice::where('category_id', $category)->where('size_id', $size)->where('archive', false)->value('operation_price');

            $operating = $equipmentData['operating'] ?? 0;
            $store = $equipmentData['store'] ?? 0;
            $income = ($operating == 0) ? ($store * $store_price) : (($operating * $operation_price) + ($store * $store_price));

            $serviceEquipment = ServiceEquip::updateOrCreate([
                'service_id' => $service->id,
                'equipment_id' => $equipment_id
            ], [
                'commentary' => $equipmentData['commentary'] ?? "",
                'period_start_date' => $equipmentData['period_start_date'] ?? null,
                'return_date' => $equipmentData['return_date'] ?? null,
                'period_end_date' => $equipmentData['period_end_date'] ?? null,
                'store' => $store,
                'shipping_date' => $equipmentData['shipping_date'] ?? null,
                'operating' => $operating,
                'income' => $income
            ]);

            $fullIncome += $income;

            if (!empty($equipmentData['subEquipment']) && is_array($equipmentData['subEquipment'])) {
                foreach ($equipmentData['subEquipment'] as $subEquipmentData) {
                    $subEquipment_id = $subEquipmentData['subequipment_id'];
                    $subOperating = $subEquipmentData['operating'] ?? 0;
                    $subStore = $subEquipmentData['store'] ?? 0;
                    $subincome = ($subOperating == 0) ? ($subStore * $store_price) : (($subOperating * $operation_price) + ($subStore * $store_price));

                    ServiceSub::updateOrCreate([
                        'service_id' => $service->id,
                        'subequipment_id' => $subEquipment_id,
                        'service_equipment_id' => $serviceEquipment->id
                    ], [
                        'shipping_date' => $subEquipmentData['shipping_date'] ?? null,
                        'period_start_date' => $subEquipmentData['period_start_date'] ?? null,
                        'return_date' => $subEquipmentData['return_date'] ?? null,
                        'period_end_date' => $subEquipmentData['period_end_date'] ?? null,
                        'operating' => $subOperating,
                        'store' => $subStore,
                        'income' => $subincome
                    ]);

                    $fullIncome += $subincome;
                }
            }
        }

        $service->update(['full_income' => $fullIncome]);

    }
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $subservice = ServiceSub::where('service_id', $id);

        $service->delete();
        $subservice->delete();
    }

    public function destroyServiceEquip($id)
    {
        $serviceEquip = ServiceEquip::findOrFail($id);

        $serviceEquip->serviceSubs()->delete();

        $serviceEquip->delete();
    }
}
