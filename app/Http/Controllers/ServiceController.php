<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\ServiceSub;
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

    public function index()
    {


        $services = Service::with([
            'subservices.equipment.category',
            'subservices.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->paginate(10);

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
        $services->getCollection()->transform(function ($service) use ($grouped_subservices) {
            // Attach subservices to the service
            $service->subservices = collect($grouped_subservices->get($service->id) ?? []);



            // Map through subservices and attach equipment info based on subequipment_id
            $service->subservices = $service->subservices->map(function ($subservice) {

                if ($subservice->subequipment_id) {
                    // Fetch equipment info and eager load its category and size
                    $subservice->equipment_info = Equipment::with(['category', 'size'])
                        ->find($subservice->subequipment_id);

                } else {
                    $subservice->equipment_info = null; // No equipment info if subequipment_id is not present
                }


                return $subservice;
            });

            // Attach equipment info to the service itself
            $service->equipment_info = $service->equipment;

            if ($service->equipment) {
                // Attach category_name and size_name for service's equipment
                $service->equipment_info->category_name = $service->equipment->category->name ?? null;
                $service->equipment_info->size_name = $service->equipment->size->name ?? null;
            } else {
                // Set equipment_info to null if there's no equipment in the service
                $service->equipment_info = null;
            }

            return $service;
        });

        // Retrieve contragent names
        $contragents_names = Contragents::pluck('name', 'id');

        // Render the view with the transformed data
        return Inertia::render('Services/Index', [
            'services' => $services,
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
        $request->validate([
            'equipment_id' => 'required|int',
            'contragent_id' => 'required|int',
            'shipping_date' => 'required|date',
            'service_number' => "required|string",
            'service_date' => "required|date",
            'period_start_date' => 'required|date',
            'return_date' => 'nullable|date',
            'period_end_date' => 'nullable|date',
            'store' => 'nullable|string',
            'operating' => 'nullable|string',
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

        $service = Service::create($request->only([
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
            'active',
            'income'
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

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }


    public function edit($id)
    {   
        $service = Service::findOrFail($id);
        $equipment = Equipment::where('id','=', $service->equipment_id)->get();
        $subservices = ServiceSub::where('service_id','=', $service->id)->get();

        return Inertia::render('Services/Edit',['service' => $service, 'equipment' => $equipment,'subservices' => $subservices]);
    }

    public function update(Request $request, Service $service)
    {
        // Validate the request data
        $request->validate([
            'equipment_id' => 'required|int',
            'contragent_id' => 'required|int',
            'shipping_date' => 'required|date',
            'service_number' => "required|string",
            'service_date' => "required|date",
            'period_start_date' => 'required|date',
            'return_date' => 'nullable|date',
            'period_end_date' => 'nullable|date',
            'store' => 'nullable|string',
            'operating' => 'nullable|string',
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

        // Update the main service record
        $service->update($request->only([
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
            'active',
            'income'
        ]));

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

}
