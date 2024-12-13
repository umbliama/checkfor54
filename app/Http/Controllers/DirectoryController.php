<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $type, $id)
    {
        $allowedTypes = ['equipment', 'service', 'sale','test','repair'];

        if (!in_array($type, $allowedTypes)) {
            return redirect()->back()->withErrors(['error' => 'Invalid directory type']);
        }


        $typeIdField = $type . '_id';

        $directory = Directory::where($typeIdField, $id)->first();

        return Inertia::render('Directory/Index', [
            'directory' => $directory,
            'type' => $type,
            'id' => $id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $type, $id)
    {
        $allowedTypes = ['equipment', 'service', 'sale','test','repair'];
        if (!in_array($type, $allowedTypes)) {
            return response()->json(['error' => 'Invalid directory type'], 400);
        }

        $directory = Directory::where($type . '_id', $id)->first();


        $validatedData = $request->validate([
            'files.*' => 'nullable|file',
            'commentary' => 'required',
        ]);

        $fileUrls = [];

        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files'); 

            foreach ($uploadedFiles as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('files'), $fileName);
                $fileUrls[] = 'files/' . $fileName; 
            }
        }
        if ($directory) {
            $directory->update(array_merge($validatedData, [
                $type . '_id' => $id,
                'files' => json_encode($fileUrls), 
            ]));

            return redirect()->back()->with('success', 'Directory updated successfully!');

        } else {
            $directory = Directory::create(array_merge($validatedData, [
                $type . '_id' => $id,
                'files' => json_encode($fileUrls), 
            ]));

            return redirect()->back()->with('success', 'Directory created successfully!');

        }


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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
