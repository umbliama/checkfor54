<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentLocation;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = EquipmentLocation::all();

        return response()->json($cities);
    }
}
