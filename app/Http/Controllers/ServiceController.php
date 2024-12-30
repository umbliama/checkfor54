<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\ServiceSub;
use App\Models\Column;
use App\Models\User;
use App\Models\Notification;
use App\Models\EquipmentPrice;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ServiceModel;

class ServiceController extends Controller
{
    private function formatServicesByYearMonth($services_by_year_month)
    {
        // Define a map for converting month numbers to month names
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

        // Initialize an empty array to hold the formatted data
        $formatted_data = [];

        // Loop through the raw data and format it
        foreach ($services_by_year_month as $service) {
            $year = $service->year;
            $month = $month_names[$service->month] ?? $service->month; // Convert month number to month name
            $count = $service->count;

            // Initialize the year if it doesn't exist in the array yet
            if (!isset($formatted_data[$year])) {
                $formatted_data[$year] = [
                    'year' => $year,
                    'months' => []
                ];
            }

            // Add the count for the specific month
            $formatted_data[$year]['months'][$month] = $count;
        }

        // Return the formatted data as an array
        return array_values($formatted_data); // Use array_values to reset the keys
    }

    public function index(Request $request)
    {

        $perPage = $request->input('perPage');
        $inActiveServices = Service::with([
            'subservices.equipment.category',
            'subservices.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->where('active', 0)->paginate($perPage);

        $activeServices = Service::with([
            'subservices.equipment.category',
            'subservices.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->where('active', 1)->paginate($perPage);


        // Retrieve all subservices and group them by service_id
        $subservices = ServiceSub::with(['equipment.category', 'equipment.size'])->get();
        $grouped_subservices = $subservices->groupBy('service_id');

        // Count active and inactive services/subservices
        $count_services_active = Service::where('active', 1)->count();
        $count_services_inactive = Service::where('active', 0)->count();

        $services_by_year_month = Service::selectRaw('strftime("%Y", service_date) as year, strftime("%m", service_date) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->get();

        // Format the data into the desired structure
        $formatted_data_months = $this->formatServicesByYearMonth($services_by_year_month);

        // Transform the services collection
        $inActiveServices->getCollection()->transform(function ($service) use ($grouped_subservices) {
            // Attach subservices to the service
            $service->subservices = collect($grouped_subservices->get($service->id) ?? []);



            $service->subservices = $service->subservices->map(function ($subservice) {
                if ($subservice->subequipment_id) {
                    $subservice->equipment_info = Equipment::with(['category', 'size'])->find($subservice->subequipment_id);
                } else {
                    $subservice->equipment_info = null;
                }
                return $subservice;
            });

            $service->equipment_info = $service->equipment;

            if ($service->equipment) {
                $service->equipment_info->category_name = $service->equipment->category->name ?? null;
                $service->equipment_info->size_name = $service->equipment->size->name ?? null;
            } else {
                $service->equipment_info = null;
            }


            return $service;
        });




        $contragents_names = Contragents::pluck('name', 'id');

        return Inertia::render('Services/Index', [
            'services' => $inActiveServices,
            'activeServices' => $activeServices,
            'contragents_names' => $contragents_names,
            'count_services_active' => $count_services_active,
            'count_services_inactive' => $count_services_inactive,
            'formatted_data_months' => $formatted_data_months
        ]);
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
        $user = User::where('user_id', $user_id)->get();
        $request->validate([
            'equipment_id' => 'required|int',
            'contragent_id' => 'required|int',
            'shipping_date' => 'required|date',
            'service_number' => "required|string",
            'service_date' => "required|date",
            'commentary' => "nullable|string",
            'period_start_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'period_end_date' => 'nullable|date',
            'store' => 'nullable|numeric',
            'operating' => 'nullable|numeric',
            'return_reason' => 'nullable',
            'active' => 'required|boolean',
            'income' => 'nullable|int',
            'hyperlink' => 'nullable|string',
            'subEquipment' => 'array|nullable',
            'subEquipment.*.subequipment_id' => 'nullable|int|exists:equipment,id',
            'subEquipment.*.shipping_date' => 'nullable|date',
            'subEquipment.*.period_start_date' => 'nullable|date',
            'subEquipment.*.return_date' => 'nullable|date',
            'subEquipment.*.period_end_date' => 'nullable|date',
            'subEquipment.*.income' => 'nullable|int'


        ]);

        $service = Service::create($request->only([
            'equipment_id',
            'contragent_id',
            'shipping_date',
            'commentary',
            'service_number',
            'service_date',
            'period_start_date',
            'return_date',
            'period_end_date',
            'store',
            'operating',
            'return_reason',
            'active',
            'income',
            'hyperlink'
        ]));


        foreach ($request->subEquipment as $subEquipmentData) {
            ServiceSub::create([
                'subequipment_id' => $subEquipmentData['subequipment_id'],
                'shipping_date' => $subEquipmentData['shipping_date'],
                'period_start_date' => $subEquipmentData['period_start_date'],
                'commentary' => $subEquipmentData['commentary'],
                'service_id' => $service->id,
            ]);
        }

        Notification::create([
            'type' => 'Пользователь ' . User::find($user_id)->name . ' создал новую аренда №' . $service->id,
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

        $subservices = ServiceSub::where('service_id', '=', $service->id)->get();

        return Inertia::render('Services/Edit', ['service' => $service, 'equipment' => $equipment, 'subservices' => $subservices, 'contragents' => $contragents]);
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

        $equipment_id = $request->get('equipment_id');
        $category = Equipment::where('id', $equipment_id)->value('category_id');
        $size = Equipment::where('id', $equipment_id)->value('size_id');
        $store_price = EquipmentPrice::where('category_id', $category)->where('size_id', $size)->where('archive', false)->value('store_price');
        $operation_price = EquipmentPrice::where('category_id', $category)->where('size_id', $size)->where('archive', false)->value('operation_price');


        $operating = $request->get('operating', 0);
        $store = $request->get('store', 0);

        if ($operating == 0) {
            $income = $store * $store_price;
        } else {
            $days = ceil($operating / 24);


            $income = ($operating * $operation_price) + ($store * $store_price);
        }


        // Validate the request data
        $request->validate([
            'equipment_id' => 'nullable|int',
            'contragent_id' => 'nullable|int',
            'shipping_date' => 'nullable|date',
            'service_number' => "nullable|string",
            'service_date' => "nullable|date",
            'period_start_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'period_end_date' => 'nullable|date',
            'store' => 'nullable|numeric',
            'operating' => 'nullable|numeric',
            'return_reason' => 'nullable',
            'active' => 'required|boolean',
            'income' => 'nullable|int',
            'subEquipment' => 'array|nullable',
            'subEquipment.*.subequipment_id' => 'nullable|int|exists:equipment,id',
            'subEquipment.*.shipping_date' => 'nullable|date',
            'subEquipment.*.period_start_date' => 'nullable|date',
            'subEquipment.*.return_date' => 'nullable|date',
            'subEquipment.*.period_end_date' => 'nullable|date',
            'subEquipment.*.income' => 'nullable|int'
        ]);
        $isChangingToInactive = $service->active && !$request->get('active', true);

        // Update the main service record
        $service->update(array_merge(
            $request->only([
                'equipment_id',
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
            ['income' => $income]
        ));

        if ($isChangingToInactive) {
            Notification::create([
                'type' => 'Пользователь ' . User::find($user_id)->name . ' закрыл аренду №' . $service->id,
                'data' => ['service_id' => $service->id],
                'user_id' => $user_id
            ]);
        }

        // Update or create sub-equipment records
        if ($request->has('subEquipment')) {
            foreach ($request->subEquipment as $subEquipmentData) {
                if (isset($subEquipmentData['id'])) {
                    // Update existing sub-equipment
                    $subService = ServiceSub::find($subEquipmentData['id']);
                    if ($subService) {
                        $subService->update([
                            'subequipment_id' => $subEquipmentData['subequipment_id'],
                            'shipping_date' => $subEquipmentData['shipping_date'],
                            'period_start_date' => $subEquipmentData['period_start_date'],
                            'commentary' => $subEquipmentData['commentary'] ?? null,
                            'return_date' => $subEquipmentData['return_date'],
                            'period_end_date' => $subEquipmentData['period_end_date'],
                            'income' => $subEquipmentData['income'] ?? null,
                        ]);
                    }
                } else {
                    // Create new sub-equipment record if it's a new entry
                    ServiceSub::create([
                        'subequipment_id' => $subEquipmentData['subequipment_id'],
                        'shipping_date' => $subEquipmentData['shipping_date'],
                        'period_start_date' => $subEquipmentData['period_start_date'],
                        'commentary' => $subEquipmentData['commentary'] ?? null,
                        'service_id' => $service->id,
                        'return_date' => $subEquipmentData['return_date'],
                        'period_end_date' => $subEquipmentData['period_end_date'],
                        'income' => $subEquipmentData['income'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $subservice = ServiceSub::where('service_id', $id);

        $service->delete();
        $subservice->delete();
    }

}
