<?php
namespace App\Http\Controllers;

use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\EquipmentCategories;
use App\Models\EquipmentLocation;
use App\Models\EquipmentMove;
use App\Models\EquipmentPrice;
use App\Models\EquipmentRepair;
use App\Models\EquipmentSize;
use App\Models\EquipmentTest;
use App\Models\ServiceEquip;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\User;
use App\Events\NotificationCountUpdated;
use App\Models\ServiceSub;
use Exception;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage');
        $manufacturers = Equipment::distinct()->pluck('manufactor')->map(function ($manufactor) {
            return [
                'title' => $manufactor,
                'value' => $manufactor,
            ];
        })->toArray();

        $statusesList = [
            'new' => 'Новое',
            'good' => 'Хорошее',
            'satisfactory' => 'Удовлетворительно',
            'bad' => 'Плохое',
            'sold' => 'Продан',
            'off' => 'Списано',
            'unknown' => 'Неизвестный статус',
        ];

        $statusesArray = Equipment::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->map(function ($status) use ($statusesList) {
                return [
                    'title' => $statusesList[$status] ?? ucfirst($status),
                    'value' => $status,
                ];
            })->values()->toArray();

        $equipment_categories = EquipmentCategories::all();
        $equipment_location = EquipmentLocation::where('id', '!=', '-1')->get();
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $query = Equipment::query()->with(['directory','category','size'])->whereNotIn('status', ['deteled','sold','off']);
        $locationId = $request->query('location_id');
        $rentActive = $request->query('isRentActive');

        $serviceSubCount = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', 1);
        })
            ->whereHas('equipment', function ($query) use ($categoryId, $sizeId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($sizeId) {
                    $query->where('size_id', $sizeId);
                }
            })
            ->count();

        $serviceEquipCount = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })
            ->whereHas('equipment', function ($query) use ($categoryId, $sizeId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($sizeId) {
                    $query->where('size_id', $sizeId);
                }
            })
            ->count();

        $equipment_on_rent_count = $serviceSubCount + $serviceEquipCount;

        $activeEquipment = ServiceEquip::with(['services', 'serviceSubs.equipment'])
            ->whereHas('services', function ($query) {
                $query->where('active', 1);
            })
            ->get()
            ->flatMap(function ($serviceEquip) {
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
                        'type' => 'sub',
                        'category_id' => optional($sub->equipment)->category_id,
                        'size_id' => optional($sub->equipment)->size_id,
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
                    'type' => 'main',
                    'category_id' => optional($serviceEquip->equipment)->category_id,
                    'size_id' => optional($serviceEquip->equipment)->size_id,
                ];

                return collect([$mainEquip])->concat($subs);
            })
            ->values();

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
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)
                ->whereNotIn('status', ['deleted', 'off'])
                ->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $sizeIDForCount = $size->id;
            $equipment_sizes_counts[$sizeIDForCount] = Equipment::where('size_id', $sizeIDForCount)->whereNotIn('status', ['deleted', 'off'])->count();
        }

        $location_counts = [];
        foreach ($equipment_location as $location) {
            $locationIdCount = $location->id;
            if ($categoryId > 0) {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->where('category_id', $categoryId)->where('status','!=','deleted')->count();
            } else {
                $location_counts[$locationIdCount] = Equipment::where('location_id', $locationIdCount)->count();
            }
        }

        $in_way_count = Equipment::where('location_id', '-1')->where('category_id', $categoryId)->where('size_id', $sizeId)->count();


        if ($rentActive) {
            $query = Equipment::whereHas('serviceEquipment.services', function ($query) {
                $query->where('active', 1);
            })
                ->orWhereHas('serviceSubs.service', function ($query) { // Corrected relationship usage
                    $query->where('active', 1);
                })
                ->with([
                    'serviceEquipment' => function ($query) use ($categoryId, $sizeId) {
                        $query->whereHas('services', function ($subQuery) {
                            $subQuery->where('active', 1);
                        })->with([
                                    'serviceSubs' => function ($subQuery) use ($categoryId, $sizeId) {
                                        $subQuery->whereHas('service', function ($subSubQuery) {
                                            $subSubQuery->where('active', 1);
                                        })->with('equipment');

                                        if ($categoryId) {
                                            $subQuery->whereHas('equipment', function ($eqQuery) use ($categoryId) {
                                                $eqQuery->where('category_id', $categoryId);
                                            });
                                        }

                                        if ($sizeId) {
                                            $subQuery->whereHas('equipment', function ($eqQuery) use ($sizeId) {
                                                $eqQuery->where('size_id', $sizeId);
                                            });
                                        }
                                    }
                                ]);
                    },
                    'serviceSubs' => function ($query) use ($categoryId, $sizeId) {
                        $query->whereHas('service', function ($subQuery) {
                            $subQuery->where('active', 1);
                        })->with('equipment');

                        if ($categoryId) {
                            $query->whereHas('equipment', function ($eqQuery) use ($categoryId) {
                                $eqQuery->where('category_id', $categoryId);
                            });
                        }

                        if ($sizeId) {
                            $query->whereHas('equipment', function ($eqQuery) use ($sizeId) {
                                $eqQuery->where('size_id', $sizeId);
                            });
                        }
                    }
                ]);
        }




        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }


        $equipment = $query->paginate($perPage);

        $equipment->getCollection()->map(function ($equip) {
            if (isset($equip->directory['files'])) {
                $equip->directory['files'] = json_decode($equip->directory['files'], true) ?? [];
            }

            $income = ServiceEquip::where('equipment_id', $equip->id)->sum('income');
            $subincome = ServiceSub::where('equipment_id', $equip->id)->sum('income');

            $equip->income = $income;
            $equip->subincome = $subincome;
            
            $equip->used = $equip->used;


            return $equip;
        });


        return Inertia::render('Equip/Index', [
            'equipment' => $equipment,
            'manufacturers' => $manufacturers,
            'statusesArray' => $statusesArray,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'selectedCategory' => $categoryId,
            'location_counts' => $location_counts,
            'equipment_on_rent_count' => $equipment_on_rent_count,
            'activeEquipment' => $activeEquipment,
            'in_way_count' => $in_way_count
        ]);
    }
    public function storeHyperLink(Request $request, $id)
    {
        // Validate the hyperlink input
        $request->validate([
            'hyperlink' => 'required|string',
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
            'hyperlink' => 'required|string',
        ]);

        $repair = EquipmentRepair::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    public function storeTestHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string',
        ]);

        $repair = EquipmentTest::find($id);

        $repair->hyperlink = $request->input('hyperlink');

        $repair->save();
    }

    public function storeServiceHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string',
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
        $equipment_categories_counts = EquipmentRepair::groupBy('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->pluck('count', 'category_id');

        $equipment_sizes_counts = EquipmentRepair::groupBy('size_id')
            ->selectRaw('size_id, COUNT(*) as count')
            ->pluck('count', 'size_id');

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
            'equipment_sizes_counts' => $equipment_sizes_counts,
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

    public function closeRepair(Request $request)
    {
        try {
            $repair = EquipmentRepair::findOrFail($request->id);
            $request->validate([
                'on_repair_date' => 'required|string',
                'repair_date' => 'required|date',
                'location_id' => 'required|int',
                'expense' => 'required|int',
                'description' => 'required|min:3|max:200',
                'isLocked' => 'required|boolean',
            ]);

            $repair->update($request->all());

            return back()->with('message', 'Статус ремонта изменен');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function closeTest(Request $request)
    {
        try {
            $repair = EquipmentTest::findOrFail($request->id);
            $request->validate([
                'on_test_date' => 'required|string',
                'test_date' => 'required|date',
                'location_id' => 'required|int',
                'expense' => 'required|int',
                'description' => 'required|min:3|max:200',
                'isLocked' => 'required|boolean',
            ]);

            $repair->update($request->all());

            return back()->with('message', 'Статус испытания изменен');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
        $firstEquipment = $equipment->first();
        $equipment_repairs = EquipmentRepair::where('category_id', $categoryId)->where('size_id', $sizeId)->where('series', $series);
        $equipment_moves = EquipmentMove::where('category_id', $categoryId)->where('size_id', $sizeId)->where('series', $series)->with('directory.files')->get();

        $equipment_tests = Equipment::where('category_id', $categoryId)

            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->where('series', $series)
            ->get();


        return Inertia::render('Equip/Report', [
            'equipment_repairs' => $equipment_repairs,
            'equipment_location' => $equipment_location,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_series' => $equipment_series,
            'equipment_moves' => $equipment_moves,
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

        $equipment_categories_counts = EquipmentTest::groupBy('category_id')->selectRaw('category_id,COUNT(*) as count')->pluck('count', 'category_id');
        $equipment_sizes_counts = EquipmentTest::groupBy('size_id')->selectRaw('size_id,COUNT(*) as count')->pluck('count', 'size_id');
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
                'equipment_sizes' => $equipment_sizes,
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

        try {
            $test = EquipmentTest::findOrFail($id);
            $request->validate([
                'test_date' => 'nullable|date',
                'location_id' => 'nullable|int',
                'expense' => 'nullable|int',
                'description' => 'nullable|min:3|max:200',
                'category_id' => 'nullable|int',
                'size_id' => 'nullable|int',
                'series' => 'nullable|string',
            ]);

            $test->update($request->all());
            return back()->with('message', 'Испытание обновлено');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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

        $perPage = $request->input('perPage', 10);

        $categoryId = $request->query('category_id');
        $sizeId = $request->query('size_id');

        $prices = EquipmentPrice::with(['category', 'size', 'contragent', 'directory'])
            ->where('archive', 0)
            ->when($categoryId, function ($query, $categoryId) {
                return $query->whereHas('category', function ($q) use ($categoryId) {
                    $q->where('id', $categoryId);
                });
            })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->whereHas('size', function ($q) use ($sizeId) {
                    $q->where('id', $sizeId);
                });
            })
            ->paginate($perPage);

        $archivedPrices = EquipmentPrice::with(['category', 'size', 'contragent', 'directory'])
            ->where('archive', 1)
            ->when($categoryId, function ($query, $categoryId) {
                return $query->whereHas('category', function ($q) use ($categoryId) {
                    $q->where('id', $categoryId);
                });
            })
            ->when($sizeId, function ($query, $sizeId) {
                return $query->whereHas('size', function ($q) use ($sizeId) {
                    $q->where('id', $sizeId);
                });
            })
            ->paginate($perPage);

        $prices->getCollection()->transform(function ($sale) {
            if (isset($sale->directory['files'])) {
                $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
            }
            return $sale;
        });

        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();

        $equipment_categories_counts = [];
        foreach ($equipment_categories as $category) {
            $equipment_categories_counts[$category->id] = Equipment::where('category_id', $category->id)->count();
        }

        $equipment_sizes_counts = [];
        foreach ($equipment_sizes as $size) {
            $equipment_sizes_counts[$size->id] = Equipment::where('size_id', $size->id)->count();
        }

        $series = $request->query('series');
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
            'archivedPrices' => $archivedPrices,
        ]);
    }
    public function storePrice(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category_id' => "required|int",
                'size_id' => "required|int",
                'contragent_id' => "required|int",
                'store_date' => "required|date",
                'notes' => "nullable|string",
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

            return back()->with('success', 'Цена успешно установлена');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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

            'hyperlink' => 'required|string',
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
        $equipment_location = EquipmentLocation::where('id', '!=', '-1')->get();
        $ownershipArray = Equipment::getOwnershipOptions();

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
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();

        return Inertia::render('Equip/Create', [
            'equipment_categories' => $equipment_categories,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'ownershipArray' => $ownershipArray
        ]);
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
        ]);

        EquipmentLocation::create($request->all());
    }
    public function editLocation(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|max:20',
        ]);

        $location = EquipmentLocation::findOrFail($request->id);

        
        $location->update($request->all());
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
        $userId = Auth::id();
        $validatedData = $request->validate([
            'manufactor' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'size_id' => 'required|integer',
            'series' => 'required|string',
            'manufactor_date' => 'nullable|date',
            'status' => ['nullable', 'string', 'in:new,good,satisfactory,bad,off,unknown,sold'],
            'notes' => 'nullable|string',
            'price' => 'nullable|integer',
            'commentary' => 'nullable|string',
            'length' => 'nullable|string',
            'operating' => 'nullable|string',
            'ownership' => 'required|in:our,sub',

        ]);

        $existingEquipment = Equipment::where('series', $request->input('series'))->where('size_id',$request->input('size_id'))->first();

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

        $equipment = Equipment::create($validatedData);
        $equipment->load('category');
        $equipment->load('size');

        $category = $equipment->category;
        $size = $equipment->size;
        
        $notification = Notification::create([
            'type' => 'Создано новое оборудование '. $category->name . " " . $size->name . " " . $equipment->series,
            'data' => ['name' => $equipment],
            'created_by' => $userId,
        ]);

        $this->sendNotificationToUsers($notification, $userId);
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
        $ownershipArray = Equipment::getOwnershipOptions();

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
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'ownershipArray' => $ownershipArray
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
            'status' => ['nullable', 'string', 'in:new,good,satisfactory,bad,off,unknown,sold'],
            'notes' => 'nullable|string',
            'price' => 'nullable|integer',
            'commentary' => 'nullable|string',
            'length' => 'nullable|string',
            'operating' => 'nullable|string',
            'ownership' => 'nullable|in:our,sub',


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

        $existingEquipment = Equipment::where('series', $request->input('series'))->where('id', '!=',$id)->first();

        if ($existingEquipment) {
            return redirect()->back()->withErrors(['series' => 'Оборудование с такой серией уже существует.']);
        }
        $equipment->update($validatedData);

        return redirect()->route('equip.index')->with('success', 'Оборудование успешно обновлено.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $equipment->status = 'deleted';

            $equipment->save();

            return back()->with('message', 'Оборудование удалено');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function fakeDestroy(string $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $equipment->status = 'off';

            $equipment->save();

            return back()->with('message', 'Оборудование списано');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
        $equipment_categories_counts = EquipmentMove::groupBy('category_id')->selectRaw('category_id,COUNT(*) as count')->pluck('count', 'category_id');

        $equipment_sizes_counts = EquipmentMove::groupBy('size_id')->selectRaw('size_id,COUNT(*) as count')->pluck('count', 'size_id');

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
            ->with('directory.files.uploader')->get()->map(function ($sale) {
                if (isset($sale->directory['files'])) {
                    $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
                }
                return $sale;
            })->pluck('id');



        $equipment_moves = EquipmentMove::where('equipment_id', $equipment_id)->with('directory')->get();

        return Inertia::render('Equip/Move', [
            'equipmentSeries' => $equipment_series,
            'equipment_locations' => $equipment_locations,
            'equipment_moves' => $equipment_moves,
            'equipment_sizes' => $equipment_sizes,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_categories' => $equipment_categories,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_location_found' => $equipment_location_found,
        ]);
    }

    public function storeMove(Request $request)
    {
        try {
            $request->validate([
                'send_date' => 'required|date',
                'from' => 'nullable|int',
                'to' => 'nullable|int',
                'reason' => 'required|string',
                'expense' => 'required|min:3|max:200',
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

            $foundedEquipment->location_id = -1;

            $foundedEquipment->save();

            EquipmentMove::create(array_merge($request->all(), [
                'equipment_id' => $foundedEquipment->id,
            ]));

            return back()->with('message', 'Запись успешно добавлена.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateMove(Request $request, $id)
    {
        try {
            $request->validate([
                'send_date' => 'required|date',
                'from' => 'nullable|int',
                'to' => 'nullable|int',
                'reason' => 'nullable|string',
                'expense' => 'nullable|min:3|max:200',
            ]);

            $moveRecord = EquipmentMove::where('id', $id)->latest()->first();

            if (!$moveRecord) {
                return back()->with('error', 'Запись перемещения для данного оборудования не найдена.');
            }

            $equipment = Equipment::where('id', $moveRecord->equipment_id)->first();

            $equipment->location_id = $request->to;

            $equipment->save();

            $moveRecord->update(array_merge($request->all()));

            return back()->with('message', 'Запись успешно обновлена.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function closeMove(Request $request)
    {
        try {
            $repair = EquipmentMove::findOrFail($request->equipment_id);
            $request->validate([
                'send_date' => 'required|date',
                'from' => 'required|int',
                'to' => 'required|int',
                'departure_date' => 'required|date',
                'reason' => 'required|string',
                'expense' => 'required|min:1|max:200',
                'equipment_id' => 'required|exists:equipment,id',
                'isLocked' => 'required|int',
            ]);

            $repair->isLocked = $request->isLocked;
            $repair->save();

            return back()->with('message', 'Статус перемещения изменен');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyMove($id)
    {
        try {

            $move = EquipmentMove::findOrFail($id);

            $move->delete();

            return back()->with('message', 'Запись удалена');
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    public function archive(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage');
        $manufacturers = Equipment::distinct()->pluck('manufactor')->map(function ($manufactor) {
            return [
                'title' => $manufactor,
                'value' => $manufactor,
            ];
        })->toArray();

        $statusesList = [
            'new' => 'Новое',
            'good' => 'Хорошее',
            'satisfactory' => 'Удовлетворительно',
            'bad' => 'Плохое',
            'sold' => 'Продан',
            'off' => 'Списано',
            'unknown' => 'Неизвестный статус',
        ];

        $statusesArray = Equipment::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->map(function ($status) use ($statusesList) {
                return [
                    'title' => $statusesList[$status] ?? ucfirst($status),
                    'value' => $status,
                ];
            })->values()->toArray();

        $equipment_categories = EquipmentCategories::all();
        $equipment_location = EquipmentLocation::where('id', '!=', '-1')->get();
        $categoryId = $request->query('category_id', 1);
        $sizeId = $request->query('size_id');
        $equipment_sizes = EquipmentSize::where('category_id', $categoryId)->get();
        $query = Equipment::query()->with('directory')->whereIn('status',['sold','off','deleted']);
        $locationId = $request->query('location_id');
        $rentActive = $request->query('isRentActive');

        $serviceSubCount = ServiceSub::whereHas('service', function ($query) {
            $query->where('active', 1);
        })
            ->whereHas('equipment', function ($query) use ($categoryId, $sizeId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($sizeId) {
                    $query->where('size_id', $sizeId);
                }
            })
            ->count();

        $serviceEquipCount = ServiceEquip::whereHas('services', function ($query) {
            $query->where('active', 1);
        })
            ->whereHas('equipment', function ($query) use ($categoryId, $sizeId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($sizeId) {
                    $query->where('size_id', $sizeId);
                }
            })
            ->count();

        $equipment_on_rent_count = $serviceSubCount + $serviceEquipCount;

        $activeEquipment = ServiceEquip::with(['services', 'serviceSubs.equipment'])
            ->whereHas('services', function ($query) {
                $query->where('active', 1);
            })
            ->get()
            ->flatMap(function ($serviceEquip) {
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
                        'type' => 'sub',
                        'category_id' => optional($sub->equipment)->category_id,
                        'size_id' => optional($sub->equipment)->size_id,
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
                    'type' => 'main',
                    'category_id' => optional($serviceEquip->equipment)->category_id,
                    'size_id' => optional($serviceEquip->equipment)->size_id,
                ];

                return collect([$mainEquip])->concat($subs);
            })
            ->values();

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
            $equipment_categories_counts[$categoryIDForCount] = Equipment::where('category_id', $categoryIDForCount)
                ->whereNotIn('status', ['deleted', 'off'])
                ->count();
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

        $in_way_count = Equipment::where('location_id', '-1')->where('category_id', $categoryId)->where('size_id', $sizeId)->count();


        if ($rentActive) {
            $query = Equipment::whereHas('serviceEquipment.services', function ($query) {
                $query->where('active', 1);
            })
                ->orWhereHas('serviceSubs.service', function ($query) { // Corrected relationship usage
                    $query->where('active', 1);
                })
                ->with([
                    'serviceEquipment' => function ($query) use ($categoryId, $sizeId) {
                        $query->whereHas('services', function ($subQuery) {
                            $subQuery->where('active', 1);
                        })->with([
                                    'serviceSubs' => function ($subQuery) use ($categoryId, $sizeId) {
                                        $subQuery->whereHas('service', function ($subSubQuery) {
                                            $subSubQuery->where('active', 1);
                                        })->with('equipment');

                                        if ($categoryId) {
                                            $subQuery->whereHas('equipment', function ($eqQuery) use ($categoryId) {
                                                $eqQuery->where('category_id', $categoryId);
                                            });
                                        }

                                        if ($sizeId) {
                                            $subQuery->whereHas('equipment', function ($eqQuery) use ($sizeId) {
                                                $eqQuery->where('size_id', $sizeId);
                                            });
                                        }
                                    }
                                ]);
                    },
                    'serviceSubs' => function ($query) use ($categoryId, $sizeId) {
                        $query->whereHas('service', function ($subQuery) {
                            $subQuery->where('active', 1);
                        })->with('equipment');

                        if ($categoryId) {
                            $query->whereHas('equipment', function ($eqQuery) use ($categoryId) {
                                $eqQuery->where('category_id', $categoryId);
                            });
                        }

                        if ($sizeId) {
                            $query->whereHas('equipment', function ($eqQuery) use ($sizeId) {
                                $eqQuery->where('size_id', $sizeId);
                            });
                        }
                    }
                ]);
        }




        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }


        $equipment = $query->paginate($perPage);

        $equipment->getCollection()->map(function ($equip) {
            if (isset($equip->directory['files'])) {
                $equip->directory['files'] = json_decode($equip->directory['files'], true) ?? [];
            }

            $income = ServiceEquip::where('equipment_id', $equip->id)->sum('income');
            $subincome = ServiceSub::where('equipment_id', $equip->id)->sum('income');

            $equip->income = $income;
            $equip->subincome = $subincome;

            $equip->used = $equip->used;


            return $equip;
        });


        return Inertia::render('Equip/Archive', [
            'equipment' => $equipment,
            'manufacturers' => $manufacturers,
            'statusesArray' => $statusesArray,
            'equipment_categories' => $equipment_categories,
            'equipment_categories_counts' => $equipment_categories_counts,
            'equipment_sizes_counts' => $equipment_sizes_counts,
            'equipment_sizes' => $equipment_sizes,
            'equipment_location' => $equipment_location,
            'selectedCategory' => $categoryId,
            'location_counts' => $location_counts,
            'equipment_on_rent_count' => $equipment_on_rent_count,
            'activeEquipment' => $activeEquipment,
            'in_way_count' => $in_way_count
        ]);
    }

    private function sendNotificationToUsers($notification, $currentUserId)
    {
        $otherUserIds = User::where('id', '!=', $currentUserId)->pluck('id')->toArray();

        foreach ($otherUserIds as $userId) {
            // 🔹 Создаём запись о непрочитанном уведомлении
            NotificationRead::create([
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'read_at' => null,
            ]);

            // 🔹 Подсчитываем количество непрочитанных уведомлений
            $unreadCount = NotificationRead::where('user_id', $userId)
                ->whereNull('read_at')
                ->count();

            event(new NotificationCountUpdated($unreadCount, $userId));
        }
    }

}
