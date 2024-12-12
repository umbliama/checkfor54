<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\Sale;
use App\Models\Service;
use Inertia\Inertia;



class SearchController extends Controller
{

    public function index()
    {
        return Inertia::render('GlobalSearch/Index');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
    
        $contragents = Contragents::where('name', 'like', '%'. $query. '%')
            ->orWhere('fullname', 'like', '%'. $query. '%')
            ->orWhere('inn', 'like', '%'. $query. '%')
            ->get();
    
        $equipment = Equipment::where('manufactor', 'like', '%'. $query. '%')
            ->orWhere('series', 'like', '%'. $query. '%')
            ->get();
    
        $sales = Sale::where('sale_number', 'like', '%'. $query. '%')
            ->orWhere('commentary', 'like', '%'. $query. '%')
            ->get();
    
        $services = Service::where('service_number', 'like', '%'. $query. '%')
            ->orWhere('commentary', 'like', '%'. $query. '%')
            ->get();
    
        $results = [];
    
        foreach ($contragents as $contragent) {
            $results[] = [
                'id' => $contragent->id,
                'type' => 'contragent',
                'name' => $contragent->name,
                'description' => $contragent->fullname,
                'link' => "/contragents/".$contragent->id
            ];
        }
    
        foreach ($equipment as $item) {
            $results[] = [
                'id' => $item->id,
                'type' => 'equipment',
                'name' => $item->manufactor,
                'description' => $item->series,
            ];
        }
    
        foreach ($sales as $sale) {
            $results[] = [
                'id' => $sale->id,
                'type' =>'sale',
                'name' => $sale->sale_number,
                'description' => $sale->commentary,
            ];
        }
    
        foreach ($services as $service) {
            $results[] = [
                'id' => $service->id,
                'type' =>'service',
                'name' => $service->service_number,
                'description' => $service->commentary,
            ];
        }
    
        return redirect()->route('search.index')->with('search_results', $results);
    }
}