<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ServiceModel;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render("Services/Index");
    }

    public function create()
    {
        $equipment = Equipment::with('category', 'size')->get();
        $contragents = Contragents::all();

        // Transform the equipment data into the desired format
        $equipmentFormatted = $equipment->map(function ($item) {
            return [
                'id' => $item->id,
                'display' => $item->category->name . ' ' . $item->size->name . ' ' . $item->series,
            ];
        });
        return Inertia::render("Services/Create", ['equipmentFormatted' => $equipmentFormatted, 'contragents' => $contragents]);
    }

    public function store(Request $request){
        $request->validate([
            'equipment_id' => 'required|int',
            'contragent_id' => 'required|int',
            'shipping_date' => 'required|date',
            'period_start_date' => 'required|date',
            'return_date' => 'required|date',
            'period_end_date' => 'required|date',
            'store' => 'required|string',
            'operating' => 'required|string',
            'return_reason' => 'required',
            'active' => 'required|boolean',
            'income' => 'required|int'
     
        ]);

        Service::create($request->all());
    }
}
