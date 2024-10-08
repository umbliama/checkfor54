<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentSize;
use App\Models\EquipmentTest;
use Illuminate\Http\Request;
use App\Models\EquipmentRepair;


class EquipmentController extends Controller
{

    public function getFilteredRepairs(Request $request)
    {
        $series = $request->input('series');
        $category = $request->input('category_id');
        $size = $request->input('size_id');

        // Assuming you have a Repair model
        $repairs = EquipmentRepair::where('series', $series)
            ->where('category_id', $category)
            ->where('size_id', $size)
            ->get();

        return response()->json($repairs);
    }
    public function getFilteredReports(Request $request)
    {
        $series = $request->input('series');
        $category = $request->input('category_id');
        $size = $request->input('size_id');

        // Assuming you have a Repair model
        $reports = Equipment::where('series', $series)
            ->where('category_id', $category)
            ->where('size_id', $size)
            ->get();

        return response()->json($reports);
    }
    public function getFilteredTests(Request $request)
    {
        $series = $request->input('series');
        $category = $request->input('category_id');
        $size = $request->input('size_id');

        // Assuming you have a Repair model
        $reports = EquipmentTest::where('series', $series)
            ->where('category_id', $category)
            ->where('size_id', $size)
            ->get();

        return response()->json($reports);
    }


    public function getEquipment(Request $request)
    {


        $equipment = Equipment::paginate(4);



        return response()->json($equipment);

    }

    public function getEquipmentCategories()
    {


        $EquipmentCategories = EquipmentCategories::all();



        return response()->json($EquipmentCategories);

    }
    public function getEquipmentCategoriesCount()
    {

        $equipment_categories = EquipmentCategories::all();
        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[] = [
                'category_id' => $categoryIDForCount,
                'count' => Equipment::where('category_id', $categoryIDForCount)->count()
            ];
        }

        return response()->json($equipment_categories_counts);


    }
    public function getEquipmentSizes()
    {


        $equipment_sizes = EquipmentSize::all();



        return response()->json($equipment_sizes);

    }
    public function getEquipmentCount()
    {
        $equipment_count = Equipment::count();

        return response()->json($equipment_count);
    }
    public function getEquipmentRepairCount()
    {
        $equipment_count = EquipmentRepair::count();

        return response()->json($equipment_count);
    }
    public function getEquipmentTestCount()
    {
        $equipment_count = EquipmentTest::count();

        return response()->json($equipment_count);
    }
    public function getEquipmentSizesCount()
    {

        $equipment_sizes = EquipmentSize::all();

        $equipment_sizes_counts = [];

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        return response()->json($equipment_sizes_counts);


    }

    public function getEquipmentByCategoryAndBySize($categoryId, $sizeId)
    {
        $equipment = Equipment::where('category_id', $categoryId)
            ->where('size_id', $sizeId)
            ->paginate(10); // paginate before getting results

        return response()->json($equipment); // directly return the paginated result

    }



    public function getEquipmentByID($id)
    {
        $equipment = Equipment::with(['category', 'size'])
            ->where('id', $id)
            ->first();

        return response()->json($equipment);
    }
    public function getEquipmentByCategoryID($categoryId)
    {

        $query = Equipment::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $equipment = $query->paginate(3);

        return response()->json($equipment);
    }

}
