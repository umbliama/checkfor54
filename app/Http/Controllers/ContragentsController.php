<?php

namespace App\Http\Controllers;

use App\Models\ContrDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Contragents;


class ContragentsController extends Controller
{

    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $query = Contragents::query();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('fullname', 'LIKE', "%$searchTerm%")
                    ->orWhere('inn', 'LIKE', "%$searchTerm%");
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

            $request->merge([
                // Boolean fields
                'supplier' => filter_var($request->input('supplier'), FILTER_VALIDATE_BOOLEAN),
                'customer' => filter_var($request->input('customer'), FILTER_VALIDATE_BOOLEAN),
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),

                // URL field
                'site' => filter_var($request->input('site'), FILTER_VALIDATE_URL) ?:
                    ($request->input('site') ? 'https://' . ltrim($request->input('site'), 'http://') : null),

                // Email fields
                'email' => $request->input('email') === 'null' ? null :
                    (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ?:
                        ($request->input('email') ? strtolower(trim($request->input('email'))) : null)),
                'contact_person_email' => $request->input('contact_person_email') === 'null' ? null :
                    (filter_var($request->input('contact_person_email'), FILTER_VALIDATE_EMAIL) ?:
                        ($request->input('contact_person_email') ? strtolower(trim($request->input('contact_person_email'))) : null)),
                // Text fields - trim and convert null strings to actual null
                'agentTypeLegal' => $request->input('agentTypeLegal') === 'null' ? null : trim($request->input('agentTypeLegal')),
                'country' => $request->input('country') === 'null' ? null : trim($request->input('country')),
                'name' => $request->input('name') === 'null' ? null : trim($request->input('name')),
                'fullname' => $request->input('fullname') === 'null' ? null : trim($request->input('fullname')),
                'inn' => $request->input('inn') === 'null' ? null : trim($request->input('inn')),
                'kpp' => $request->input('kpp') === 'null' ? null : trim($request->input('kpp')),
                'ogrn' => $request->input('ogrn') === 'null' ? null : trim($request->input('ogrn')),
                'reason' => $request->input('reason') === 'null' ? null : trim($request->input('reason')),
                'notes' => $request->input('notes') === 'null' ? null : trim($request->input('notes')),
                'commentary' => $request->input('commentary') === 'null' ? null : trim($request->input('commentary')),
                'group' => $request->input('group') === 'null' ? null : trim($request->input('group')),
                'bankname' => $request->input('bankname') === 'null' ? null : trim($request->input('bankname')),
                'bank_bik' => $request->input('bank_bik') === 'null' ? null : trim($request->input('bank_bik')),
                'bank_inn' => $request->input('bank_inn') === 'null' ? null : trim($request->input('bank_inn')),
                'bank_rs' => $request->input('bank_rs') === 'null' ? null : trim($request->input('bank_rs')),
                'bank_kpp' => $request->input('bank_kpp') === 'null' ? null : trim($request->input('bank_kpp')),
                'bank_ca' => $request->input('bank_ca') === 'null' ? null : trim($request->input('bank_ca')),
                'bank_commnetary' => $request->input('bank_commnetary') === 'null' ? null : trim($request->input('bank_commnetary')),
                'address' => $request->input('address') === 'null' ? null : trim($request->input('address')),
                'phone' => $request->input('phone') === 'null' ? null : trim($request->input('phone')),
                'contact_person' => $request->input('contact_person') === 'null' ? null : trim($request->input('contact_person')),
                'contact_person_phone' => $request->input('contact_person_phone') === 'null' ? null : trim($request->input('contact_person_phone')),
                'contact_person_notes' => $request->input('contact_person_notes') === 'null' ? null : trim($request->input('contact_person_notes')),
                'contact_person_commentary' => $request->input('contact_person_commentary') === 'null' ? null : trim($request->input('contact_person_commentary')),
            ]);
            $validatedData = $request->validate([
                'agentTypeLegal' => 'required|string',
                'country' => 'required|string',
                'name' => 'required|string',
                'fullname' => 'nullable|string',
                'inn' => 'required|string',
                'kpp' => 'nullable|string',
                'ogrn' => 'nullable|string',
                'reason' => 'nullable|string',
                'notes' => 'nullable|string',
                'commentary' => 'nullable|string',
                'group' => 'nullable|string',
                'bankname' => 'nullable|string',
                'bank_bik' => 'nullable|string',
                'bank_inn' => 'nullable|string',
                'bank_rs' => 'nullable|string',
                'bank_kpp' => 'nullable|string',
                'bank_ca' => 'nullable|string',
                'bank_commnetary' => 'nullable|string',
                'supplier' => 'nullable|boolean',
                'customer' => 'nullable|boolean',
                'address' => 'nullable|string',
                'site' => 'nullable|url',
                'phone' => 'nullable|string',
                'email' => 'nullable|email',
                'contact_person' => 'nullable|string',
                'contact_person_phone' => 'nullable|string',
                'contact_person_email' => 'nullable|email',
                'contact_person_notes' => 'nullable|string',
                'contact_person_commentary' => 'nullable|string',
                'status' => 'nullable|boolean',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=400,min_height=400',
                'contracts.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'commercials.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'transport.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'financial.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'adddocs.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            ]);

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('avatars'), $avatarName);
                $validatedData['avatar'] = 'avatars/' . $avatarName;
            }

            $contragent = Contragents::create($validatedData);

            $data = ['contragent_id' => $contragent->id];
            $docFields = ['commercials', 'contracts', 'transport', 'financial', 'adddocs'];

            foreach ($docFields as $field) {
                if ($request->hasFile($field)) {
                    $files = [];
                    foreach ($request->file($field) as $file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path("documents/{$contragent->id}/{$field}"), $fileName);
                        $files[] = "documents/{$contragent->id}/{$field}/" . $fileName;
                    }
                    $data[$field] = json_encode($files);
                }
            }

            ContrDocuments::create($data);

            return back()->with('message', 'Профиль компании сохранен.');
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

            $validated = $request->validate([
                'agentTypeLegal' => 'nullable',
                'country' => 'nullable',
                'name' => 'nullable',
                'fullname' => 'nullable',
                'inn' => 'nullable',
                'kpp' => 'nullable',
                'ogrn' => 'nullable',
                'reason' => 'nullable',
                'notes' => 'nullable',
                'commentary' => 'nullable',
                'group' => 'nullable',
                'bankname' => 'nullable',
                'bank_bik' => 'nullable',
                'bank_inn' => 'nullable',
                'bank_rs' => 'nullable',
                'bank_kpp' => 'nullable',
                'bank_ca' => 'nullable',
                'bank_commnetary' => 'nullable',
                'supplier' => 'nullable',
                'customer' => 'nullable',
                'address' => 'nullable',
                'site' => 'nullable',
                'phone' => 'nullable',
                'email' => 'nullable',
                'contact_person' => 'nullable',
                'contact_person_phone' => 'nullable',
                'contact_person_email' => 'nullable',
                'contact_person_notes' => 'nullable',
                'contact_person_commentary' => 'nullable',
                'status' => 'nullable',
                'avatar' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                'contracts.*' => 'required|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'transport.*' => 'required|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'financial.*' => 'required|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'commercials.*' => 'required|file|mimes:pdf,doc,docx,zip,txt|max:5120',
                'adddocs.*' => 'required|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            ]);

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('avatars'), $avatarName);
                $validated['avatar'] = 'avatars/' . $avatarName;
            } else {
                unset($validated['avatar']);
            }

            $contrDocs = ContrDocuments::where('contragent_id', $id)->first();
            $existingFiles = $contrDocs ? $contrDocs->toArray() : [];

            $docFields = ['commercials', 'contracts', 'transport', 'financial', 'adddocs'];
            $data = ['contragent_id' => $contragent->id, 'user_id' => Auth::id()];

            foreach ($docFields as $field) {
                $newFiles = [];

                if ($contrDocs && !empty($existingFiles[$field])) {
                    $newFiles = json_decode($existingFiles[$field], true) ?? [];
                }

                if ($request->hasFile($field)) {
                    foreach ($request->file($field) as $file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path("documents/{$contragent->id}/{$field}"), $fileName);
                        $newFiles[] = "documents/{$contragent->id}/{$field}/" . $fileName;
                    }
                }

                if (!empty($newFiles)) {
                    $data[$field] = json_encode($newFiles);
                }
            }

            if ($contrDocs && $contrDocs->user_id === Auth::id()) {
                $contrDocs->update($data);
            } else {
                ContrDocuments::create($data);
            }

            $contragent->update($validated);

            return back()->with('message', "Контрагент успешно обновлен");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function show($id)
    {
        $contragent = Contragents::findOrFail($id);
        $contragent->append('formatted_country');
        $contragent->append('legal_statuses');

        $countries = Contragents::getCountryMapping();
        $legalStatuses = Contragents::getLegalMapping();

        return Inertia::render('Contragents/Show', ['contragent' => $contragent, 'countries' => $countries, 'legalStatuses' => $legalStatuses]);
    }



    public function destroy($id)
    {
        $contragent = Contragents::findOrFail($id);

        $contragent->delete();
    }
    public function deleteDocumentFileByContragent(Request $request)
    {
        $contragentId = $request->query('contragentId');
        $fileName = $request->query('fileName');
        $document = ContrDocuments::where('contragent_id', $contragentId)->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found for this contragent.'], 404);
        }

        $docFields = ['commercials', 'contracts', 'transport', 'financial', 'adddocs'];
        $fileFound = false;

        foreach ($docFields as $field) {
            if (!empty($document->$field)) {
                $files = json_decode($document->$field, true) ?? [];
                $fileIndex = array_search($fileName, $files);
                if ($fileIndex !== false) {

                    $filePath = public_path($files[$fileIndex]);

                    if (file_exists($filePath)) {

                        unlink($filePath);

                        unset($files[$fileIndex]);
                        $document->$field = json_encode(array_values($files));
                        $document->save();
                        $fileFound = true;
                    }
                    break;
                }
            }
        }

        if (!$fileFound) {
            return response()->json(['message' => 'File not found in any document field.'], 404);
        }

        return back()->with('message', 'Файл успешно удален');
    }

}
