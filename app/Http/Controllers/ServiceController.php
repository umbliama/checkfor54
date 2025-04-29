<?php
namespace App\Http\Controllers;

use App\Events\NotificationCountUpdated;
use App\Models\Column;
use App\Models\Contragents;
use App\Models\ContrDocuments;
use App\Models\Equipment;
use App\Models\EquipmentPrice;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\Service;
use App\Models\ServiceContragent;
use App\Models\ServiceEquip;
use App\Models\ServiceExtra;
use App\Models\ServiceSub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
                    'months' => [],
                ];
            }

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
            'mainServices.serviceSubs.equipment.directory',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'mainServices.equipment.directory',
            'equipment.category',
            'equipment.size',
            'directory',
        ])
            ->where('active', 0)
            ->get()
            ->groupBy('contragent_id')
            ->map(function ($services, $contragentId) {
                // Fetch all ServiceContragent records for this contragent
                $serviceContragents = ServiceContragent::where('contragent_id', $contragentId)->get();

                // Fetch the associated contragent with directory & hyperlink
                $contragent = Contragents::with('directory')->find($contragentId);

                return [
                    'services' => $services->map(function ($service) {
                        if (isset($service->directory['files'])) {
                            $service->directory['files'] = json_decode($service->directory['files'], true) ?? [];
                        }
                        return $service;
                    }),
                    'contragent_data' => $serviceContragents->map(function ($serviceContragent) use ($contragent) {
                        return [
                            'contragent_id' => $serviceContragent->contragent_id,
                            'shipping_date' => $serviceContragent->shipping_date,
                            'commentary' => $serviceContragent->commentary,
                            'hyperlink' => $contragent->hyperlink ?? null,
                            'directory' => $contragent->directory ? [
                                'commentary' => $contragent->directory->commentary,
                                'files' => $contragent->directory->files->map(function ($file) {
                                    return [
                                        'id' => $file->id,
                                        'filename' => $file->file_name,
                                        'path' => $file->file_path,
                                    ];
                                }),
                            ] : null,
                        ];
                    }),
                ];
            });
        $activeServices = Service::with([
            'mainServices.serviceSubs.equipment.category',
            'mainServices.serviceSubs.equipment.size',
            'mainServices.serviceSubs.equipment.directory',
            'mainServices.equipment.category',
            'mainServices.equipment.size',
            'mainServices.equipment.directory',
            'equipment.category',
            'equipment.size',
            'directory',
        ])
            ->where('active', 1)
            ->get()
            ->groupBy('contragent_id')
            ->map(function ($services, $contragentId) {
                // Fetch all ServiceContragent records for this contragent
                $serviceContragents = ServiceContragent::where('contragent_id', $contragentId)->get();

                // Fetch the associated contragent with directory & hyperlink
                $contragent = Contragents::with('directory')->find($contragentId);

                return [
                    'services' => $services->map(function ($service) {
                        if (isset($service->directory['files'])) {
                            $service->directory['files'] = json_decode($service->directory['files'], true) ?? [];
                        }
                        return $service;
                    }),
                    'contragent_data' => $serviceContragents->map(function ($serviceContragent) use ($contragent) {
                        return [
                            'contragent_id' => $serviceContragent->contragent_id,
                            'shipping_date' => $serviceContragent->shipping_date,
                            'commentary' => $serviceContragent->commentary,
                            'hyperlink' => $contragent->hyperlink ?? null,
                            'directory' => $contragent->directory ? [
                                'commentary' => $contragent->directory->commentary,
                                'files' => $contragent->directory->files->map(function ($file) {
                                    return [
                                        'id' => $file->id,
                                        'filename' => $file->file_name,
                                        'path' => $file->file_path,
                                    ];
                                }),
                            ] : null,
                        ];
                    }),
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
            'count_services_inactive' => $count_services_inactive,
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
        $lastServiceNum = Service::latest()->first();
        if ($lastServiceNum === null) {
            $lastServiceNum = 1;
        }
        $contragents = Contragents::with(['documents'])->get();
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
        return Inertia::render("Services/Create", ['equipmentFormatted' => $equipmentFormatted, 'contragents' => $contragents, 'equipment' => $equipment, 'contracts' => $contracts, 'lastServiceNum' => $lastServiceNum]);
    }

    public function store(Request $request)
    {

        try {
            $user_id = Auth::id();
            $user = User::find($user_id);

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
                'equipment.*.commentary' => 'nullable|string',
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
                'equipment.*.subEquipment.*.income' => 'nullable|numeric',
            ]);
            $service = Service::create([
                'contragent_id' => $request->contragent_id,
                'service_date' => $request->service_date,
                'service_number' => $request->service_number,
                'contract' => $request->contract,
                'active' => $request->active,
                'commentary' => $request->commentary,
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

            ServiceContragent::create([
                'contragent_id' => $service->contragent_id,
            ]);

            $notification = Notification::create([
                'type' => "Пользователь {$user->name} создал новую аренду",
                'data' => ['service_number' => $service->service_number],
                'created_by' => $user_id,
            ]);

            $this->sendNotificationToUsers($notification, $user_id);

            return back()->with('message', 'Аренда успешно создана');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeHyperLink(Request $request, $id)
    {
        try {
            $request->validate([
                'hyperlink' => 'required|string',
            ]);

            $service = Service::find($id);

            $service->hyperlink = $request->input('hyperlink');

            $service->save();

            return back()->with('message', 'Данные обновлены');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $equipment = Equipment::where('id', $service->equipment_id)
            ->with(['category', 'size'])
            ->first();
        $contragents = Contragents::with(['documents'])->get();
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
        $serviceEquip = ServiceEquip::where('service_id', $service->id)->with('equipment', 'equipment.category', 'equipment.size','equipment.directory', 'serviceSubs.equipment', 'serviceSubs.equipment.directory','serviceSubs.equipment.category', 'serviceSubs.equipment.size')->get();
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
            'equipment.size',
        ])->findOrFail($id);
        $user_id = Auth::id();
        $contragent_id = Contragents::findOrFail($service->contragent_id)->value('id');
        $position = Column::max('position') + 1;
        $column = Column::create([
            'position' => $position,
            'type' => 'adv',
            'creator_id' => $user_id,

        ]);

        $position = $column->blocks()->max('position') + 1;

        $block = $column->blocks()->create([
            'type' => 'customer',
            'contragent_id' => $contragent_id,
            'position' => $position,
            'creator_id' => $user_id,

        ]);
        if ($service->subequiment_id !== null) {
            $block_subequipment = $block->subequipment()->create([
                'block_id' => $block->id,
                'subequipment_id' => $service->subequipment_id,
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
                'contract' => 'nullable',
                'equipment' => 'nullable|array',
                'equipment.*.equipment_id' => 'nullable|int|exists:equipment,id',
                'equipment.*.commentary' => 'nullable|string',
                'equipment.*.period_start_date' => 'nullable|date',
                'equipment.*.return_date' => 'nullable|date',
                'equipment.*.period_end_date' => 'nullable|date',
                'equipment.*.store' => 'nullable|numeric',
                'equipment.*.operating' => 'nullable|numeric',
                'equipment.*.income' => 'nullable|numeric',
                'equipment.*.subEquipment' => 'array|nullable',
                'equipment.*.subEquipment.*.subequipment_id' => 'nullable|int',
                'equipment.*.subEquipment.*.shipping_date' => 'nullable|date',
                'equipment.*.subEquipment.*.period_start_date' => 'nullable|date',
                'equipment.*.subEquipment.*.return_date' => 'nullable|date',
                'equipment.*.subEquipment.*.period_end_date' => 'nullable|date',
                'equipment.*.subEquipment.*.store' => 'nullable|numeric',
                'equipment.*.subEquipment.*.operating' => 'nullable|numeric',
                'equipment.*.subEquipment.*.commentary' => 'nullable|string',
                'equipment.*.subEquipment.*.income' => 'nullable|numeric',
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
                    'contract',
                    'return_reason',
                    'active',
                ]),
                ['full_income' => 0]
            ));

            if ($isChangingToInactive) {
                Notification::create([
                    'type' => 'Пользователь ' . User::find($user_id)->name . ' закрыл аренду №' . $service->id,
                    'data' => ['service_id' => $service->id],
                    'user_id' => $user_id,
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


            $serviceContragent = ServiceContragent::firstOrCreate([
                'contragent_id' => $service->contragent_id,
            ]);
            
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
                        $store_price = 0;
                        $operation_price = 0;
                    }else {
                        $store_price = $equipmentPrice->store_price;
                        $operation_price = $equipmentPrice->operation_price;    
                    }

                    $operating = $equipmentData['operating'] ?? 0;
                    $store = $equipmentData['store'] ?? 0;

                    $existingServiceEquipment = ServiceEquip::where('service_id', $service->id)
                        ->where('equipment_id', $equipment_id)
                        ->first();

                    $shouldUpdateEquipment = !$existingServiceEquipment ||
                        $existingServiceEquipment->return_date != ($equipmentData['return_date'] ?? null) ||
                        $existingServiceEquipment->return_reason != ($equipmentData['return_reason'] ?? null);

                    if ($existingServiceEquipment) {
                        if (
                            $existingServiceEquipment->store != $store ||
                            $existingServiceEquipment->operating != $operating
                        ) {
                            $shouldUpdateEquipment = true;
                            $income = ($store * $store_price) + ($operating * $operation_price);
                        } else {
                            $income = $existingServiceEquipment->income;
                        }
                    } else {
                        $income = ($store * $store_price) + ($operating * $operation_price);
                    }

                    if ($shouldUpdateEquipment) {
                        $existingServiceEquipment = ServiceEquip::updateOrCreate(
                            [
                                'service_id' => $service->id,
                                'equipment_id' => $equipment_id,
                            ],
                            [
                                'commentary' => $equipmentData['commentary'] ?? "",
                                'period_start_date' => $equipmentData['period_start_date'] ?? null,
                                'return_date' => $equipmentData['return_date'] ?? null,
                                'period_end_date' => $equipmentData['period_end_date'] ?? null,
                                'store' => $store,
                                'return_reason' => $equipmentData['return_reason'] ?? null,
                                'shipping_date' => $equipmentData['shipping_date'] ?? null,
                                'operating' => $operating,
                                'income' => $income,
                            ]
                        );
                    }

                    $fullIncome += $income;
                    if (isset($equipmentData['subEquipment']) && is_array($equipmentData['subEquipment'])) {
                        foreach ($equipmentData['subEquipment'] as $subEquipmentData) {
                            try {
                                $subEquipment_id = $subEquipmentData['subequipment_id'] ?? null;
                                if (!$subEquipment_id) {
                                    continue;
                                }
                                $subEquipment = Equipment::find($subEquipment_id);
                                if (!$subEquipment) {
                                    continue;
                                }
                                $subOperating = $subEquipmentData['operating'] ?? 0;
                                $subStore = $subEquipmentData['store'] ?? 0;
                                $return_reason = $subEquipmentData['return_reason'];
                                $return_date = $subEquipmentData['return_date'];

                                $existingServiceSub = ServiceSub::where('service_id', $service->id)
                                    ->where('subequipment_id', $subEquipment_id)
                                    ->where('service_equipment_id', $existingServiceEquipment->id)
                                    ->first();

                                $shouldUpdateSub = !$existingServiceSub;



                                $subCategory = $subEquipment->category_id;
                                $subSize = $subEquipment->size_id;
                        
                                $subEquipmentPrice = EquipmentPrice::where('category_id', $subCategory)
                                    ->where('size_id', $subSize)
                                    ->where('archive', false)
                                    ->first();
                                
                                if (!$subEquipmentPrice) {
                                    $subStorePrice = 0;
                                    $subOperationPrice = 0;
    
                                }else {
                                    $subStorePrice = $subEquipmentPrice->store_price;
                                    $subOperationPrice = $subEquipmentPrice->operation_price;
    
                                }

                                
                        
                        

                                if ($existingServiceSub) {
                                    if (
                                        $existingServiceSub->store != $subStore ||
                                        $existingServiceSub->operating != $subOperating ||
                                        $existingServiceSub->return_reason != $return_reason ||
                                        $existingServiceSub->return_date != $return_date 
                                    ) {
                                        $shouldUpdateSub = true;
                                        $subincome = ($subStore * $subStorePrice) + ($subOperating * $subOperationPrice);
                                    } else {
                                        $subincome = $existingServiceSub->income;
                                    }
                                } else {
                                    $subincome = ($subStore * $subStorePrice) + ($subOperating * $subOperationPrice);
                                }
                                \Log::info('Updating ServiceSub', [
                                    'return_date' => $subEquipmentData['return_date'] ?? 'Not Passed',
                                    'return_reason' => $subEquipmentData['return_reason'] ?? 'Not Passed',
                                ]);
                                if ($shouldUpdateSub) {
                                    $serviceSub = ServiceSub::updateOrCreate(
                                        [
                                            'service_id' => $service->id,
                                            'subequipment_id' => $subEquipment_id,
                                            'service_equipment_id' => $existingServiceEquipment->id,
                                        ],
                                        [
                                            'shipping_date' => $subEquipmentData['shipping_date'] ?? null,
                                            'period_start_date' => $subEquipmentData['period_start_date'] ?? null,
                                            'period_end_date' => $subEquipmentData['period_end_date'] ?? null,
                                            'operating' => $subOperating,
                                            'store' => $subStore,
                                            'income' => $subincome,
                                            'return_date' => $subEquipmentData['return_date'] ?? null,
                                            'return_reason' => $subEquipmentData['return_reason'] ?? null
                                        ]
                                    );


                                    
           
                                }

                                $fullIncome += $subincome;
                            } catch (\Exception $e) {
                                return back()->with('error', 'Ошибка при обработке subEquipment: ' . $e->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return back()->with('error', 'Ошибка при обработке оборудования: ' . $e->getMessage());
                }
            }
            $notification = Notification::create([
                'type' => 'Обновлена аренда',
                'data' => ['service_number' => $service->service_number],
                'created_by' => $user_id,
            ]);

            $this->sendNotificationToUsers($notification, $user_id);

            $service->update(['full_income' => $fullIncome]);
            return back()->with('message', 'Аренда успешно обновлена');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $subservice = ServiceSub::where('service_id', $id);
            $user_id = Auth::id();
            $service->delete();
            $subservice->delete();
            $notification = Notification::create([
                'type' => 'Удалена аренда',
                'data' => ['service_number' => $service->service_number],
                'created_by' => $user_id,
            ]);
            $this->sendNotificationToUsers($notification, $user_id);
            return back()->with('message', 'Аренда удалена');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

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
                    'shipping_date' => $shipping_date,
                ]);
            } else {
                ServiceContragent::create([
                    'contragent_id' => $contragent_id,
                    'commentary' => $commentary,
                    'shipping_date' => $shipping_date,
                ]);
            }

            return back()->with('message', 'Данные обновлены');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    private function sendNotificationToUsers($notification)
    {
        $userIds = User::pluck('id')->toArray(); // Include all users
    
        foreach ($userIds as $userId) {
            // 🔹 Создаём запись о непрочитанном уведомлении
            NotificationRead::create([
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'read_at' => null,
            ]);
    
            // 🔹 Подсчитываем количество непрочитанных уведомлений
            $unreadCount = NotificationRead::where('user_id', $userId)
                ->whereNull('read_at')
                ->count();
    
            event(new NotificationCountUpdated($unreadCount, $userId));
        }
    }


}
