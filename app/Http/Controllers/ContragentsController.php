<?php

namespace App\Http\Controllers;

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

        // Format the country attribute for each contragent
        $contragents->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });

        $contragents_customers->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });

        $contragents_suppliers->getCollection()->transform(function ($contragent) {
            return $contragent->append('formatted_country');
        });

        $countries = Contragents::getCountryMapping();

        return Inertia::render('Contragents/Index', [
            'contragents' => $contragents,
            'contragents_count' => $contragents_count,
            'countries' => $countries,
            'contragents_customer_count' => $contragents_customer_count,
            'contragents_supplier_count' => $contragents_supplier_count,
            'contragents_customers' => $contragents_customers,
            'contragents_suppliers' => $contragents_suppliers
        ]);
    }
    public function create()
    {
        return Inertia::render('Contragents/Create');
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
        return redirect()->route('contragents.edit', parameters: $contragent->id)
            ->with('success', 'Профиль компании сохранен.');
    }


    public function edit($id)
    {
        $contragent = Contragents::findOrFail($id);
        return Inertia::render('Contragents/Edit', ['contragent' => $contragent]);
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
        ]);
        if ($request->hasFile('avatar')) {
            // Handle avatar upload
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
    
            // Add avatar path to the validated array
            $validated['avatar'] = 'avatars/' . $avatarName;
        }
    


        // Fetch the client by ID and update it
        $contragent->update($validated);

        // Redirect back with a success message
        return redirect()->route('contragents.edit', $contragent->id)->with('success', 'Профиль компании успешно сохранен.');



    }

    public function show($id)
    {
        $contragent = Contragents::findOrFail($id);

        return Inertia::render('Contragents/Show', ['contragent' => $contragent]);
    }



    public function destroy($id)
    {
        $contragent = Contragents::findOrFail($id);

        $contragent->delete();
    }



}
