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
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage');
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $query = Equipment::query();
        $equipment_categories = EquipmentCategories::all();
        $equipment_location = EquipmentLocation::all();
        $equipment_on_rent_count = Equipment::whereHas('serviceEquipment', function ($query) {
            $query->where('active', true);
        })->count();
        $activeEquipment = Equipment::whereHas('serviceEquipment', function ($query) {
            $query->where('active', true);
        })->paginate($perPage);
        $contragents_count = Contragents::count();


        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $locationId = $request->query('location_id');
        $rentActive = $request->query('isRentActive');
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
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
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'selectedCategory' => $categoryId,
            'location_counts' => $location_counts,
            'equipment_on_rent_count' => $equipment_on_rent_count,
            'activeEquipment' => $activeEquipment,
            'contragents_count' => $contragents_count
        ]);
    }

    public function rent(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();

        $equipment_categories_counts_all = 0;
        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }


        $contragents = Contragents::all();

        $category_id = $request->input('category_id');
        $size_id = $request->input('size_id');
        $perPage = $request->input('perPage', 10);


        $equipment_sizes = EquipmentSize::where('category_id', $category_id)->get();

        $rented_equipment_query = Equipment::whereHas('serviceEquipment.services', function ($query) {
            $query->where('active', 1);
        })->with([
                    'serviceEquipment.services' => function ($query) {
                        $query->where('active', 1);
                    }
                ])->whereDoesntHave('serviceEquipment.services', function ($query) {
                    $query->where('active', 0);
                });
        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)
                ->where('category_id', $category_id)
                ->count();
        }
        if ($category_id) {
            $rented_equipment_query->where('category_id', $category_id);
        }

        if ($size_id) {
            $rented_equipment_query->where('size_id', $size_id);
        }

        $rented_equipment = $rented_equipment_query->paginate($perPage);
        return Inertia::render('Dashboard/Rent', ['equipment_sizes_counts' => $equipment_sizes_counts, 'equipment_sizes' => $equipment_sizes, 'equipment_categories' => $equipment_categories, 'equipment_categories_counts' => $equipment_categories_counts, 'equipment_categories_counts_all' => $equipment_categories_counts_all, 'contragents' => $contragents, 'rented_equipment' => $rented_equipment]);
    }
    public function free(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();
        $contragents = Contragents::all();
        $equipment_location = EquipmentLocation::all();
        $category_id = $request->input('category_id');
        $size_id = $request->input('size_id');
        $perPage = $request->input('perPage', 10);
        $location_id = $request->input('location_id');
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $equipment_sizes = EquipmentSize::where('category_id', $category_id)->get();
        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)
                ->where('category_id', $category_id)
                ->count();
        }

        $equipment = Equipment::where('category_id', $category_id)
            ->where('size_id', $size_id)
            ->when($location_id != 0, function ($query) use ($location_id) {
                $query->where('location_id', $location_id);
            })
            ->whereNotExists(function ($subQuery) use ($category_id, $size_id, $location_id) {
                $subQuery->select(DB::raw(1))
                    ->from('equipment_repairs as er')
                    ->whereColumn('equipment.category_id', 'er.category_id')
                    ->whereColumn('equipment.size_id', 'er.size_id')
                    ->whereColumn('equipment.series', 'er.series')
                    ->where('er.category_id', $category_id)
                    ->where('er.size_id', $size_id)
                    ->whereNotNull('er.repair_date');

                if ($location_id != 0) {
                    $subQuery->where('er.location_id', $location_id);
                }
            })
            ->whereNotExists(function ($subQuery) use ($category_id, $size_id, $location_id) {
                $subQuery->select(DB::raw(1))
                    ->from('equipment_tests as et')
                    ->whereColumn('equipment.category_id', 'et.category_id')
                    ->whereColumn('equipment.size_id', 'et.size_id')
                    ->whereColumn('equipment.series', 'et.series')
                    ->where('et.category_id', $category_id)
                    ->where('et.size_id', $size_id)
                    ->whereNotNull('et.test_date');

                if ($location_id != 0) {
                    $subQuery->where('et.location_id', $location_id);
                }
            })
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('service_equipment as se')
                    ->join('services as s', 'se.service_id', '=', 's.id')
                    ->whereColumn('equipment.id', 'se.equipment_id')
                    ->where('s.active', 1)
                    ->whereNotNull('se.period_start_date')
                    ->where(function ($q) {
                        $q->whereNull('se.period_end_date')
                            ->orWhere('se.period_end_date', '>', now());
                    });
            })
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('service_subequipment as ss')
                    ->join('services as s', 'ss.service_id', '=', 's.id')
                    ->whereColumn('equipment.id', 'ss.subequipment_id')
                    ->where('s.active', 1)
                    ->whereNotNull('ss.period_start_date')
                    ->where(function ($q) {
                        $q->whereNull('ss.period_end_date')
                            ->orWhere('ss.period_end_date', '>', now());
                    });
            })
            ->distinct()
            ->paginate($perPage);


        return Inertia::render('Dashboard/Free', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents' => $contragents,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location
        ]);
    }
    public function serviced(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();
        $contragents = Contragents::all();
        $equipment_location = EquipmentLocation::all();
        $category_id = $request->input('category_id');
        $size_id = $request->input('size_id');
        $perPage = $request->input('perPage', 10);
        $location_id = $request->input('location_id');
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $equipment_sizes = EquipmentSize::where('category_id', $category_id)->get();
        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)
                ->where('category_id', $category_id)
                ->count();
        }

        $equipment = Equipment::where(function ($query) use ($category_id, $size_id, $location_id) {
            $query->whereExists(function ($subQuery) use ($category_id, $size_id, $location_id) {
                $subQuery->select(DB::raw(1))
                    ->from('equipment_repairs as er')
                    ->whereColumn('equipment.category_id', 'er.category_id')
                    ->whereColumn('equipment.size_id', 'er.size_id')
                    ->whereColumn('equipment.series', 'er.series')
                    ->where('er.category_id', $category_id)
                    ->where('er.size_id', $size_id);

                if ($location_id != 0) {
                    $subQuery->where('er.location_id', $location_id);
                }

                $subQuery->whereNotNull('er.repair_date'); // Ensure repair exists
            })
                ->orWhereExists(function ($subQuery) use ($category_id, $size_id, $location_id) {
                    $subQuery->select(DB::raw(1))
                        ->from('equipment_tests as et')
                        ->whereColumn('equipment.category_id', 'et.category_id')
                        ->whereColumn('equipment.size_id', 'et.size_id')
                        ->whereColumn('equipment.series', 'et.series')
                        ->where('et.category_id', $category_id)
                        ->where('et.size_id', $size_id);

                    if ($location_id != 0) {
                        $subQuery->where('et.location_id', $location_id);
                    }

                    $subQuery->whereNotNull('et.test_date'); // Ensure test exists
                });
        })
            ->distinct()
            ->paginate($perPage);



        return Inertia::render('Dashboard/Serviced', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents' => $contragents,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location
        ]);
    }
    public function analysis()
    {
        $contragents_count = Contragents::count();
        $contragents_inactive = Contragents::where('status', false)->count();
        $recent_contragents = Contragents::where('created_at', '>=', now()->subMonths(3))->get();
        $recent_contragents_count = $recent_contragents->count();
        $recent_contragents_percentage = $contragents_count > 0 ? ($recent_contragents_count / $contragents_count) * 100 : 0;

        $contragents_with_active_services_count = Contragents::whereHas('services', function ($query) {
            $query->where('active', true);
        })->count();
        $active_contragents_percentage = $contragents_count > 0 ? ($contragents_with_active_services_count / $contragents_count) * 100 : 0;

        $equipment_count = Equipment::count();
        $recent_equipment = Equipment::where('created_at', '>=', now()->subMonths(3))->get();
        $recent_equipment_count = $recent_equipment->count();

        $equipment_in_active_services_count = Service::where('active', true)->with('serviceEquipment')->count();
        $equipment_in_active_subservices_count = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', true);
        })->count();

        $equipment_count_active_sum = $equipment_in_active_services_count + $equipment_in_active_subservices_count;
        $equipment_count_active_sum_percent = $equipment_count_active_sum > 0 ? ($equipment_count_active_sum / $equipment_count) * 100 : 0;

        $equipment_on_repair = EquipmentRepair::count();
        $equipment_on_test = EquipmentTest::count();
        $unavailable = $equipment_in_active_services_count + $equipment_on_repair + $equipment_on_test;
        $on_store = $equipment_count - $unavailable;
        $equipment_categories = EquipmentCategories::all();
        $categoryData = EquipmentCategories::select(
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
                'id' => $item->contragent_id,
                'fullincome' => $item->full_income,
                'contragentname' => $contragent->name ?? 'Unknown',
                'percent' => $totalIncomeSum > 0 ? round(($item->full_income / $totalIncomeSum) * 100, 2) : 0,
            ];
        });

        $services = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })
            ->with('equipment.category', 'serviceSubs.equipment.category')
            ->get();

        $categoryDataIncome = $services->groupBy('equipment.category.name')->map(function ($services, $categoryName) {
            return [
                'category' => $categoryName,
                'full_income' => $services->sum('income'),
            ];
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
                    'category' => $categoryName,
                    'full_income' => $subIncome,
                ];
            }
        }

        $categoryDataIncome = collect($categoryDataIncomeArray);
        $categories = EquipmentCategories::with([
            'equipment' => function ($query) {
                $query->select('id', 'size_id', 'category_id', 'status')
                    ->with(['size:id,name']);
            },
            'sizes:id,name,category_id'
        ])->get()->map(function ($category) {
            $allSizes = $category->sizes->keyBy('id');

            $sizeData = $category->equipment->groupBy('size_id')->map(function ($equipmentGroup, $sizeId) use ($allSizes) {
                $sizeName = $allSizes[$sizeId]->name ?? 'Unknown';

                $totalQuantity = $equipmentGroup->count();
                $equipmentIds = $equipmentGroup->pluck('id');

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
                $equipmentLeft = $totalQuantity + $onRepairCount;
                return [
                    'size' => $sizeName,
                    'totalQuantity' => $totalQuantity,
                    'numberOfSizeOnRent' => $numberOfSizeOnRent,
                    'equipmentLeft' => max($equipmentLeft, 0),
                    'percent' => $equipmentLeft * ($numberOfSizeOnRent / 100)
                ];
            });

            foreach ($allSizes as $sizeId => $size) {
                if (!$sizeData->has($sizeId)) {
                    $sizeData[$sizeId] = [
                        'size' => $size->name,
                        'totalQuantity' => 0,
                        'numberOfSizeOnRent' => 0,
                        'equipmentLeft' => 0
                    ];
                }
            }

            return $sizeData->values()->toArray();
        })->toArray();

        $categoriesForPecent = EquipmentCategories::with([
            'equipment' => function ($query) {
                $query->select('id', 'category_id');
            }
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
                'percent' => $percent
            ];
        });


        return Inertia::render('Dashboard/Analysis', [
            'contragents_count' => $contragents_count,
            'contragents_inactive' => $contragents_inactive,
            'recent_contragents_count' => $recent_contragents_count,
            'recent_contragents_percentage' => $recent_contragents_percentage,
            'contragents_with_active_services_count' => $contragents_with_active_services_count,
            'active_contragents_percentage' => $active_contragents_percentage,
            'equipment_count' => $equipment_count,
            'recent_equipment_count' => $recent_equipment_count,
            'equipment_in_active_services_count' => $equipment_count_active_sum,
            'equipment_count_active_sum_percent' => $equipment_count_active_sum_percent,
            'on_store' => $on_store,
            'unavailable' => $unavailable,
            'categoryData' => $categoryData,
            'contragentincome' => $contragentincome,
            'equipment_categories' => $equipment_categories,
            'categoryDataIncome' => $categoryDataIncome,
            'categoriesProgress' => $categories,
            'categoryPercentages' => $categoryPercentages
        ]);
    }
    public function commercial()
    {

        // $contragents = Contragents::with('documents','documents.user')->get();

        // $contragents->each(function ($contragent) {
        //     if ($contragent->documents) {
        //         $contragent->documents->each(function ($document) {
        //             if (isset($document->contracts)) {
        //                 $document->contracts = json_decode($document->contracts, true) ?? [];
        //             }
        //             if (isset($document->financial)) {
        //                 $document->financial = json_decode($document->financial, true) ?? [];
        //             }
        //             if (isset($document->transport)) {
        //                 $document->transport = json_decode($document->transport, true) ?? [];
        //             }
        //             if (isset($document->commercials)) {
        //                 $document->commercials = json_decode($document->commercials, true) ?? [];
        //             }
        //             if (isset($document->adddocs)) {
        //                 $document->adddocs = json_decode($document->adddocs, true) ?? [];
        //             }

        //             $document->setRawAttributes($document->getAttributes());
        //         });
        //     }
        // });

        $contragents = Contragents::all();

        $documents = ContrDocuments::with('contragent', 'user')
            ->where('commercials', '!=', '')
            ->get();

        $processedDocuments = [];

        foreach ($documents as $document) {
            $contragentId = $document->contragent_id;
            if (!isset($processedDocuments[$contragentId])) {
                $processedDocuments[$contragentId] = [
                    'commercials' => [],
                    'count' => 0,
                    'contragent_name' => $document->contragent->name ?? 'Unknown'
                ];
            }

            $commercials = json_decode($document->commercials, true);
            if (is_array($commercials)) {
                foreach ($commercials as $commercial) {
                    $processedDocuments[$contragentId]['commercials'][] = [
                        'path' => $commercial,
                        'user' => [
                            'id' => $document->user->id,
                            'name' => $document->user->name,
                        ]
                    ];
                    $processedDocuments[$contragentId]['count']++;
                }
            }
        }



        return Inertia::render('Dashboard/Commercial', ['documents' => $processedDocuments, 'contragents' => $contragents]);
    }



}
