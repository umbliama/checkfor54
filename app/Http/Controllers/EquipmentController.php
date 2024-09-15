<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategories;
use App\Models\EquipmentSize;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipment;
class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $equipment = Equipment::all();
        $equipment_categories = EquipmentCategories::all(); 
        $equipment_sizes = EquipmentSize::all();

        $categoryId = $request->query('category');

        $query = Equipment::query();

        if($categoryId) {
            $query->where('category_id',$categoryId);
        }

        $equipment = $query->get();


        return Inertia::render('Equip/Index', [
            'equipment'=>  $equipment,
            'equipment_categories'=> $equipment_categories,
            'equipment_sizes'=> $equipment_sizes,
            'categories' => EquipmentCategories::all(), 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $equipment_categories = EquipmentCategories::all(); 
        $equipment_sizes = EquipmentSize::all();


        return Inertia::render('Equip/Create',[
            'equipment_categories'=> $equipment_categories,
            'equipment_sizes'=> $equipment_sizes
        ]);
        
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
