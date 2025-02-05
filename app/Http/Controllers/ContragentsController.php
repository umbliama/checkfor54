<?php

namespace App\Http\Controllers;

use App\Models\ContrDocuments;
use Illuminate\Http\Request;
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
        $request->validate([
            'agentTypeLegal' => 'required',
            'country' => 'required',
            'name' => 'required',
            'fullname' => 'nullable',
            'inn' => 'required',
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

            // Save the avatar path to the request data
            $requestData = $request->all();
            $requestData['avatar'] = 'avatars/' . $avatarName;
        } else {
            $requestData = $request->all();
        }




        $contragent = Contragents::create($requestData);

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

        return redirect()->route('contragents.edit', parameters: $contragent->id)
            ->with('success', 'Профиль компании сохранен.');
    }


    public function edit($id)
{
    $contragent = Contragents::with('documents')->findOrFail($id);

    $contragent->documents = $contragent->documents->map(function ($document) {
        if (isset($document->contracts)) {
            $document->contracts = json_decode($document->contracts, true) ?? [];
        }
        if (isset($document->financial)) {
            $document->financial = json_decode($document->financial, true) ?? [];
        }
        if (isset($document->transport)) {
            $document->transport = json_decode($document->transport, true) ?? [];
        }
        if (isset($document->commercials)) {
            $document->commercials = json_decode($document->commercials, true) ?? [];
        }
        if (isset($document->adddocs)) {
            $document->adddocs = json_decode($document->adddocs, true) ?? [];
        }

        $document->setRawAttributes($document->getAttributes());

        return $document;
    });

    $countries = Contragents::getCountryMapping();
    $legalStatuses = Contragents::getLegalMapping();

    return Inertia::render('Contragents/Edit', ['contragent' => $contragent, 'legalStatuses' => $legalStatuses, 'countries' => $countries]);
}

    public function update(Request $request, $id)
    {
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
            'notes' => 'nullable ',
            'commentary' => 'nullable',
            'group' => 'nullable ',
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
            'avatar' => 'nullable',
            'contracts.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            'transport.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            'financial.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            'commercials.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',
            'adddocs.*' => 'nullable|file|mimes:pdf,doc,docx,zip,txt|max:5120',

        ]);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);

            $validated['avatar'] = 'avatars/' . $avatarName;
        }
        $data = [];


        $docFields = ['commercials', 'contracts', 'transport', 'financial', 'adddocs'];

        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                $files = [];
                foreach ($request->file($field) as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path("documents/{$field}"), $fileName);
                    $files[] = "documents/{$contragent->id}/{$field}/" . $fileName;
                }
                $data[$field] = json_encode($files);
            }
        }
        ContrDocuments::where('contragent_id', $id)->update($data);
        $contragent->update($validated);

        return redirect()->route('contragents.edit', $contragent->id)->with('success', 'Профиль компании успешно сохранен.');



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
    
        return response()->json(['message' => 'File deleted successfully']);
    }
    
}
