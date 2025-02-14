<?php

namespace App\Http\Controllers;

use App\Models\Contragents;
use App\Models\EquipmentCategories;
use App\Models\EquipmentMove;
use App\Models\EquipmentPrice;
use App\Models\EquipmentSize;
use App\Models\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EquipmentLocation;
use App\Models\Equipment;
use App\Models\EquipmentRepair;
use App\Models\EquipmentTest;
use App\Models\ServiceSub;
use App\Models\ServiceEquip;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage');

        $equipment_categories = EquipmentCategories::all();
        $equipment_location = EquipmentLocation::all();

        $serviceSubCount = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', 1);
        })->count();


        $serviceEquipCount = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })->count();


        $equipment_on_rent_count = $serviceSubCount + $serviceEquipCount;


        $activeEquipment = ServiceEquip::with(['services', 'serviceSubs'])
            ->whereHas('services', function ($query) {
                $query->where('active', 1);
            })
            ->get()
            ->map(function ($serviceEquip) {
                $subs = $serviceEquip->serviceSubs->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'equipment_id' => $sub->subequipment_id,
                        'shipping_date' => $sub->shipping_date,
                        'period_start_date' => $sub->period_start_date,
                        'return_date' => $sub->return_date,
                        'period_end_date' => $sub->period_end_date,
                        'store' => $sub->store,
                        'operating' => $sub->operating,
                        'income' => $sub->income,
                        'type' => 'sub'
                    ];
                });

                $mainEquip = [
                    'id' => $serviceEquip->id,
                    'equipment_id' => $serviceEquip->equipment_id,
                    'shipping_date' => $serviceEquip->shipping_date,
                    'period_start_date' => $serviceEquip->period_start_date,
                    'return_date' => $serviceEquip->return_date,
                    'period_end_date' => $serviceEquip->period_end_date,
                    'store' => $serviceEquip->store,
                    'operating' => $serviceEquip->operating,
                    'income' => $serviceEquip->income,
                    'type' => 'main'
                ];

                return collect([$mainEquip])->concat($subs);
            })
            ->flatten(1);

        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $query = Equipment::query()->with('directory');
        $locationId = $request->query('location_id');
        $rentActive = $request->query('isRentActive');

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('manufactor', 'LIKE', "%$searchTerm%")
                    ->orWhere('series', 'LIKE', "%$searchTerm%")
                    ->orWhere('manufactor_date', 'LIKE', "%$searchTerm%")
                    ->orWhere('status', 'LIKE', "%$searchTerm%")
                    ->orWhere('price', 'LIKE', "%$searchTerm%")
                    ->orWhere('notes', 'LIKE', "%$searchTerm%");
            });
        }

        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        $location_counts = [];
        foreach ($equipment_location as $location) {
            $locationIdCount = $location->id;
            if ($categoryId > 0) {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->where('category_id', $categoryId)->count();
            } else {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->count();
            }
        }
        if ($rentActive) {
            $equipment = $query->whereHas('serviceEquipment.services', function ($query) {
                $query->where('active', 1);
            })->with('serviceEquipment.serviceSubs')->get();

            $flattenedEquipment = $equipment->flatMap(function ($main) {
                $items = collect([$main]);
                foreach ($main->serviceEquipment as $serviceEquipment) {
                    $items = $items->merge($serviceEquipment->serviceSubs);
                }

                return $items;
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        if ($locationId > 0) {
            $query->where('location_id', $locationId);
        }



        $equipment = $query->paginate($perPage);

        $equipment->getCollection()->map(function ($sale) {
            if (isset($sale->directory['files'])) {
                $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
            }
            return $sale;
        });


        return Inertia::render('Equip/Index', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'selectedCategory' => $categoryId,
            'location_counts' => $location_counts,
            'equipment_on_rent_count' => $equipment_on_rent_count,
            'activeEquipment' => $activeEquipment
        ]);
    }
    public function storeHyperLink(Request $request, $id)
    {
        // Validate the hyperlink input
        $request->validate([
            'hyperlink' => 'required|string'
        ]);

        // Find the equipment by its id
        $equipment = Equipment::find($id);

        // Check if equipment exists
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }

        // Update the hyperlink field
        $equipment->hyperlink = $request->input('hyperlink');
        $equipment->save();
    }

    public function storeRepairHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string'
        ]);

        $repair = EquipmentRepair::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    public function storeTestHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string'
        ]);

        $repair = EquipmentTest::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    public function storeServiceHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string'
        ]);

        $repair = EquipmentTest::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    public function repair(Request $request)
    {

        $sizeId = $request->query('size_id');
        $series = $request->query('series');
        $equipment_location = EquipmentLocation::all();
        $categoryId = $request->query('category_id', 1);
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $equipment_categories = EquipmentCategories::all();
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }



        $equipment_series = Equipment::when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->pluck('series');
        $equipment_repairs = Equipment::where('category_id', $categoryId)

            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->where('series', $series)
            ->with('directory')->get()->map(function ($sale) {
                if (isset($sale->directory['files'])) {
                    $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
                }
                return $sale;
            });
        return Inertia::render('Equip/Repair', [
            'equipmentSeries' => $equipment_series,
            'equipment_location' => $equipment_location,
            'equipment_repairs' => $equipment_repairs,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes_counts' => $equipment_sizes_counts
        ]);
    }
    public function createRepair()
    {
        return Inertia::render('Equip/CreateRepair');
    }

    public function storeRepair(Request $request)
    {
        try {
            $request->validate([
                'on_repair_date' => 'required|date',
                'repair_date' => 'nullable|date',
                'location_id' => 'nullable|int',
                'expense' => 'nullable|int',
                'description' => 'nullable|min:3|max:200',
                'category_id' => 'required|int',
                'size_id' => 'required|int',
                'series' => 'required|string',
            ]);

            $foundedEquipment = Equipment::where('category_id', $request->category_id)
                ->where('size_id', $request->size_id)
                ->where('series', $request->series)
                ->first();

            if (!$foundedEquipment) {
                return back()->with('error', 'Оборудование не найдено.');
            }

            if ($foundedEquipment->location_id != $request->location_id) {
                return back()->with('error', 'Локации не совпадают.');
            }

            EquipmentRepair::create(array_merge($request->all(), [
                'equipment_id' => $foundedEquipment->id,
            ]));

            return back()->with('message', 'Запись успешно добавлена.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function updateRepair(Request $request, $id)
    {
        $repair = EquipmentRepair::findOrFail($id);
        $request->validate([
            'repair_date' => 'nullable|date',
            'location_id' => 'nullable|int',
            'expense' => 'nullable|int',
            'description' => 'nullable|min:3|max:200',
        ]);
        $repair->update($request->all());
    }
    public function destroyRepair($id)
    {
        $repair = EquipmentRepair::findOrFail($id);

        $repair->delete();
    }


    public function report(Request $request)
    {
        $equipment_location = EquipmentLocation::all();
        $equipment_categories = EquipmentCategories::all();
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $series = $request->query('series');


        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();



        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }
        $equipment_series = Equipment::when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->pluck('series');

        $equipment = Equipment::where('category_id', $categoryId)

            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->where('series', $series)
            ->get()->map(function ($sale) {
                if (isset($sale->directory['files'])) {
                    $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
                }
                return $sale;
            });



        $equipment_repairs = EquipmentRepair::where('category_id', $categoryId)->where('size_id', $sizeId)->where('series', $series);
        $equipment_tests = Equipment::where('category_id', $categoryId)

            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->where('series', $series)
            ->get();
        // $equipment_repairs = EquipmentRepair::all();


        return Inertia::render('Equip/Report', [
            'equipment_repairs' => $equipment_repairs,
            'equipment_location' => $equipment_location,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_series' => $equipment_series,
            'equipment_tests' => $equipment_tests,
            'equipment' => $equipment,
        ]);
    }




    public function tests(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();
        $sizeId = $request->query('size_id');
        $equipment_location = EquipmentLocation::all();
        $equipment_tests = EquipmentTest::all();

        $categoryId = $request->query('category_id', 1);

        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();


        $equipment_categories_counts = [];

        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }
        $equipment_sizes_counts = [];

        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }

        $equipment_series = Equipment::when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->pluck('series');

        return Inertia::render(
            'Equip/Tests',
            [
                'equipmentSeries' => $equipment_series,
                'equipment_location' => $equipment_location,
                'equipment_tests' => $equipment_tests,
                'equipment_categories_counts' => $equipment_categories_counts,
                'equipment_sizes_counts' => $equipment_sizes_counts,
                'equipment_categories' => $equipment_categories,
                'equipment_sizes' => $equipment_sizes
            ]
        );
    }


    public function storeTest(Request $request)
    {
        try {
            $request->validate([
                'on_test_date' => 'required|date',
                'test_date' => 'nullable|date',
                'location_id' => 'nullable|int',
                'expense' => 'nullable|int',
                'description' => 'nullable|min:3|max:200',
                'category_id' => 'required|int',
                'size_id' => 'required|int',
                'series' => 'required|string',
            ]);

            $foundedEquipment = Equipment::where('category_id', $request->category_id)
                ->where('size_id', $request->size_id)
                ->where('series', $request->series)
                ->first();

            if (!$foundedEquipment) {
                return back()->with('error', 'Оборудование не найдено.');
            }

            if ($foundedEquipment->location_id != $request->location_id) {
                return back()->with('error', 'Локации не совпадают.');
            }

            EquipmentTest::create(array_merge($request->all(), [
                'equipment_id' => $foundedEquipment->id,
            ]));

            return back()->with('message', 'Запись успешно добавлена.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function updateTest(Request $request, $id)
    {

        $test = EquipmentTest::findOrFail($id);
        $request->validate([
            'test_date' => 'nullable|date',
            'location_id' => 'nullable|int',
            'expense' => 'nullable|int',
            'description' => 'nullable|min:3|max:200',
            'category_id' => 'nullable|int',
            'size_id' => 'nullable|int',
            'series' => 'nullable|string'
        ]);

        $test->update($request->all());
    }

    public function destroyTest($id)
    {
        $Test = EquipmentTest::findOrFail($id);

        $Test->delete();
    }

    public function price(Request $request)
    {
        $equipment_categories = EquipmentCategories::all();
        $equipment_location = EquipmentLocation::all();
        $contragents = Contragents::all();
        $perPage = $request->input('perPage');
        $prices = EquipmentPrice::with(['category', 'size', 'contragent', 'directory'])->where('archive', 0)->paginate($perPage);
        $archivedPrices = EquipmentPrice::with(['category', 'size', 'contragent', 'directory'])->where('archive', 1)->paginate($perPage);

        $prices->getCollection()->transform(function ($sale) {
            if (isset($sale->directory['files'])) {
                $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
            }
            return $sale;
        });

        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $series = $request->query('series');

        // Retrieve equipment sizes for the given category
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();

        // Count equipment by categories
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $equipment_categories_counts[$category->id] = Equipment::where('category_id', $category->id)->count();
        }

        // Count equipment by sizes
        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $equipment_sizes_counts[$size->id] = Equipment::where('size_id', $size->id)->count();
        }

        // Fetch equipment with category and size names
        $equipment = Equipment::with(['category', 'size'])
            ->where('category_id', $categoryId)
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->when($series, function ($query, $series) {
                return $query->where('series', $series);
            })
            ->get();



        return Inertia::render('Equip/Price', [
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'contragents' => $contragents,
            'prices' => $prices,
            'archivedPrices' => $archivedPrices
        ]);
    }

    public function storePrice(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => "required|int",
            'size_id' => "required|int",
            'contragent_id' => "required|int",
            'store_date' => "required|date",
            'notes' => "required|string",
            'store_price' => "required|int",
            'operation_price' => "required|int",
            'archive' => "required|boolean",
        ]);

        EquipmentPrice::where('category_id', $validatedData['category_id'])
            ->where('size_id', $validatedData['size_id'])
            ->where('contragent_id', $validatedData['contragent_id'])
            ->where('archive', false)
            ->update(['archive' => true]);


        EquipmentPrice::create(array_merge($validatedData, ['archive' => false]));
    }

    public function updatePrice(Request $request, $id)
    {

        $price = EquipmentPrice::findOrFail($id);

        $validatedData = $request->validate([
            'contragent_id' => "nullable|int",
            'store_date' => "nullable|date",
            'notes' => "nullable|string",
            'store_price' => "nullable|int",
            'operation_price' => "nullable|int",
            'archive' => "nullable|boolean",
        ]);

        $price->update($validatedData);
    }
    public function destroyPrice($id)
    {
        $price = EquipmentPrice::findOrFail($id);

        $price->delete();
    }



    public function storeHyperlinkPrice(Request $request, $id)
    {
        $request->validate([

            'hyperlink' => 'required|string'
        ]);

        $repair = EquipmentPrice::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $equipment_categories = EquipmentCategories::all();
        $equipment_sizes = EquipmentSize::all();
        $equipment_location = EquipmentLocation::all();


        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        // Count the number of equipment per size
        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }
        $categoryId = $request->query('category_id', 1); // Default to category_id = 1 if not provided
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();

        return Inertia::render('Equip/Create', [
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts
        ]);
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20'
        ]);

        EquipmentLocation::create($request->all());
    }
    public function deleteLocation($id)
    {

        $location = EquipmentLocation::findOrFail($id);

        $location->delete();


        return redirect()->route('equip.index')->with('success', 'Локация успешно удалена.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'manufactor' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'size_id' => 'required|integer',
            'series' => 'required|string',
            'manufactor_date' => 'nullable|date',
            'status' => ['nullable', 'string', 'in:new,good,satisfactory,bad,off'],
            'notes' => 'nullable|string',
            'price' => 'nullable|integer',
            'commentary' => 'nullable|string',
            'length' => 'nullable|string',
            'operating' => 'nullable|string',

        ]);

        $existingEquipment = Equipment::where('series', $request->input('series'))->first();

        if ($existingEquipment) {
            return redirect()->back()->withErrors(['series' => 'Оборудование с такой серией уже существует.']);
        }

        switch ($request->input('category_id')) {
            case 1: // ВЗД
                $extraData = $request->validate([
                    'zahodnost' => 'nullable|string',
                    'stator_rotor' => 'nullable|string',
                    'narabotka_ds' => 'nullable|string',
                    'dlina_ds' => 'nullable|string',
                ]);
                $validatedData = array_merge($validatedData, $extraData); // Merge extra data
                break;
            case 2:
                break;
            case 3: // ФД
            case 5: // ПК
            case 4: // ПК
            case 7:
                $extraData = $request->validate([
                    'rezbi' => 'nullable|string',
                    'length_rezba' => 'nullable|string',
                    'diameter' => 'nullable|string',
                ]);
                $validatedData = array_merge($validatedData, $extraData); // Merge extra data
                break;
        }


        $equipment = Equipment::create($validatedData);


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
    public function edit(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment_categories = EquipmentCategories::all();
        $equipment_sizes = EquipmentSize::all();
        $equipment_location = EquipmentLocation::all();


        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();


        return Inertia::render('Equip/Edit', [
            'equipment' => $equipment,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'manufactor' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'size_id' => 'required|integer',
            'series' => 'nullable|string',
            'manufactor_date' => 'nullable|date',
            'status' => ['nullable', 'string', 'in:new,good,satisfactory,bad,off'],
            'notes' => 'nullable|string',
            'price' => 'nullable|integer',
            'commentary' => 'nullable|string',
            'length' => 'nullable|string',
            'operating' => 'nullable|string',

        ]);
        $equipment = Equipment::findOrFail($id);

        switch ($request->input('category_id')) {
            case 1: // ВЗД
                $extraData = $request->validate([
                    'zahodnost' => 'nullable|string',
                    'stator_rotor' => 'nullable|string',
                    'narabotka_ds' => 'nullable|string',
                    'dlina_ds' => 'nullable|string',
                ]);
                $validatedData = array_merge($validatedData, $extraData);
                break;
            case 2:
                break;
            case 3: // ФД
            case 5: // ПК
            case 4: // ПК
            case 7:
                $extraData = $request->validate([
                    'rezbi' => 'nullable|string',
                    'length_rezba' => 'nullable|string',
                    'diameter' => 'nullable|string',
                ]);
                $validatedData = array_merge($validatedData, $extraData);
                break;
        }


        $equipment->update($validatedData);


        return redirect()->route('equip.index')->with('success', 'Оборудование успешно обновлено.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipment_item = Equipment::findOrFail($id);

        $equipment_item->delete();
    }

    public function changeLocation(Request $request)
    {

        $request->validate([
            'id' => 'required|integer',
            'locationId' => 'required|integer',
        ]);

        $id = $request->input('id');
        $locationId = $request->input('locationId');

        try {
            $equipment = Equipment::findOrFail($id);
            $location = EquipmentLocation::findOrFail($locationId);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Equipment or Location not found'], 404);
        }

        $equipment->location_id = $location->id;
        $equipment->save();
    }

    public function move(Request $request)
    {
        $sizeId = $request->query('size_id');
        $series = $request->query('series');
        $equipment_locations = EquipmentLocation::all();
        $categoryId = $request->query('category_id', 1);
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $equipment_categories = EquipmentCategories::all();
        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $categoryIDForCount = $category->id;
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->count();
        }



        $equipment_series = Equipment::when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->pluck('series');

        $equipment_location = Equipment::when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->where('size_id', $sizeId);
            })->when($series, function ($query, $series) {
                return $query->where('series', $series);
            })
            ->value('location_id');

        $equipment_location_found = EquipmentLocation::where('id', $equipment_location)->first();



        $equipment_id = Equipment::where('category_id', $categoryId)

            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->where('series', $series)
            ->with('directory')->get()->map(function ($sale) {
                if (isset($sale->directory['files'])) {
                    $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
                }
                return $sale;
            })->pluck('id');


        $equipment_moves = EquipmentMove::where('equipment_id', $equipment_id)->get();


        return Inertia::render('Equip/Move', [
            'equipmentSeries' => $equipment_series,
            'equipment_locations' => $equipment_locations,
            'equipment_moves' => $equipment_moves,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_location_found' => $equipment_location_found
        ]);
    }

    public function storeMove(Request $request)
    {
        try {
            $request->validate([
                'send_date' => 'required|date',
                'from' => 'nullable|int',
                'to' => 'nullable|int',
                'reason' => 'nullable|int',
                'expense' => 'nullable|min:3|max:200',
                'category_id' => 'required|int',
                'size_id' => 'required|int',
                'series' => 'required|string',
            ]);

            $foundedEquipment = Equipment::where('category_id', $request->category_id)
                ->where('size_id', $request->size_id)
                ->where('series', $request->series)
                ->first();

            if (!$foundedEquipment) {
                return back()->with('error', 'Оборудование не найдено.');
            }

            if ($request->to === $foundedEquipment->location_id) {
                return back()->with('error', 'Выбраны одинаковые локации.');
            }

            EquipmentMove::create(array_merge($request->all(), [
                'equipment_id' => $foundedEquipment->id,
            ]));

            return back()->with('message', 'Запись успешно добавлена.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
