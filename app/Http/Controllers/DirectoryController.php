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
        $allowedTypes = ['equipment', 'service', 'sale','test','repair','price'];

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
        $allowedTypes = ['equipment', 'service', 'sale', 'test', 'repair', 'price'];
        if (!in_array($type, $allowedTypes)) {
            return response()->json(['error' => 'Invalid directory type'], 400);
        }
    
        $validatedData = $request->validate([
            'files.*' => 'nullable|file',
            'commentary' => 'required|string',
        ]);
    
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
    
            foreach ($uploadedFiles as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'files/' . $fileName;
    
                $file->move(public_path('files'), $fileName);
    
                Directory::create([
                    $type . '_id' => $id,
                    'file_path' => $filePath,
                    'commentary' => $validatedData['commentary'],
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Files uploaded successfully!');
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
