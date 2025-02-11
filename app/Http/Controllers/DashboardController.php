<?php

namespace App\Http\Controllers;

use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentLocation;
use App\Models\EquipmentRepair;
use App\Models\EquipmentSize;
use App\Models\EquipmentTest;
use App\Models\Sale;
use App\Models\Service;
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


        $location_counts = [];
        foreach ($equipment_location as $location) {
            $locationIdCount = $location->id;
            if ($category_id > 0) {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->where('category_id', $category_id)->count();
            } else {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->count();

            }
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
                    ->where('s.active', 1) //
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
                    ->where('s.active', 1) //
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
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents' => $contragents,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'location_counts' => $location_counts
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
        $equipment_sizes = EquipmentSize::where('category_id', $category_id)->get();

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

                $subQuery->whereNotNull('er.repair_date');
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

                    $subQuery->whereNotNull('et.test_date');
                });
        })
            ->distinct()
            ->paginate($perPage);

        $equipment_categories_counts = Equipment::whereIn('id', $equipment->pluck('id'))
            ->select('category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->pluck('count', 'category_id');

        $equipment_sizes_counts = Equipment::whereIn('id', $equipment->pluck('id'))
            ->select('size_id', DB::raw('COUNT(*) as count'))
            ->groupBy('size_id')
            ->pluck('count', 'size_id');

        $equipment_locations_counts = Equipment::whereIn('id', $equipment->pluck('id'))
            ->select('location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('location_id')
            ->pluck('count', 'location_id');


        return Inertia::render('Dashboard/Serviced', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories_counts_all' => $equipment_categories_counts_all,
            'contragents' => $contragents,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_locations_counts' => $equipment_locations_counts
        ]);
    }
    public function analysis()
    {
        $contragents_count = Contragents::count();
        $contragents_inactive = Contragents::where('status', false)->count();
        $recent_contragents = Contragents::where('created_at', '>=', now()->subMonths(3))
            ->get();

        $recent_contragents_count = $recent_contragents->count();

        $recent_contragents_percentage = $contragents_count > 0 ? ($recent_contragents_count / $contragents_count) * 100 : 0;

        $contragents_with_active_services_count = Contragents::whereHas('services', function ($query) {
            $query->where('active', true);
        })->count();

        $active_contragents_percentage = $contragents_count > 0 ? ($contragents_with_active_services_count / $contragents_count) * 100 : 0;

        $equipment_count = Equipment::count();

        $recent_equipment = Equipment::where('created_at', '>=', now()->subMonths(3))
            ->get();
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

        $serviceIncome = Service::select(
            'contragent_id',
            DB::raw('SUM(full_income) as total_income')
        )
            ->groupBy('contragent_id');

        $saleIncome = Sale::select(
            'contragent_id',
            DB::raw('SUM(price) as total_income')
        )
            ->groupBy('contragent_id');
        $combinedIncome = $serviceIncome->unionAll($saleIncome);
        $finalIncome = DB::table(DB::raw("({$combinedIncome->toSql()}) as incomes"))
            ->mergeBindings($combinedIncome->getQuery())
            ->select(
                'contragent_id',
                DB::raw('SUM(total_income) as full_income')
            )
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

        $equipment_categories = EquipmentCategories::all();



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
            'equipment_categories' => $equipment_categories
        ]);
    }

    public function commercial()
    {
        return Inertia::render('Dashboard/Commercial');
    }



}
