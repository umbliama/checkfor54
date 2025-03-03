<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentMove;
use App\Models\EquipmentRepair;
use App\Models\EquipmentSize;
use App\Models\EquipmentTest;
use App\Models\Service;
use App\Models\ServiceSub;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{

    public function getFilteredRepairs(Request $request)
    {
        $series   = $request->input('series');
        $category = $request->input('category_id');
        $size     = $request->input('size_id');

        $repairs = EquipmentRepair::when($category, function ($query, $category) {
            return $query->where('category_id', $category);
        })
            ->when($size, function ($query, $size) {
                return $query->where('size_id', $size);
            })
            ->when($series, function ($query, $series) {
                return $query->where('series', $series);
            })
            ->with('directory')
            ->get()->map(function ($repair) {
            if (isset($repair->directory['files'])) {
                $repair->directory['files'] = json_decode($repair->directory['files'], true) ?? [];
            }
            return $repair;
        });

        return response()->json($repairs);
    }
    public function getFilteredMoves(Request $request)
    {
        $series   = $request->input('series');
        $category = $request->input('category_id');
        $size     = $request->input('size_id');

        $query = EquipmentMove::query();

        if ($series) {
            $query->where('series', $series);
        }
        if ($category) {
            $query->where('category_id', $category);
        }
        if ($size) {
            $query->where('size_id', $size);
        }

        $repairs = $query->with('directory.files.uploader')->get();

        return $repairs;
    }
    public function getFilteredReports(Request $request)
    {
        $series   = $request->input('series');
        $category = $request->input('category_id');
        $size     = $request->input('size_id');

        $reports = Equipment::when($category, function ($query, $category) {
            return $query->where('category_id', $category);
        })->when($size, function ($query, $size) {
            return $query->where('size_id', $size);
        })->when($series, function ($query, $series) {
            return $query->where('series', $series);
        })->get();

        return response()->json($reports);
    }
    public function getFilteredTests(Request $request)
    {
        $series   = $request->input('series');
        $category = $request->input('category_id');
        $size     = $request->input('size_id');

        $reports = EquipmentTest::when($category, function ($query, $category) {
            return $query->where('category_id', $category);
        })->when($size, function ($query, $size) {
            return $query->where('size_id', $size);
        })->when($series, function ($query, $series) {
            return $query->where('series', $series);
        })->with('directory')
            ->get()->map(function ($repair) {
            if (isset($repair->directory['files'])) {
                $repair->directory['files'] = json_decode($repair->directory['files'], true) ?? [];
            }
            return $repair;
        });

        return response()->json($reports);
    }

    public function getFilteredServicesInActive(Request $request)
    {
        $series   = $request->input('series');
        $category = $request->input('category_id');
        $size     = $request->input('size_id');

        $equipment_ids = Equipment::where('series', $series)
            ->where('category_id', $category)
            ->where('size_id', $size)
            ->pluck('id');

        $services = Service::whereIn('equipment_id', $equipment_ids)->get();

        $equipment = Equipment::whereIn('id', $services->pluck('equipment_id'))->get();

        $contragents = Contragents::whereIn('id', $services->pluck('contragent_id'))->get();

        $subequipment = ServiceSub::whereIn('service_id', $services->pluck('id'))->get();

        $sizes      = EquipmentSize::whereIn('id', $equipment->pluck('size_id'))->get();
        $categories = EquipmentCategories::whereIn('id', $equipment->pluck('category_id'))->get();

        $subequipmentEquipment = Equipment::whereIn('id', $subequipment->pluck('subequipment_id'))->get();

        $response = $services->map(function ($service) use ($contragents, $equipment, $subequipment, $sizes, $categories, $subequipmentEquipment) {
            $contragent = $contragents->firstWhere('id', $service->contragent_id);

            $equipmentData = $equipment->firstWhere('id', $service->equipment_id);

            $sizeData     = $sizes->firstWhere('id', $equipmentData->size_id);
            $categoryData = $categories->firstWhere('id', $equipmentData->category_id);

            $subequipmentData = $subequipment->where('service_id', $service->id)->map(function ($sub) use ($subequipmentEquipment, $sizes, $categories) {
                $subequipmentData = $subequipmentEquipment->firstWhere('id', $sub->subequipment_id);

                $sizeData     = $sizes->firstWhere('id', $subequipmentData->size_id);
                $categoryData = $categories->firstWhere('id', $subequipmentData->category_id);

                return [
                    'subequipment_id'       => $sub->subequipment_id,
                    'shipping_date'         => $sub->shipping_date,
                    'period_start_date'     => $sub->period_start_date,
                    'commentary'            => $sub->commentary,
                    'return_date'           => $sub->return_date,
                    'period_end_date'       => $sub->period_end_date,
                    'store'                 => $sub->store,
                    'operating'             => $sub->operating,
                    'income'                => $sub->income,
                    'return_reason'         => $sub->return_reason,
                    'subequipment_size'     => $sizeData ? $sizeData->name : null,
                    'subequipment_category' => $categoryData ? $categoryData->name : null,
                ];
            });

            return [
                'service'      => $service,
                'contragent'   => $contragent,
                'equipment'    => [
                    'equipmentData' => $equipmentData,
                    'size'          => $sizeData ? $sizeData->name : null,
                    'category'      => $categoryData ? $categoryData->name : null,
                ],
                'subequipment' => $subequipmentData,
            ];
        });

        return response()->json($response);
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
    public function getSubEquipmentCategories()
    {

        $EquipmentCategories = EquipmentCategories::whereNotIn('id', [1, 2])->get();

        return response()->json($EquipmentCategories);

    }


    public function getEquipmentCategoriesCount()
    {

        $equipment_categories        = EquipmentCategories::all();
        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount            = $category->id;
            $equipment_categories_counts[] = [
                'category_id' => $categoryIDForCount,
                'count'       => Equipment::where('category_id', $categoryIDForCount)->count(),
            ];
        }

        return response()->json($equipment_categories_counts);

    }
    public function getEquipmentSizes()
    {

        $equipment_sizes = EquipmentSize::all();

        return response()->json($equipment_sizes);

    }

    public function getEquipmentSizesByCategoryId($categoryId)
    {
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();

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
            $sizeIDForCount                          = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        return response()->json($equipment_sizes_counts);

    }
    public function getEquipmentSizesCountById()
    {

        $equipment_sizes = EquipmentSize::where('category_id', 1);

        dd($equipment_sizes);
        $equipment_sizes_counts = [];

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount                          = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        return response()->json($equipment_sizes_counts);

    }

    public function getEquipmentByCategoryAndBySize($categoryId, $sizeId)
    {
        $equipment = Equipment::where('category_id', $categoryId)
            ->where('size_id', $sizeId)
            ->with(['activeServices', 'repairs', 'tests']) 
            ->paginate(10);

        $equipment->map(function ($item) {
            $item->used = $item->used; 
            return $item;
        });

        return response()->json($equipment);
    }

    public function getSubEquipmentByCategoryAndBySize($categoryId, $sizeId)
    {
        $equipment = Equipment::where('category_id', $categoryId)
            ->where('size_id', $sizeId)
            ->with(['activeServices', 'repairs', 'tests'])
            ->paginate(10);
    
        $equipment->map(function ($item) {
            $item->used = $item->used;
            return $item;
        });
        return response()->json($equipment);
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

        $equipment = Equipment::where('category_id', $categoryId)->get();

        return response()->json($equipment);
    }
    public function getEquipmentBySizeID($sizeId)
    {

        $equipment = Equipment::where('category_id', $sizeId)->get();

        return response()->json($equipment);
    }

    public function getEquipmentWithExtraData($id)
    {
        $equipment = Equipment::with('category', 'size')->findOrFail($id);

        return response()->json([
            'equipment'    => $equipment,
            'equipment_id' => $id,
        ]);
    }

}
