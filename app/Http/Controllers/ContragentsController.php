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

        $query = Contragents::query();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('fullname', 'LIKE', "%$searchTerm%")
                    ->orWhere('inn', 'LIKE', "%$searchTerm%");
            });
        }

        // Paginate the results, specifying the number of items per page (e.g., 10)
        $contragents = $query->paginate(10); // Adjust the number per page

        // Paginate customers and suppliers separately
        $contragents_customers = Contragents::where("customer", 1)->paginate(5); // Adjust the number per page
        $contragents_suppliers = Contragents::where("supplier", 1)->paginate(5); // Adjust the number per page

        // Get the counts for statistics (these do not need to be paginated)
        $contragents_count = Contragents::count();
        $contragents_customer_count = Contragents::where("customer", 1)->count();
        $contragents_supplier_count = Contragents::where("supplier", 1)->count();

        return Inertia::render('Contragents/Index', [
            'contragents' => $contragents,
            'contragents_count' => $contragents_count,
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
            'fullname' => 'required',
            'inn' => 'required',
            'kpp' => 'required',
            'ogrn' => 'required',
            'reason' => 'required',
            'notes' => 'required',
            'commentary' => 'required',
            'group' => 'required',
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
            ->with('success', 'Contragent created successfully!');
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
            'agentTypeLegal' => 'required',
            'country' => 'required',
            'name' => 'required',
            'fullname' => 'required',
            'inn' => 'required',
            'kpp' => 'required',
            'ogrn' => 'required',
            'reason' => 'required',
            'notes' => 'required',
            'commentary' => 'required',
            'group' => 'required',
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
