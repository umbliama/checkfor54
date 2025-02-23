<?php
namespace App\Http\Controllers;

use App\Events\NotificationCountUpdated;
use App\Models\Column;
use App\Models\Contragents;
use App\Models\ContrSale;
use App\Models\Equipment;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\Sale;
use App\Models\SaleContragent;
use App\Models\SaleEquip;
use App\Models\SaleExtra;
use App\Models\SaleSub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with([
            'saleEquipment.equipment',
            'saleEquipment.equipment.category',
            'saleEquipment.equipment.size',
            'saleEquipment.equipment.directory',
            'saleEquipment.subequipment',
            'saleEquipment.subequipment.equipment',
            'saleEquipment.subequipment.equipment.category',
            'saleEquipment.subequipment.equipment.size',
            'saleEquipment.subequipment.equipment.directory',
            'directory',
        ])->get();

        $sales->transform(function ($sale) {
            if (isset($sale->directory['files'])) {
                $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
            }
            return $sale;
        });

        $groupedSales = $sales->groupBy('contragent_id');

        $groupedSales = $groupedSales->map(function ($sales, $contragentId) {
            $saleContragents = SaleContragent::where('contragent_id', $contragentId)->get();

            $contragent = Contragents::with('directory.files')->find($contragentId);

            return [
                'sales'           => $sales->map(function ($sale) {
                    if (isset($sale->directory['files'])) {
                        $sale->directory['files'] = json_decode($sale->directory['files'], true) ?? [];
                    }
                    return $sale;
                }),
                'contragent_data' => $saleContragents->map(function ($saleContragent) use ($contragent) {
                    return [
                        'contragent_id' => $saleContragent->contragent_id,
                        'shipping_date' => $saleContragent->shipping_date,
                        'commentary'    => $saleContragent->commentary,
                        'name'     => $contragent?->name ?? null, 
                        'hyperlink'     => $contragent?->hyperlink ?? null, 
                        'directory'     => $contragent?->directory ?? null,
                    ];
                }),
            ];
        });

        $contragents_names = Contragents::all();
        $perPage           = 10;
        $currentPage       = request()->get('page', 1);
        $pagedData         = $groupedSales->forPage($currentPage, $perPage);

        $paginatedSales = new LengthAwarePaginator(
            $pagedData,
            $groupedSales->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $saleStatuses  = Sale::getStatusesMapping();
        $extraServices = Sale::getExtraServices();

        return Inertia::render('Sale/Index', [
            'sales'             => $paginatedSales,
            'saleStatuses'      => $saleStatuses,
            'extraServices'     => $extraServices,
            'contragents_names' => $contragents_names,
        ]);
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
        $contragents   = Contragents::with(['documents'])->get();
        $saleStatuses  = Sale::getStatusesMapping();
        $extraServices = Sale::getExtraServices();

        return Inertia::render('Sale/Create', ['contragents' => $contragents, 'saleStatuses' => $saleStatuses, "extraServices" => $extraServices]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $full_income = 0;
        $request->validate([
            'contragent_id'                              => 'required|int',
            'sale_number'                                => "required|string",
            'sale_date'                                  => "required|date",
            'status'                                     => "in:credit,full,pred",
            'commentary'                                 => 'nullable|string',
            'equipment'                                  => 'required|array',
            'equipment.*.equipment_id'                   => 'nullable|int|exists:equipment,id',
            'equipment.*.commentary'                     => 'nullable|string',
            'equipment.*.shipping_date'                  => 'nullable|date',
            'equipment.*.price'                          => 'nullable|numeric',
            'equipment.*.subEquipment'                   => 'array|nullable',
            'equipment.*.subEquipment.*.subequipment_id' => 'nullable|int',
            'equipment.*.subEquipment.*.shipping_date'   => 'nullable|date',
            'equipment.*.subEquipment.*.commentary'      => 'nullable|string',
            'equipment.*.subEquipment.*.price'           => 'nullable|numeric',

        ]);

        $sale = Sale::create($request->only([
            'contragent_id',
            'sale_number',
            'sale_date',
            'status',
            'price',
            'commentary',
        ]));

        foreach ($request->equipment as $equipmentData) {
            $saleEquipment = SaleEquip::create([
                'sale'          => $sale->id,
                'equipment_id'  => $equipmentData['equipment_id'],
                'shipping_date' => $equipmentData['shipping_date'] ?? null,
                'commentary'    => $equipmentData['commentary'] ?? null,
                'price'         => $equipmentData['price'] ?? null,
            ]);

            $full_income += $equipmentData['price'];
            if (! empty($equipmentData['subEquipment'])) {
                foreach ($equipmentData['subEquipment'] as $subEquipmentData) {
                    SaleSub::create([
                        'equipment_id'      => $subEquipmentData['equipment_id'],
                        'sale_id'           => $sale->id,
                        'sale_equipment_id' => $saleEquipment->id,
                        'shipping_date'     => $subEquipmentData['shipping_date'],
                        'commentary'        => $subEquipmentData['commentary'] ?? null,
                        'price'             => $subEquipmentData['price'] ?? null,
                    ]);
                    $full_income += $subEquipmentData['price'];
                }
            }
        }

        foreach ($request->extraServices as $extraService) {
            SaleExtra::create([
                'shipping_date' => $extraService['shipping_date'],
                'sale_id'       => $sale->id,
                'type'          => $extraService['item'],
                'commentary'    => $extraService['commentary'],
                'price'         => $extraService['price'],
            ]);
            $full_income += $extraService['price'];
        }

        foreach (User::all() as $user) {
            Notification::create([
                'type'    => 'Создан новая продажа №' . $sale->id,
                'data'    => ['equipment_id' => $sale->equipment_id],
                'user_id' => $user->id,
            ]);
        }

        $sale->price = $full_income;

        $sale->save();

        SaleContragent::create([
            'contragent_id' => $sale->contragent_id,
        ]);
        ContrSale::create([
            'contragent_id' => $sale->contragent_id,
            'sale_id'       => $sale->id,
        ]);

        $notification = Notification::create([
            'type'       => "Пользователь {$user->name} создал новую аренду",
            'data'       => ['service_number' => $sale->sale_number],
            'created_by' => $user->id,
        ]);

        $this->sendNotificationToUsers($notification, $user->id);

        return redirect()->route('sale.index')->with('success', 'Service created successfully.');

    }

    public function createIncident($id)
    {
        $user_id       = Auth::id();
        $sale          = Sale::findOrFail($id);
        $equipment     = Equipment::findOrFail($sale->equipment_id)->value('id');
        $contragent_id = Contragents::findOrFail($sale->contragent_id)->value('id');
        $position      = Column::max('position') + 1;
        $column        = Column::create(['position' => $position, 'type' => 'adv']);
        Notification::create([
            'type'    => 'Создан новый инцидент',
            'data'    => ['position' => $position],
            'user_id' => $user_id,
        ]);

        $position = $column->blocks()->max('position') + 1;

        $block = $column->blocks()->create([
            'type'          => 'customer',
            'contragent_id' => $contragent_id,
            'equipment_id'  => $equipment,
            'position'      => $position,
        ]);
        if ($sale->subequiment_id !== null) {
            $block_subequipment = $block->subequipment()->create([
                'block_id'        => $block->id,
                'subequipment_id' => $sale->subequipment_id,
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
        $sale          = Sale::findOrFail($id);
        $contragents   = Contragents::all();
        $saleStatuses  = Sale::getStatusesMapping();
        $subsales      = SaleSub::where('sale_id', '=', $sale->id)->with(['equipment', 'equipment.category', 'equipment.size'])->get();
        $saleEquip     = SaleEquip::where('sale', $sale->id)->with('equipment', 'equipment.category', 'equipment.size', 'subequipment', 'subequipment.equipment', 'subequipment.equipment.size', 'subequipment.equipment.category')->get();
        $extraServices = SaleExtra::where('sale_id', $sale->id)->get()->map(function ($service) {
            $serviceMapping = Sale::getExtraServices();
            $service->value = $serviceMapping[$service->type] ?? $service->type;
            return $service;
        });
        return Inertia::render('Sale/Edit', [
            'sale'          => $sale,
            'contragents'   => $contragents,
            'saleEquip'     => $saleEquip,
            'extraServices' => $extraServices,
            'saleStatuses'  => $saleStatuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'contragent_id'                              => 'nullable|int',
            'sale_number'                                => "nullable|string",
            'sale_date'                                  => "nullable|date",
            'status'                                     => "in:credit,full,pred",
            'commentary'                                 => 'nullable|string',
            'equipment'                                  => 'nullable|array',
            'equipment.*.equipment_id'                   => 'nullable|int|exists:equipment,id',
            'equipment.*.commentary'                     => 'nullable|string',
            'equipment.*.shipping_date'                  => 'nullable|date',
            'equipment.*.price'                          => 'nullable|numeric',
            'equipment.*.subEquipment'                   => 'array|nullable',
            'equipment.*.subEquipment.*.subequipment_id' => 'nullable|int',
            'equipment.*.subEquipment.*.shipping_date'   => 'nullable|date',
            'equipment.*.subEquipment.*.commentary'      => 'nullable|string',
            'equipment.*.subEquipment.*.price'           => 'nullable|numeric',

        ]);

        $sale = Sale::findOrFail($id);
        $sale->update($request->only([
            'contragent_id',
            'sale_number',
            'sale_date',
            'status',
            'price',
            'commentary',
        ]));

        foreach ($request->equipment as $equipmentData) {
            $saleEquipment = SaleEquip::where('sale', $sale->id)
                ->where('equipment_id', $equipmentData['equipment_id'])
                ->first();
            if ($saleEquipment) {

                $saleEquipment->update([
                    'sale'          => $sale->id,
                    'equipment_id'  => $equipmentData['equipment_id'],
                    'shipping_date' => $equipmentData['shipping_date'] ?? null,
                    'commentary'    => $equipmentData['commentary'] ?? null,
                    'price'         => $equipmentData['price'] ?? null,
                ]);
            } else {
                $saleEquipment = SaleEquip::create([
                    'sale'          => $sale->id,
                    'equipment_id'  => $equipmentData['equipment_id'],
                    'shipping_date' => $equipmentData['shipping_date'] ?? null,
                    'commentary'    => $equipmentData['commentary'] ?? null,
                    'price'         => $equipmentData['price'] ?? null,
                ]);
            }

            if (! empty($equipmentData['subEquipment'])) {
                foreach ($equipmentData['subEquipment'] as $subEquipmentData) {
                    $saleSub = SaleSub::where('sale_equipment_id', $saleEquipment->id)
                        ->where('equipment_id', $subEquipmentData['equipment_id'])
                        ->first();
                    if ($saleSub) {
                        $saleSub->update([
                            'equipment_id'      => $subEquipmentData['equipment_id'],
                            'sale_id'           => $sale->id,
                            'sale_equipment_id' => $saleEquipment->id,
                            'shipping_date'     => $subEquipmentData['shipping_date'],
                            'commentary'        => $subEquipmentData['commentary'] ?? null,
                            'price'             => $subEquipmentData['price'] ?? null,
                        ]);
                    } else {
                        // If no existing SaleSub, create a new one
                        SaleSub::create([
                            'equipment_id'      => $subEquipmentData['equipment_id'],
                            'sale_id'           => $sale->id,
                            'sale_equipment_id' => $saleEquipment->id,
                            'shipping_date'     => $subEquipmentData['shipping_date'],
                            'commentary'        => $subEquipmentData['commentary'] ?? null,
                            'price'             => $subEquipmentData['price'] ?? null,
                        ]);
                    }
                }
            }
        }

        if ($request->extraServices) {
            foreach ($request->extraServices as $extraService) {
                if (isset($extraService['id'])) {
                    SaleExtra::where('id', $extraService['id'])->update([
                        'shipping_date' => $extraService['shipping_date'],
                        'type'          => $extraService['type'],
                        'commentary'    => $extraService['commentary'],
                        'price'         => $extraService['price'],
                    ]);
                } else {
                    SaleExtra::create([
                        'shipping_date' => $extraService['shipping_date'],
                        'sale_id'       => $sale->id,
                        'type'          => $extraService['item'],
                        'commentary'    => $extraService['commentary'],
                        'price'         => $extraService['price'],
                    ]);
                }
            }
        }

        $notification = Notification::create([
            'type'       => 'Обновлена аренда',
            'data'       => ['service_number' => $sale->sale_number],
            'created_by' => $user_id,
        ]);

        $this->sendNotificationToUsers($notification, $user_id);

    }

    public function setContSale(Request $request, $saleId)
    {
        $contrSale = ContrSale::where('sale_id', $saleId)->get();

        $commentary    = $request->input('commentary');
        $shipping_date = $request->input('shipping_date');

        $contrSale->commentary    = $commentary;
        $contrSale->shipping_date = $shipping_date;

        $contrSale->save();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $sale = Sale::findOrFail($id);

            $sale->delete();

            $notification = Notification::create([
                'type'       => 'Удалена аренда',
                'data'       => ['service_number' => $service_number],
                'created_by' => $user_id,
            ]);

            $this->sendNotificationToUsers($notification, $user_id);

            return back()->with('message', 'Продажа удалена');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeHyperlink(Request $request, $id)
    {
        $request->validate([

            'hyperlink' => 'required|string',
        ]);
        $repair = Sale::find($id);

        $repair->hyperlink = $request->input('hyperlink');
        $repair->save();

    }
    public function setContragentSaleData(Request $request)
    {

        try {
            $contragent_id = $request->input('contragent_id');
            $commentary    = $request->input('commentary');
            $shipping_date = $request->input('shipping_date');
            $found         = SaleContragent::where('contragent_id', $contragent_id)->first();
            if ($found) {
                $found->update([
                    'contragent_id' => $contragent_id,
                    'commentary'    => $commentary,
                    'shipping_date' => $shipping_date,
                ]);
            } else {
                SaleContragent::create([
                    'contragent_id' => $contragent_id,
                    'commentary'    => $commentary,
                    'shipping_date' => $shipping_date,
                ]);
            }

            return back()->with('message', 'Данные обновлены');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteEquipment(Request $request)
    {
        $id = $request->input('id');

        SaleEquip::find($id)->delete();
    }

    public function deleteSubEquipment(Request $request)
    {
        $id = $request->input('id');
        SaleSub::find($id)->delete();
    }

    private function sendNotificationToUsers($notification, $currentUserId)
    {
        $otherUserIds = User::where('id', '!=', $currentUserId)->pluck('id')->toArray();

        foreach ($otherUserIds as $userId) {
            // 🔹 Создаём запись о непрочитанном уведомлении
            NotificationRead::create([
                'notification_id' => $notification->id,
                'user_id'         => $userId,
                'read_at'         => null,
            ]);

            // 🔹 Подсчитываем количество непрочитанных уведомлений
            $unreadCount = NotificationRead::where('user_id', $userId)
                ->whereNull('read_at')
                ->count();

            \Log::info("Отправка NotificationCountUpdated для пользователя $userId", ['count' => $unreadCount]);

            event(new NotificationCountUpdated($unreadCount, $userId));
        }
    }

}
