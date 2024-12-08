<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\SaleSub;
use Inertia\Inertia;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::paginate(10);
        return Inertia::render('Sale/Index',['sales' => $sales]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contragents = Contragents::all();
        return Inertia::render('Sale/Create', ['contragents' => $contragents]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|int',
            'contragent_id' => 'required|int',
            'shipping_date' => 'required|date',
            'sale_number' => "required|string",
            'sale_date' => "required|date",
            'commentary' => "required|string",
            'status' => "in:credit,full,pred",
            'price' => "required|string",
            'subEquipment' => 'array|nullable',
            'subEquipment.*.subequipment_id' => 'nullable|int|exists:equipment,id',
            'subEquipment.*.shipping_date' => 'nullable|date',
            'subEquipment.*.period_start_date' => 'nullable|date',
            'subEquipment.*.return_date' => 'nullable|date',
            'subEquipment.*.period_end_date' => 'nullable|date',
            'subEquipment.*.income' => 'nullable|int'

        ]);

        $sale = Sale::create($request->only([
            'equipment_id',
            'contragent_id',
            'shipping_date',
            'service_number',
            'service_date',
            'period_start_date',
            'return_date',
            'period_end_date',
            'store',
            'operating',
            'return_reason',
            'active',
            'income'
        ]));

        foreach ($request->subEquipment as $subEquipmentData) {
            SaleSub::create([
                'equipment_id' => $subEquipmentData['subequipment_id'],
                'sale_number' => $sale->id,
                'shipping_date' => $subEquipmentData['shipping_date'],
                'commentary' => $subEquipmentData['commentary'],
                'price' => $subEquipmentData['price'],
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Service created successfully.');

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
