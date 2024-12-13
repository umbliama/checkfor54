<?php

namespace App\Http\Controllers;
use App\Models\Contragents;
use App\Models\SaleExtra;
use App\Models\SaleSub;
use App\Models\Equipment;
use App\Models\Column;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Sale;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with([
            'subservices.equipment.category',
            'subservices.equipment.size',
            'equipment.category',
            'equipment.size'
        ])->paginate(10);

        $saleStatuses = Sale::getStatusesMapping();

        $sales->getCollection()->transform(function ($sale) {
            $sale->subservices = $sale->subservices->map(function ($subservice) {

                if ($subservice->equipment_id) {
                    $subservice->equipment_info = $subservice->equipment;
                } else {
                    $subservice->equipment_info = null;
                }
                return $subservice;
            });

            $sale->equipment_info = $sale->equipment;

            if ($sale->equipment) {
                $sale->equipment_info->category_name = $sale->equipment->category->name ?? null;
                $sale->equipment_info->size_name = $sale->equipment->size->name ?? null;
            } else {
                $sale->equipment_info = null;
            }

            return $sale;
        });

        return Inertia::render('Sale/Index', ['sales' => $sales, 'saleStatuses' => $saleStatuses]);
    }

    public function getExtraServices()
    {
        $saleStatuses = Sale::getExtraServices();

        return $saleStatuses;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contragents = Contragents::all();
        $saleStatuses = Sale::getStatusesMapping();
        $extraServices = Sale::getExtraServices();

        return Inertia::render('Sale/Create', ['contragents' => $contragents, 'saleStatuses' => $saleStatuses, "extraServices" => $extraServices]);
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
            'sale_number',
            'sale_date',
            'commentary',
            'status',
            'price'
        ]));

        foreach ($request->subEquipment as $subEquipmentData) {
            SaleSub::create([
                'equipment_id' => $subEquipmentData['subequipment_id'],
                'sale_id' => $sale->id,
                'shipping_date' => $subEquipmentData['shipping_date'],
                'commentary' => $subEquipmentData['commentary'],
                'price' => $subEquipmentData['price'],
            ]);
        }


        foreach ($request->extraServices as $extraService) {
            SaleExtra::create([
                'shipping_date' => $extraService['shipping_date'],
                'sale_id' => $sale->id,
                'type' => $extraService['item']['item'],
                'commentary' => $extraService['commentary'],
                'price' => $extraService['price'],
            ]);
        }

        foreach (User::all() as $user){
            Notification::create([
                'type' => 'Создан новая продажа №'.$sale->id,
                'data' => ['equipment_id' => $sale->equipment_id],
                'user_id' => $user->id
            ]);
        }

        return redirect()->route('sale.index')->with('success', 'Service created successfully.');

    }

    public function createIncident($id)
    {
        $user_id = Auth::id();
        $sale = Sale::findOrFail($id);
        $equipment = Equipment::findOrFail($sale->equipment_id)->value('id');
        $contragent_id = Contragents::findOrFail($sale->contragent_id)->value('id');
        $position = Column::max('position') + 1;
        $column = Column::create(['position' => $position,'type' => 'adv']);
        Notification::create([
            'type' => 'Создан новый инцидент',
            'data' => ['position'=>$position],
            'user_id' => $user_id
        ]);

        $position = $column->blocks()->max('position') + 1;
    
        $block = $column->blocks()->create([
            'type' => 'customer',
            'contragent_id' => $contragent_id,
            'equipment_id' => $equipment,
            'position' => $position
        ]);
        if ($sale->subequiment_id !== null) {
            $block_subequipment = $block->subequipment()->create([
                'block_id' => $block->id,
                'subequipment_id' => $sale->subequipment_id
            ]);
        }
        

        return redirect()->route('incident.index')->with('success', 'Column updated successfully.');
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
        return Inertia::render('Sale/Edit');
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
        $sale = Sale::findOrFail($id);

        $sale->delete();
    }
}
