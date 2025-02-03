<?php

namespace App\Http\Controllers;

use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentLocation;
use App\Models\EquipmentSize;
use Illuminate\Http\Request;

use Inertia\Inertia;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage');
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
        $query = Equipment::query();
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
        $perPage = $request->input('perPage', 10);
        $equipment_categories_counts_all = 0;
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
            $equipment_categories_counts_all += $equipment_categories_counts[$categoryIDForCount];
        }

        $contragents = Contragents::all();

        $rented_equipment = Equipment::whereHas('serviceEquipment.services', function ($query) {
            $query->where('active', 1);
        })->with([
                    'serviceEquipment.services' => function ($query) {
                        $query->where('active', 1);
                    }
                ])->whereDoesntHave('serviceEquipment.services', function ($query) {
                    $query->where('active', 0);
                })->paginate($perPage);

        return Inertia::render('Dashboard/Rent', ['equipment_categories' => $equipment_categories, 'equipment_categories_counts' => $equipment_categories_counts, 'equipment_categories_counts_all' => $equipment_categories_counts_all, 'contragents' => $contragents, 'rented_equipment' => $rented_equipment]);
    }
    public function free()
    {
        return Inertia::render('Dashboard/Free');
    }
    public function serviced()
    {
        return Inertia::render('Dashboard/Serviced');
    }
    public function analysis()
    {
        return Inertia::render('Dashboard/Analysis');
    }
}
