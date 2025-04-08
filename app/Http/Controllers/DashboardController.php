<?php
namespace App\Http\Controllers;

use App\Models\Contragents;
use App\Models\ContrDocuments;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentLocation;
use App\Models\EquipmentRepair;
use App\Models\EquipmentSize;
use App\Models\EquipmentTest;
use App\Models\Sale;
use App\Models\Service;
use App\Models\ServiceEquip;
use App\Models\ServiceSub;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {

        return redirect('rent');
        $searchTerm              = $request->query('search');
        $perPage                 = $request->query('perPage');
        $categoryId              = $request->query('category_id', 1);
        $sizeId                  = $request->query('size_id');
        $equipment_sizes         = EquipmentSize::where('category_id', $categoryId)->get();
        $query                   = Equipment::query();
        $equipment_categories    = EquipmentCategories::all();
        $equipment_location      = EquipmentLocation::all();
        $equipment_on_rent_count = Equipment::whereHas('serviceEquipment', function ($query) {
            $query->where('active', true);
        })->count();
        $activeEquipment = Equipment::whereHas('serviceEquipment', function ($query) {
            $query->where('active', true);
        })->paginate($perPage);
        $contragents_count = Contragents::count();

        $categoryId                  = $request->query('category_id', 1);
        $sizeId                      = $request->query('size_id');
        $equipment_sizes             = EquipmentSize::where('category_id', $categoryId)->get();
        $locationId                  = $request->query('location_id');
        $rentActive                  = $request->query('isRentActive');
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount                               = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount                          = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        $location_counts = [];
        foreach ($equipment_location as $location) {
            $locationIdCount = $location->id;
            if ($categoryId > 0) {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->where('category_id', $categoryId)->count();
            } else {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->count();
            }
        }

        if ($rentActive) {
            $query->whereHas('serviceEquipment', function ($query) {
                $query->where('active', true);
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        if ($locationId > 0) {
            $query->where('location_id', $locationId);
        }

        $equipment = $query->paginate($perPage);

        return Inertia::render("Dashboard/Dashboard", [
            'equipment'                   => $equipment,
            'equipment_categories'        => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts'      => $equipment_sizes_counts,
            'equipment_sizes'             => $equipment_sizes,
            'equipment_location'          => $equipment_location,
            'selectedCategory'            => $categoryId,
            'location_counts'             => $location_counts,
            'equipment_on_rent_count'     => $equipment_on_rent_count,
            'activeEquipment'             => $activeEquipment,
            'contragents_count'           => $contragents_count,
        ]);
    }

    public function rent(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();

        $contragents = Contragents::whereHas('services', function ($query) {
            $query->where('active', 1);
        })->get();
        $mobileAgents = Contragents::whereHas('services', function ($query) {
            $query->where('active', 1);
        })->get(['name', 'id'])->map(function ($agent) {
            return [
                'name' => $agent->name,
                'id'   => $agent->id,
            ];
        })->values();
        $currentPage                     = (int) $request->input('page', 1);
        $category_id                     = $request->input('category_id');
        $size_id                         = $request->input('size_id');
        $perPage                         = $request->input('perPage', 10);
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts     = [];
        $equipment_sizes                 = EquipmentSize::where('category_id', $category_id)->get();

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;

            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)
                ->whereHas('services', function ($query) {
                    $query->where('active', 1);
                })
                ->count();

            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $equipment_sizes_counts_all = 0;
        $equipment_sizes_counts     = [];

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;

            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)
                ->whereHas('services', function ($query) {
                    $query->where('active', 1);
                })
                ->count();

            $equipment_sizes_counts_all += $equipment_sizes_counts[$sizeIDForCount];
        }

        $all_equipment_sizes_counts = Equipment::selectRaw('size_id, category_id, COUNT(*) as total')
            ->groupBy('size_id', 'category_id')
            ->get()
            ->groupBy('category_id')
            ->map(function ($sizes) {
                return $sizes->pluck('total', 'size_id');
            });

        $all_equipment_categories_counts = Equipment::selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $total_equipment_categories = $all_equipment_categories_counts->sum();

        $all_equipment_sizes_counts = Equipment::selectRaw('size_id, category_id, COUNT(*) as total')
            ->groupBy('size_id', 'category_id')
            ->get()
            ->groupBy('category_id')
            ->map(function ($sizes) {
                return $sizes->pluck('total', 'size_id');
            });

        $total_equipment_sizes = $all_equipment_sizes_counts->flatten()->sum();

        $rented_services = Service::where('active', 1)
            ->with([
                'mainServices' => function ($query) {
                    $query->with(['equipment', 'serviceSubs.equipment']);
                },
            ])
            ->get();

        $rented_services_grouped = $rented_services->groupBy('contragent_id')
            ->map(function ($services, $contragentId) {
                $contragent     = Contragents::find($contragentId);
                $contragentName = $contragent ? $contragent->name : 'Unknown';

                $totalIncome = 0;

                $groupedServices = $services->map(function ($service) use (&$totalIncome) {
                    $serviceIncome = 0;

                    $serviceEquipment = $service->mainServices->map(function ($serviceEquip) use (&$serviceIncome) {
                        $mainIncome = $serviceEquip->income ?? 0;

                        $subEquipmentsIncome = $serviceEquip->serviceSubs->sum(function ($serviceSub) {
                            return $serviceSub->income ?? 0;
                        });

                        $totalEquipIncome = $mainIncome + $subEquipmentsIncome;
                        $serviceIncome += $totalEquipIncome;

                        return [
                            'id'                   => $serviceEquip->id,
                            'shipping_date'        => $serviceEquip->shipping_date,
                            'commentary'           => $serviceEquip->commentary,
                            'income'               => $mainIncome,
                            'equipment'            => [
                                'id'          => $serviceEquip->equipment->id ?? null,
                                'category_id' => $serviceEquip->equipment->category_id ?? null,
                                'category'    => $serviceEquip->equipment->category->name ?? null,
                                'size_id'     => $serviceEquip->equipment->size_id ?? null,
                                'size'        => $serviceEquip->equipment->size->name ?? null,
                                'series'      => $serviceEquip->equipment->series ?? null,
                            ],
                            'subservice_equipment' => $serviceEquip->serviceSubs->map(function ($serviceSub) {
                                return [
                                    'id'            => $serviceSub->id,
                                    'commentary'    => $serviceSub->commentary,
                                    'shipping_date' => $serviceSub->shipping_date,
                                    'income'        => $serviceSub->income ?? 0,
                                    'equipment'     => [
                                        'id'          => $serviceSub->equipment->id ?? null,
                                        'category_id' => $serviceSub->equipment->category_id ?? null,
                                        'category'    => $serviceSub->equipment->category->name ?? null,
                                        'size_id'     => $serviceSub->equipment->size_id ?? null,
                                        'size'        => $serviceSub->equipment->size->name ?? null,
                                        'series'      => $serviceSub->equipment->series ?? null,
                                    ],
                                ];
                            })->values()->toArray(),
                        ];
                    })->values()->toArray();

                    $totalIncome += $serviceIncome;

                    return [
                        'service_id'           => $service->id,
                        'service_date'         => $service->service_date,
                        'commentary'           => $service->commentary,
                        'service_total_income' => $serviceIncome,
                        'service_equipment'    => $serviceEquipment,
                    ];
                })->values()->toArray();

                return [
                    'contragent_id'   => $contragentId,
                    'contragent_name' => $contragentName,
                    'services'        => $groupedServices,
                    'total_income'    => $totalIncome,
                ];
            })->values();

        $totalContragents     = $rented_services_grouped->count();
        $paginatedContragents = $rented_services_grouped->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated_result = new LengthAwarePaginator(
            $paginatedContragents,
            $totalContragents,
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        // Generate pagination links
        $pagination_links = [
            'first_page_url'   => $paginated_result->url(1),
            'last_page_url'    => $paginated_result->url($paginated_result->lastPage()),
            'next_page_url'    => $paginated_result->nextPageUrl(),
            'prev_page_url'    => $paginated_result->previousPageUrl(),
            'current_page_url' => $paginated_result->url($paginated_result->currentPage()),
            'pages'            => collect(range(1, $paginated_result->lastPage()))->map(function ($page) use ($paginated_result) {
                return [
                    'page_number' => $page,
                    'url'         => $paginated_result->url($page),
                ];
            }),
        ];

        return Inertia::render('Dashboard/Rent', [
            'total_equipment_categories'      => $total_equipment_categories,
            'total_equipment_sizes'           => $total_equipment_sizes,
            'equipment_sizes_counts'          => $equipment_sizes_counts,
            'equipment_sizes_counts_all'      => $equipment_sizes_counts_all,
            'equipment_sizes'                 => $equipment_sizes,
            'equipment_categories'            => $equipment_categories,
            'equipment_categories_counts'     => $equipment_categories_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents'                     => $contragents,
            'rented_services_grouped'         => $paginated_result,
            'mobileAgents'                    => $mobileAgents,
            'paginated'                       => [
                'data'       => $paginated_result->items(),
                'pagination' => [
                    'total'        => $paginated_result->total(),
                    'per_page'     => $paginated_result->perPage(),
                    'current_page' => $paginated_result->currentPage(),
                    'last_page'    => $paginated_result->lastPage(),
                    'from'         => $paginated_result->firstItem(),
                    'to'           => $paginated_result->lastItem(),
                    'links'        => $pagination_links,
                ],
            ],
        ]);
    }
    public function free(Request $request)
    {
        $equipment_categories            = EquipmentCategories::all();
        $contragents                     = Contragents::all();
        $equipment_location              = EquipmentLocation::where('id', '!=', '-1')->get();
        $category_id                     = $request->input('category_id');
        $size_id                         = $request->input('size_id');
        $perPage                         = $request->input('perPage', 10);
        $location_id                     = $request->input('location_id');
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts     = [];
        $equipment_sizes_counts_all      = 0;
        $equipment_sizes_counts          = [];
        $equipment_sizes                 = EquipmentSize::where('category_id', $category_id)->get();
        $manufacturers                   = Equipment::distinct()->pluck('manufactor')->map(function ($manufactor) {
            return [
                'title' => $manufactor,
                'value' => $manufactor,
            ];
        })->toArray();

        $statusesList = [
            'new'          => 'Новое',
            'good'         => 'Хорошее',
            'satisfactory' => 'Удовлетворительно',
            'bad'          => 'Плохое',
            'off'          => 'Списано',
            'unknown'      => 'Неизвестный статус',
        ];

        $statusesArray = Equipment::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->map(function ($status) use ($statusesList) {
                return [
                    'title' => $statusesList[$status] ?? ucfirst($status),
                    'value' => $status,
                ];
            })->values()->toArray();

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;

            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->whereDoesntHave('repairs')->whereDoesntHave('tests')->whereDoesntHave('activeServices')->count();

            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;

            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->whereDoesntHave('repairs')->whereDoesntHave('tests')->whereDoesntHave('activeServices')->count();

            $equipment_sizes_counts_all += $equipment_sizes_counts[$sizeIDForCount];
        }

        $location_counts = [];
        $location_counts = Equipment::whereDoesntHave('repairs')
            ->whereDoesntHave('tests')
            ->whereDoesntHave('activeServices')
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($size_id, function ($query, $size_id) {
                return $query->where('size_id', $size_id);
            })
            ->select('location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('location_id')
            ->pluck('count', 'location_id')
            ->toArray();

        $equipment = Equipment::whereDoesntHave('repairs')
            ->whereDoesntHave('tests')
            ->with('directory.files')
            ->whereDoesntHave('activeServices')
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($size_id, function ($query, $size_id) {
                return $query->where('size_id', $size_id);
            })
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            ->paginate($perPage);

        return Inertia::render('Dashboard/Free', [
            'equipment'                       => $equipment,
            'equipment_categories'            => $equipment_categories,
            'equipment_categories_counts'     => $equipment_categories_counts,
            'equipment_sizes_counts'          => $equipment_sizes_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents'                     => $contragents,
            'equipment_sizes'                 => $equipment_sizes,
            'equipment_location'              => $equipment_location,
            'location_counts'                 => $location_counts,
            'manufacturers'                   => $manufacturers,
            'statusesArray'                   => $statusesArray,

        ]);
    }
    public function serviced(Request $request)
    {
        $equipment_categories            = EquipmentCategories::all();
        $contragents                     = Contragents::all();
        $equipment_location              = EquipmentLocation::where('id', '!=', '-1')->get();
        $category_id                     = $request->input('category_id');
        $size_id                         = $request->input('size_id');
        $perPage                         = $request->input('perPage', 10);
        $location_id                     = $request->input('location_id');
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts     = [];
        $equipment_sizes                 = EquipmentSize::where('category_id', $category_id)->get();
        $manufacturers                   = Equipment::distinct()->pluck('manufactor')->map(function ($manufactor) {
            return [
                'title' => $manufactor,
                'value' => $manufactor,
            ];
        })->toArray();

        $statusesList = [
            'new'          => 'Новое',
            'good'         => 'Хорошее',
            'satisfactory' => 'Удовлетворительно',
            'bad'          => 'Плохое',
            'off'          => 'Списано',
            'unknown'      => 'Неизвестный статус',
        ];

        $statusesArray = Equipment::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->map(function ($status) use ($statusesList) {
                return [
                    'title' => $statusesList[$status] ?? ucfirst($status),
                    'value' => $status,
                ];
            })->values()->toArray();
        $equipment = Equipment::whereHas('repairs', function ($query) {
            $query->whereNull('repair_date');
        });

        if ($category_id) {
            $equipment->where('category_id', $category_id);
        }

        if ($size_id) {
            $equipment->where('size_id', $size_id);
        }

        if ($location_id) {
            $equipment->where('location_id', $location_id);
        }

        $equipment = $equipment->with('directory.files')->paginate($perPage);

        $equipment_categories_counts = EquipmentRepair::groupBy('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->pluck('count', 'category_id');

        $equipment_sizes_counts = EquipmentRepair::groupBy('size_id')
            ->selectRaw('size_id, COUNT(*) as count')
            ->pluck('count', 'size_id');

        // Фильтруем так же, как и в $equipment
        $equipment_locations_counts = Equipment::whereHas('repairs', function ($query) {
            $query->whereNull('repair_date');
        })
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($size_id, function ($query, $size_id) {
                return $query->where('size_id', $size_id);
            })
            ->select('location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('location_id')
            ->pluck('count', 'location_id')
            ->toArray();

        // Заполняем локации с нулями
        foreach ($equipment_location as $loc) {
            if (! isset($equipment_locations_counts[$loc->id])) {
                $equipment_locations_counts[$loc->id] = 0;
            }
        }

        return Inertia::render('Dashboard/Serviced', [
            'equipment'                       => $equipment,
            'equipment_categories'            => $equipment_categories,
            'equipment_categories_counts'     => $equipment_categories_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents'                     => $contragents,
            'equipment_sizes'                 => $equipment_sizes,
            'equipment_location'              => $equipment_location,
            'equipment_sizes_counts'          => $equipment_sizes_counts,
            'equipment_locations_counts'      => $equipment_locations_counts,
            'manufacturers'                   => $manufacturers,
            'statusesArray'                   => $statusesArray,

        ]);
    }
    public function analysis()
    {
        $contragents_count             = Contragents::where('customer', true)->count();
        $contragents_inactive          = Contragents::where('status', false)->count();
        $recent_contragents            = Contragents::where('created_at', '>=', now()->subMonths(3))->where('customer', true)->get();
        $recent_contragents_count      = $recent_contragents->count();
        $recent_contragents_percentage = $contragents_count > 0 ? ($recent_contragents_count / $contragents_count) * 100 : 0;

        $contragents_with_active_services_count = Contragents::whereHas('services', function ($query) {
            $query->where('active', true);
        })->count();
        $active_contragents_percentage = $contragents_count > 0 ? ($contragents_with_active_services_count / $contragents_count) * 100 : 0;

        $equipment_count        = Equipment::count();
        $recent_equipment       = Equipment::where('created_at', '>=', now()->subMonths(3))->get();
        $recent_equipment_count = $recent_equipment->count();

        $serviceSubCount = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', 1);
        })
            ->count();

        $serviceEquipCount = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })
            ->count();

        $equipment_in_active_services_count    = Equipment::whereHas('activeServices')->count();
        $equipment_in_active_subservices_count = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', true);
        })->count();

        $equipment_count_active_sum         = $serviceEquipCount + $serviceSubCount;
        $equipment_count_active_sum_percent = $equipment_count_active_sum > 0 ? ($equipment_count_active_sum / $equipment_count) * 100 : 0;

        $equipment_on_repair = EquipmentRepair::count();
        $equipment_on_test   = EquipmentTest::count();
        $unavailable         = $equipment_count_active_sum + $equipment_on_repair + $equipment_on_test;
        // dd($equipment_count,$equipment_in_active_services_count,$equipment_on_repair, $equipment_on_test);

        $on_store             = $equipment_count - $unavailable;
        $equipment_categories = EquipmentCategories::all();
        $categoryData         = EquipmentCategories::select(
            'equipment_categories.id',
            'equipment_categories.name',
            DB::raw('COUNT(DISTINCT equipment.id) as total_equipment'),
            DB::raw('SUM(CASE WHEN service_equipment.id IS NOT NULL THEN 1 ELSE 0 END) + SUM(CASE WHEN service_subequipment.id IS NOT NULL THEN 1 ELSE 0 END) as total_service_count')
        )
            ->leftJoin('equipment', 'equipment.category_id', '=', 'equipment_categories.id')
            ->leftJoin('service_equipment', function ($join) {
                $join->on('equipment.id', '=', 'service_equipment.equipment_id');
            })
            ->leftJoin('service_subequipment', function ($join) {
                $join->on('equipment.id', '=', 'service_subequipment.subequipment_id');
            })
            ->groupBy('equipment_categories.id', 'equipment_categories.name')
            ->get();

        $firstFour = $categoryData->take(4);
        $remaining = $categoryData->slice(4);

        $serviceIncome = Service::select('contragent_id', DB::raw('SUM(full_income) as total_income'))
            ->groupBy('contragent_id');

        $saleIncome = Sale::select('contragent_id', DB::raw('SUM(price) as total_income'))
            ->groupBy('contragent_id');

        $combinedIncome = $serviceIncome->unionAll($saleIncome);

        $finalIncome = DB::table(DB::raw("({$combinedIncome->toSql()}) as incomes"))
            ->mergeBindings($combinedIncome->getQuery())
            ->select('contragent_id', DB::raw('SUM(total_income) as full_income'))
            ->groupBy('contragent_id')
            ->get();

        $totalIncomeSum = $finalIncome->sum('full_income');

        $contragentincome = $finalIncome->map(function ($item) use ($totalIncomeSum) {
            $contragent = Contragents::find($item->contragent_id);
            return [
                'id'             => $item->contragent_id,
                'fullincome'     => $item->full_income,
                'contragentname' => $contragent->name ?? 'Unknown',
                'percent'        => $totalIncomeSum > 0 ? round(($item->full_income / $totalIncomeSum) * 100, 2) : 0,
            ];
        })->sortByDesc('fullincome')->values();

        $services = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })
            ->with('equipment.category', 'serviceSubs.equipment.category')
            ->get();

        $categoryDataIncome = $services->groupBy('equipment.category.name')->map(function ($services, $categoryName) {
            return [
                'category'    => $categoryName,
                'full_income' => $services->sum('income'),
            ];
        });

        // Calculate the total full_income
        $totalIncome = $categoryDataIncome->sum('full_income');

        // Add percentage calculation
        $categoryDataIncome = $categoryDataIncome->map(function ($data) use ($totalIncome) {
            return array_merge($data, [
                'percentage' => $totalIncome > 0 ? round(($data['full_income'] / $totalIncome) * 100) : 0,
            ]);
        });

        $subequipmentIncome = $services->pluck('serviceSubs')->flatten()->groupBy('equipment.category.name')->map(function ($subs, $categoryName) {
            return $subs->sum('income');
        });

        $categoryDataIncomeArray = $categoryDataIncome->toArray();

        foreach ($subequipmentIncome as $categoryName => $subIncome) {
            if (isset($categoryDataIncomeArray[$categoryName])) {
                $categoryDataIncomeArray[$categoryName]['full_income'] += $subIncome;
            } else {
                $categoryDataIncomeArray[$categoryName] = [
                    'category'    => $categoryName,
                    'full_income' => $subIncome,
                ];
            }
        }

        $categoryDataIncome = collect($categoryDataIncomeArray);
        $categories         = EquipmentCategories::with([
            'equipment' => function ($query) {
                $query->select('id', 'size_id', 'category_id', 'status')
                    ->with(['size:id,name']);
            },
            'sizes:id,name,category_id',
        ])->get()->map(function ($category) {
            $allSizes = $category->sizes->keyBy('id');

            $sizeData = $category->equipment->groupBy('size_id')->map(function ($equipmentGroup, $sizeId) use ($allSizes) {
                $sizeName = $allSizes[$sizeId]->name ?? 'Unknown';

                $totalQuantity = $equipmentGroup->count();
                $equipmentIds  = $equipmentGroup->pluck('id');

                $serviceEquipCount = ServiceEquip::whereIn('equipment_id', $equipmentIds)
                    ->whereHas('services', function ($query) {
                        $query->where('active', 1);
                    })
                    ->count();

                $serviceSubCount = ServiceSub::whereIn('subequipment_id', $equipmentIds)
                    ->whereHas('service', function ($query) {
                        $query->where('active', 1);
                    })
                    ->count();

                $onTestCount = EquipmentTest::whereIn('equipment_id', $equipmentIds)
                    ->whereNotNull('test_date')
                    ->count();
                $onRepairCount = EquipmentTest::whereIn('equipment_id', $equipmentIds)
                    ->whereNotNull('repair_date')
                    ->count();

                $numberOfSizeOnRent = $serviceEquipCount + $serviceSubCount;
                $equipmentLeft      = $totalQuantity + $onRepairCount;
                return [
                    'size'               => $sizeName,
                    'totalQuantity'      => $totalQuantity,
                    'numberOfSizeOnRent' => $numberOfSizeOnRent,
                    'equipmentLeft'      => max($equipmentLeft, 0),
                    'percent'            => $equipmentLeft * ($numberOfSizeOnRent / 100),
                ];
            });

            foreach ($allSizes as $sizeId => $size) {
                if (! $sizeData->has($sizeId)) {
                    $sizeData[$sizeId] = [
                        'size'               => $size->name,
                        'totalQuantity'      => 0,
                        'numberOfSizeOnRent' => 0,
                        'equipmentLeft'      => 0,
                    ];
                }
            }

            return $sizeData->values()->toArray();
        })->toArray();

        $categoriesForPecent = EquipmentCategories::with([
            'equipment' => function ($query) {
                $query->select('id', 'category_id');
            },
        ])->get();

        $categoryPercentages = $categoriesForPecent->map(function ($category) {
            $totalEquipment = $category->equipment->count();

            $totalOnRent = ServiceEquip::whereIn('equipment_id', $category->equipment->pluck('id'))
                ->whereHas('services', function ($query) {
                    $query->where('active', 1);
                })
                ->count()
             +
            ServiceSub::whereIn('subequipment_id', $category->equipment->pluck('id'))
                ->whereHas('service', function ($query) {
                    $query->where('active', 1);
                })
                ->count();

            $percent = ($totalEquipment > 0) ? round(($totalOnRent / $totalEquipment) * 100, 2) : 0;

            return [
                'category' => $category->name,
                'percent'  => $percent,
            ];
        });

        return Inertia::render('Dashboard/Analysis', [
            'contragents_count'                      => $contragents_count,
            'contragents_inactive'                   => $contragents_inactive,
            'recent_contragents_count'               => $recent_contragents_count,
            'recent_contragents_percentage'          => $recent_contragents_percentage,
            'contragents_with_active_services_count' => $contragents_with_active_services_count,
            'active_contragents_percentage'          => $active_contragents_percentage,
            'equipment_count'                        => $equipment_count,
            'recent_equipment_count'                 => $recent_equipment_count,
            'equipment_in_active_services_count'     => $equipment_count_active_sum,
            'equipment_count_active_sum_percent'     => $equipment_count_active_sum_percent,
            'on_store'                               => $on_store,
            'unavailable'                            => $unavailable,
            'categoryData'                           => $categoryData,
            'contragentincome'                       => $contragentincome,
            'equipment_categories'                   => $equipment_categories,
            'categoryDataIncome'                     => $categoryDataIncome,
            'categoriesProgress'                     => $categories,
            'categoryPercentages'                    => $categoryPercentages,
            'firstFour'                              => $firstFour,
            'remaining'                              => $remaining,
        ]);
    }
    public function commercial()
    {

      

        $contragents = Contragents::with(['documents'])->get();

        $translations = [
            'commercials_incoming'  => 'Входящий',
            'commercials_outcoming' => 'Исходящий',
            'commercials_tender'    => 'Тендер',
        ];
        $documents = ContrDocuments::whereIn('type', ['commercials_incoming', 'commercials_outcoming', 'commercials_tender'])
        ->with('contragent', 'user')
        ->get()
        ->map(function ($item) use ($translations) {
            $item->translatedType = $translations[$item->type] ?? 'Unknown Type';
            return $item;
        })
        ->groupBy('contragent_id')
        ->mapWithKeys(function ($items, $contragentId) {
            $itemsArray = $items->toArray();
            if (!empty($itemsArray)) {
                // Extract the first item
                $mainItem = $itemsArray[0];
    
                // Move other items inside `nestedItems`
                $mainItem['nestedItems'] = array_slice($itemsArray, 1);
    
                // Return as an object, not inside an array
                return [$contragentId => $mainItem];
            }
            return [$contragentId => null]; // Handle empty cases
        });

        $KPcount = ContrDocuments::whereIn('type', ['commercials_incoming', 'commercials_outcoming', 'commercials_tender'])->count();

        $stats = ContrDocuments::whereIn('type', ['commercials_incoming', 'commercials_outcoming', 'commercials_tender'])
            ->selectRaw("
        SUM(CASE WHEN type = 'commercials_incoming' THEN 1 ELSE 0 END) AS incomingCount,
        SUM(CASE WHEN type = 'commercials_outcoming' THEN 1 ELSE 0 END) AS outcomingCount,
        SUM(CASE WHEN type = 'commercials_tender' THEN 1 ELSE 0 END) AS tenderCount,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS dealsCount,
        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS dialogueCount,
        SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS noDealCount
    ")
            ->first();

        $incomingCount  = $stats->incomingCount;
        $outcomingCount = $stats->outcomingCount;
        $tenderCount    = $stats->tenderCount;
        $dealsCount     = $stats->dealsCount;
        $dialogueCount  = $stats->dialogueCount;
        $noDealCount    = $stats->noDealCount;

        $incomingPercent  = $KPcount > 0 ? number_format(($incomingCount / $KPcount) * 100, 1) : 0;
        $outcomingPercent = $KPcount > 0 ? number_format(($outcomingCount / $KPcount) * 100, 1) : 0;
        $tenderPercent    = $KPcount > 0 ? number_format(($tenderCount / $KPcount) * 100, 1) : 0;
        $dialoguePercent  = $KPcount > 0 ? number_format(($dialogueCount / $KPcount) * 100, 1) : 0;
        $percentDeals     = $KPcount > 0 ? number_format(($dealsCount / $KPcount) * 100, 1) : 0;

        $documentCount = $documents->flatten()->count();
        return Inertia::render('Dashboard/Commercial', [
            'documents'        => $documents,
            'contragents'      => $contragents,
            'KPcount'          => $KPcount,
            'dealsCount'       => $dealsCount,
            'dialogueCount'    => $dialogueCount,
            'noDealCount'      => $noDealCount,
            'percentDeals'     => $percentDeals,
            'documentCount'    => $documentCount,
            'dialoguePercent'  => $dialoguePercent,
            'incomingPercent'  => $incomingPercent,
            'outcomingPercent' => $outcomingPercent,
            'tenderPercent'    => $tenderPercent,
            'incomingCount'    => $incomingCount,
            'outcomingCount'   => $outcomingCount,
            'tenderCount'      => $tenderCount,
        ]);
    }
}
