<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategories;
use App\Models\EquipmentSize;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EquipmentLocation;
use App\Models\Equipment;
use App\Models\EquipmentRepair;
use App\Models\EquipmentTest;
class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $searchTerm = $request->query('search');

        $equipment_categories = EquipmentCategories::all();
        $equipment_sizes = EquipmentSize::all();
        $equipment_location = EquipmentLocation::all();

        $categoryId = $request->query('category_id');
        $sizeId = $request->query('size_id');

        $query = Equipment::query();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('manufactor', 'LIKE', "%$searchTerm%")
                    ->orWhere('series', 'LIKE', "%$searchTerm%")
                    ->orWhere('manufactor_date', 'LIKE', "%$searchTerm%")
                    ->orWhere('status', 'LIKE', "%$searchTerm%")
                    ->orWhere('price', 'LIKE', "%$searchTerm%")
                    ->orWhere('notes', 'LIKE', "%$searchTerm%");
            });
        }



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

        // Filtering logic
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        // Paginate results
        $equipment = $query->paginate(10);

        return Inertia::render('Equip/Index', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location
        ]);

    }

    public function repair(Request $request)
    {

        $categoryId = $request->query('category_id');
        $sizeId = $request->query('size_id');
        $equipment_location = EquipmentLocation::all();
        $equipment_repairs = EquipmentRepair::all();


        $equipment_series = Equipment::where('category_id', $categoryId)->where('size_id', $sizeId)->pluck('series');

        return Inertia::render('Equip/Repair', ['equipmentSeries' => $equipment_series, 'equipment_location' => $equipment_location, 'equipment_repairs' => $equipment_repairs]);
    }
    public function createRepair()
    {
        return Inertia::render('Equip/CreateRepair');
    }

    public function storeRepair(Request $request)
    {
        $request->validate([
            'repair_date' => 'required|date',
            'location_id' => 'required|int',
            'expense' => 'required|int',
            'description' => 'required|min:3|max:200',
            'category_id' => 'required|int',
            'size_id' => 'required|int',
            'series' => 'required|string '
        ]);

        EquipmentRepair::create($request->all());
    }


    public function report(Request $request)
    {
        $equipment_location = EquipmentLocation::all();
        $equipment_categories = EquipmentCategories::all();
        $equipment_sizes = EquipmentSize::all();
        $categoryId = $request->query('category_id');
        $sizeId = $request->query('size_id');
        $series = $request->query('series');




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
        $equipment_series = Equipment::where('category_id', $categoryId)->where('size_id', $sizeId)->pluck('series');

        $equipment = Equipment::where('category_id', $categoryId)->where('size_id', $sizeId)->where('series', $series)->get();

        $equipment_repairs = EquipmentRepair::where('category_id', $categoryId)->where('size_id', $sizeId)->where('series', $series);
        // $equipment_repairs = EquipmentRepair::all();

        return Inertia::render('Equip/Report', [
            'equipment_repairs' => $equipment_repairs,
            'equipment_location' => $equipment_location,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_series' => $equipment_series,
            'equipment' => $equipment,
        ]);
    }

    


    public function tests(Request $request)
    {

        $categoryId = $request->query('category_id');
        $sizeId = $request->query('size_id');
        $equipment_location = EquipmentLocation::all();
        $equipment_tests = EquipmentTest::all();


        $equipment_series = Equipment::where('category_id', $categoryId)->where('size_id', $sizeId)->pluck('series');

        return Inertia::render('Equip/Tests', ['equipmentSeries' => $equipment_series, 'equipment_location' => $equipment_location, 'equipment_tests' => $equipment_tests]);
    }

    public function storeTest(Request $request)
    {
        $request->validate([
            'test_date' => 'required|date',
            'location_id' => 'required|int',
            'expense' => 'required|int',
            'description' => 'required|min:3|max:200',
            'category_id' => 'required|int',
            'size_id' => 'required|int',
            'series' => 'required|string '
        ]);

        EquipmentTest::create($request->all());
    }

    public function price()
    {
        return Inertia::render('Equip/Price');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $equipment_categories = EquipmentCategories::all();
        $equipment_sizes = EquipmentSize::all();


        return Inertia::render('Equip/Create', [
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes
        ]);

    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20'
        ]);

        EquipmentLocation::create($request->all());


    }
    public function deleteLocation($id)
    {

        $location = EquipmentLocation::findOrFail($id);

        $location->delete();


        return redirect()->route('equip.index')->with('success', 'Локация успешно удалена.');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'manufactor' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'size_id' => 'required|integer',
            'series' => 'nullable|string',
            'manufactor_date' => 'nullable|string',
            'status' => ['required', 'string', 'in:new,good,satisfactory, bad, off'],
            'notes' => 'nullable|string',
            'price' => 'required|integer',
            'commentary' => 'nullable|text',
        ]);

        Equipment::create($request->all());

        return redirect()->route('equip.index')->with('success', 'Оборудование успешно создано.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
