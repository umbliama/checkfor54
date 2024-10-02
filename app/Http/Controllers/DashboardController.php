<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategories;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $categories = EquipmentCategories::all();
        return Inertia::render("Dashboard",['categories' => $categories]);
    }
}
