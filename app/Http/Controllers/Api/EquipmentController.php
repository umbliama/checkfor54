<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
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


    public function getEquipment(Request $request) {


        $equipment = Equipment::paginate(4);



        return response()->json($equipment);

    }
        public function getEquipmentById($categoryId){

        $query = Equipment::query();
    
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $equipment = $query->paginate(3);

        return response()->json($equipment);
    }
    
}
