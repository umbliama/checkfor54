<?php

namespace App\Http\Controllers;

use App\Models\ContrDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Contragents;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Events\NotificationCountUpdated;
use App\Models\User;


class ContragentsController extends Controller
{

    public function index(Request $request)
    {
        $searchTerm = $request->query('query');
        $perPage = $request->query('perPage', 10);

        $query = Contragents::query()->with('directory.files');

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%$searchTerm%")
                    ->orWhere('inn', 'LIKE', "%$searchTerm%")
                    ->orWhere('contact_person', 'LIKE', "%$searchTerm%")
                    ->orWhere('contact_person_phone', 'LIKE', "%$searchTerm%")
                    ->orWhere('contact_person_email', 'LIKE', "%$searchTerm%")
                    ->orWhere('notes', 'LIKE', "%$searchTerm%");

                if (strtolower($searchTerm) === 'активный') {
                    $query->orWhere('status', 1);
                } elseif (strtolower($searchTerm) === 'неактивный' || strtolower($searchTerm) === 'не активный') {
                    $query->orWhere('status', 0);
                }
            });
        }

        $contragents = $query->paginate($perPage);

        $contragents_customers = Contragents::where('customer', 1)->paginate($perPage);
        $contragents_suppliers = Contragents::where('supplier', 1)->paginate($perPage);

        $contragents_count = Contragents::count();
        $contragents_customer_count = Contragents::where('customer', 1)->count();
        $contragents_supplier_count = Contragents::where('supplier', 1)->count();

        $contragents->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });

        $contragents_customers->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });

        $contragents_suppliers->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });
        $contragents->getCollection()->transform(function ($contragent) {
            return $contragent->append('legal_statuses');
        });

        $contragents_customers->getCollection()->transform(function ($contragent) {
            return $contragent->append('legal_statuses');
        });

        $contragents_suppliers->getCollection()->transform(function ($contragent) {
            return $contragent->append('legal_statuses');
        });


        $countries = Contragents::getCountryMapping();
        $legalStatuses = Contragents::getLegalMapping();
        return Inertia::render('Contragents/Index', [
            'contragents' => $contragents,
            'contragents_count' => $contragents_count,
            'countries' => $countries,
            'contragents_customer_count' => $contragents_customer_count,
            'contragents_supplier_count' => $contragents_supplier_count,
            'contragents_customers' => $contragents_customers,
            'contragents_suppliers' => $contragents_suppliers,
            'legalStatuses' => $legalStatuses
        ]);
    }
    public function create()
    {
        $countries = Contragents::getCountryMapping();
        $legalStatuses = Contragents::getLegalMapping();


        return Inertia::render('Contragents/Create', ['countries' => $countries, 'legalStatuses' => $legalStatuses]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'inn' => 'required|string',
                'agentTypeLegal' => 'required|string',
                'country' => 'required|string',
                'status' => 'nullable|boolean',
            ]);
    
            $contragent = Contragents::create($validatedData);
            $user_id = Auth::id();
    
            $notification = Notification::create([
                'type' => 'Создан новый контрагент',
                'data' => ['name' => $contragent->name],
                'created_by' => $user_id,
            ]);
    
            $this->sendNotificationToUsers($notification, $user_id);
    
            return back()->with('message', "Контрагент '{$contragent->name}' успешно создан.");
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
    

    public function edit($id)
    {
        $contragent = Contragents::with('documents')->whereNotNull("user_id")->findOrFail($id);

        $groupedDocuments = $contragent->documents->map(function ($document) {
            $types = ['contracts', 'financial', 'transport', 'commercials', 'adddocs'];

            foreach ($types as $type) {
                $document->$type = !empty($document->$type) ? json_decode($document->$type, true) : [];
            }

            return $document;
        })->reduce(function ($carry, $document) {
            foreach (['contracts', 'financial', 'transport', 'commercials', 'adddocs'] as $type) {
                if (!empty($document->$type)) {
                    $carry[$type] = array_merge($carry[$type] ?? [], $document->$type);
                }
            }
            return $carry;
        }, []);

        $contragent->documents = $groupedDocuments;
        $countries = Contragents::getCountryMapping();
        $legalStatuses = Contragents::getLegalMapping();

        return Inertia::render('Contragents/Edit', ['contragent' => $contragent, 'legalStatuses' => $legalStatuses, 'countries' => $countries]);
    }
    public function update(Request $request, $id)
    {
        try {
            $contragent = Contragents::findOrFail($id);
            $user_id = Auth::id();
    
            $validated = $request->validate([
                'name' => 'nullable|string',
                'inn' => 'nullable|string',
                'status' => 'nullable|boolean',
            ]);
    
            $contragent->update($validated);
    
            // 🔹 Создаём уведомление
            $notification = Notification::create([
                'type' => 'Обновлён контрагент',
                'data' => ['name' => $contragent->name],
                'created_by' => $user_id,
            ]);
    
            // 🔹 Добавляем уведомление в `notification_reads`
            $this->sendNotificationToUsers($notification, $user_id);
    
            return back()->with('message', "Контрагент '{$contragent->name}' успешно обновлён.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $contragent = Contragents::with('documents')->whereNotNull("user_id")->findOrFail($id);

        $groupedDocuments = $contragent->documents->map(function ($document) {
            $types = ['contracts', 'financial', 'transport', 'commercials', 'adddocs'];

            foreach ($types as $type) {
                $document->$type = !empty($document->$type) ? json_decode($document->$type, true) : [];
            }

            return $document;
        })->reduce(function ($carry, $document) {
            foreach (['contracts', 'financial', 'transport', 'commercials', 'adddocs'] as $type) {
                if (!empty($document->$type)) {
                    $carry[$type] = array_merge($carry[$type] ?? [], $document->$type);
                }
            }
            return $carry;
        }, []);

        $contragent->documents = $groupedDocuments;
        $contragent->append('formatted_country');
        $contragent->append('legal_statuses');

        $countries = Contragents::getCountryMapping();
        $legalStatuses = Contragents::getLegalMapping();

        return Inertia::render('Contragents/Show', ['contragent' => $contragent, 'countries' => $countries, 'legalStatuses' => $legalStatuses]);
    }

    public function destroyAvatar($id)
    {
        try {
            $contragent = Contragents::findOrFail($id);

            if ($contragent->avatar) {
                $avatarPath = $contragent->avatar;

                if (Storage::exists($avatarPath)) {
                    Storage::delete($avatarPath);
                }

                $contragent->avatar = null;
                $contragent->save();

            }
            return back()->with('message', 'Логотип удален');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $contragent = Contragents::findOrFail($id);
            $contragentName = $contragent->name;
            $user_id = Auth::id();
    
            $contragent->delete();
    
            // 🔹 Создаём уведомление
            $notification = Notification::create([
                'type' => 'Удалён контрагент',
                'data' => ['name' => $contragentName],
                'created_by' => $user_id,
            ]);
    
            // 🔹 Добавляем уведомление в `notification_reads`
            $this->sendNotificationToUsers($notification, $user_id);
    
            return back()->with('message', "Контрагент '{$contragentName}' удалён.");
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
    
    public function deleteDocumentFileByContragent(Request $request)
    {
        $contragentId = $request->query('contragentId');
        $fileId = $request->query('fileId');

        $document = ContrDocuments::where('contragent_id', $contragentId)
            ->where('id', $fileId)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found for this contragent.'], 404);
        }

        $filePath = public_path($document->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $document->delete();

        return back()->with('message', 'Файл успешно удален');
    }

    public function editKP(Request $request)
    {
        $id = $request->input('fileId');
        $notes = $request->input('notes');
        $status = $request->input('status');
        $document = ContrDocuments::find($id);

        if ($document) {
            $document->notes = $notes;
            $document->status = $status;
            $document->save();

            return back()->with('message', 'Документ обновлен.');
        } else {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    public function storeHyperLink(Request $request, $id)
    {
        $request->validate([
            'hyperlink' => 'required|string'
        ]);
        $contragent = Contragents::find($id);

        if (!$contragent) {
            return response()->json(['error' => 'contragent not found'], 404);
        }

        $contragent->hyperlink = $request->input('hyperlink');

        $contragent->save();
    }
    public function deleteHyperLink($id)
    {
        try {

            $contragent = Contragents::find($id);

            $contragent->hyperlink = null;

            $contragent->save();

            return back()->with('message', 'Ссылка удалена');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());

        }
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
