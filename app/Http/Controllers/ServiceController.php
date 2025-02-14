<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\ContrDocuments;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\ServiceContragent;
use App\Models\ServiceEquip;
use App\Models\ServiceExtra;
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
use Log;

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
    public function getExtraServices()
    {
        $serviceExtras = Service::getExtraServices();

        return $serviceExtras;
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $inActiveServices = Service::with([
            'mainServices.serviceSubs.equipment.category',
            'mainServices.serviceSubs.equipment.size',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'equipment.category',
            'equipment.size',
            'directory'
        ])->where('active', 0)->get()->groupBy('contragent_id')
            ->map(function ($services) {
                return $services->map(function ($service) {
                    if (isset($service->directory['files'])) {
                        $service->directory['files'] = json_decode($service->directory['files'], true) ?? [];
                    }
                    return $service;
                });
            });
        $activeServices = Service::with([
            'mainServices.serviceSubs.equipment.category',
            'mainServices.serviceSubs.equipment.size',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'equipment.category',
            'equipment.size',
            'directory'
        ])
            ->where('active', 1)
            ->get()
            ->groupBy('contragent_id')
            ->map(function ($services, $contragentId) {
                // Fetch ServiceContragent for this contragent_id
                $serviceContragent = ServiceContragent::where('contragent_id', $contragentId)->get();
                return [
                    'services' => $services->map(function ($service) {
                        if (isset($service->directory['files'])) {
                            $service->directory['files'] = json_decode($service->directory['files'], true) ?? [];
                        }
                        return $service;
                    }),
                    'contragent_data' => $serviceContragent
                ];
            });



        $paginatedInactive = $this->paginateCollection($inActiveServices, $perPage, $request);
        $paginatedActive = $this->paginateCollection($activeServices, $perPage, $request);

        $count_services_active = Service::where('active', 1)->count();
        $count_services_inactive = Service::where('active', 0)->count();
        $contragents_names = Contragents::pluck('name', 'id');

        $contragentsServiceData = ServiceContragent::all();

        return Inertia::render('Services/Index', [
            'contragentsServiceData' => $contragentsServiceData,
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
        $contracts = ContrDocuments::whereNotNull('contracts')->get();
        foreach ($contracts as $document) {
            $document->contracts = json_decode($document->contracts, true) ?? [];
        }
        
        $equipmentFormatted = $equipment->map(function ($item) {
            return [
                'id' => $item->id,
                'display' => $item->category->name . ' ' . $item->size->name . ' ' . $item->series,
            ];
        });
        return Inertia::render("Services/Create", ['equipmentFormatted' => $equipmentFormatted, 'contragents' => $contragents, 'equipment' => $equipment,'contracts' => $contracts]);
    }

    public function store(Request $request)
    {

        try {
            $user_id = Auth::id();
            $user = User::find($user_id);

            // Validate request data
            $request->validate([
                'contragent_id' => 'required|int|exists:contragents,id',
                'service_number' => 'required|string',
                'service_date' => 'required|date',
                'active' => 'required|boolean',
                'hyperlink' => 'nullable|string',
                'commentary' => 'nullable|string',
                'contract' => 'nullable',
                'equipment' => 'required|array',
                'equipment.*.equipment_id' => 'required|int|exists:equipment,id',
                'equipment.*.commentary' => 'required|string',
                'equipment.*.shipping_date  ' => 'nullable|date',
                'equipment.*.period_start_date' => 'required|date',
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
            $service = Service::create([
                'contragent_id' => $request->contragent_id,
                'service_date' => $request->service_date,
                'service_number' => $request->service_number,
                'contract' => $request->contract,
                'active' => $request->active,
                'commentary' => $request->commentary
            ]);

            foreach ($request->equipment as $equipmentData) {
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

            foreach ($request->extraServices as $extraService) {
                ServiceExtra::create([
                    'shipping_date' => $extraService['shipping_date'],
                    'service_id' => $service->id,
                    'type' => $extraService['item'],
                    'commentary' => $extraService['commentary'],
                    'price' => $extraService['price'],
                ]);
            }

            // Create Notification
            Notification::create([
                'type' => "Пользователь {$user->name} создал новую аренду №{$service->id}",
                'data' => ['service_id' => $service->id],
                'user_id' => $user_id
            ]);

            return back()->with('message', 'Аренда успешно создана');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
        $extraServices = ServiceExtra::where('service_id', $service->id)->get()->map(function ($service) {
            $serviceMapping = Service::getExtraServices();
            $service->value = $serviceMapping[$service->type] ?? $service->type;
            return $service;
        });
        if ($equipment) {
            $equipment->category_name = $equipment->category ? $equipment->category->name : null;
            $equipment->size_name = $equipment->size ? $equipment->size->name : null;
        }

        $subservices = ServiceSub::where('service_id', '=', $service->id)->with(['equipment', 'equipment.category', 'equipment.size'])->get();
        $serviceEquip = ServiceEquip::where('service_id', $service->id)->with('equipment', 'equipment.category', 'equipment.size', 'serviceSubs.equipment', 'serviceSubs.equipment.category', 'serviceSubs.equipment.size')->get();
        return Inertia::render('Services/Edit', ['serviceEquip' => $serviceEquip, 'service' => $service, 'equipment' => $equipment, 'subservices' => $subservices, 'contragents' => $contragents, 'extraServices' => $extraServices]);
    }

    public function createIncident($id)
    {
        $service = Service::with([
            'mainServices.serviceSubs.equipment.category',
            'mainServices.serviceSubs.equipment.size',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->findOrFail($id);
        $user_id = Auth::id();
        $contragent_id = Contragents::findOrFail($service->contragent_id)->value('id');
        $position = Column::max('position') + 1;
        $column = Column::create([
            'position' => $position,
            'type' => 'adv',
            'creator_id' => $user_id

        ]);

        $position = $column->blocks()->max('position') + 1;

        $block = $column->blocks()->create([
            'type' => 'customer',
            'contragent_id' => $contragent_id,
            'position' => $position,
            'creator_id' => $user_id

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
        try {
            $user_id = Auth::id();
            $fullIncome = 0;
            $income = 0;
            $subincome = 0;
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
                'equipment.*.store' => 'nullable|int',
                'equipment.*.operating' => 'nullable|int',
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

            $servicePrice = ServiceExtra::where('service_id', $service->id)->pluck('price');

            foreach ($servicePrice as $price) {
                $fullIncome += $price;
            }

            if ($request->extraServices) {
                foreach ($request->extraServices as $extraService) {
                    $conditions = [
                        'service_id' => $service->id,
                    ];

                    if (isset($extraService['type'])) {
                        $conditions['type'] = $extraService['type'];
                    }

                    ServiceExtra::updateOrCreate(
                        $conditions,
                        [
                            'shipping_date' => $extraService['shipping_date'] ?? null,
                            'commentary' => $extraService['commentary'] ?? null,
                            'price' => $extraService['price'] ?? null,
                        ]
                    );
                }
            }
            foreach ($request->equipment as $index => $equipmentData) {
                try {

                    $equipment_id = $equipmentData['equipment_id'];
                    $equipment = Equipment::findOrFail($equipment_id);
                    $category = $equipment->category_id;
                    $size = $equipment->size_id;

                    $equipmentPrice = EquipmentPrice::where('category_id', $category)
                        ->where('size_id', $size)
                        ->where('archive', false)
                        ->first();

                    if (!$equipmentPrice) {
                        return back()->with('error', 'Цена не установлена');
                    }

                    $store_price = $equipmentPrice->store_price;
                    $operation_price = $equipmentPrice->operation_price;

                    $operating = $equipmentData['operating'] ?? 0;
                    $store = $equipmentData['store'] ?? 0;

                    if ($operating > 0) {
                        $days_operating = $operating / 24;
                        $store = max(0, $store - $days_operating);
                    }

                    $income = ($store * $store_price) + ($operating * $operation_price);

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

                            if ($subOperating > 0) {
                                $days_operating = $subOperating / 24;
                                $subStore = max(0, $subStore - $days_operating); // Обновляем subStore
                            }

                            $subincome = ($subStore * $store_price) + ($subOperating * $operation_price);

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
                                'store' => $subStore, // Теперь сохраняем обновленное значение
                                'income' => $subincome
                            ]);

                            $fullIncome += $subincome;
                        }
                    }
                } catch (\Exception $e) {
                    return back()->with('error', $e->getMessage());
                }
            }

            $service->update(['full_income' => $fullIncome]);
            return back()->with('message', 'Аренда успешно обновлена');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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

    public function setContragentServiceData(Request $request)
    {

        try {
            $contragent_id = $request->input('contragent_id');
            $commentary = $request->input('commentary');
            $shipping_date = $request->input('shipping_date');
            $found = ServiceContragent::where('contragent_id', $contragent_id)->first();
            if ($found) {
                $found->update([
                    'contragent_id' => $contragent_id,
                    'commentary' => $commentary,
                    'shipping_date' => $shipping_date
                ]);
            } else {
                ServiceContragent::create([
                    'contragent_id' => $contragent_id,
                    'commentary' => $commentary,
                    'shipping_date' => $shipping_date
                ]);
            }


            return back()->with('message', 'Данные обновлены');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
