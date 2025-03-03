<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\DirectoryFiles;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $type, $id)
    {
        $allowedTypes = ['equipment', 'service', 'sale', 'test', 'repair', 'price', 'move', 'contragent'];

        if (!in_array($type, $allowedTypes)) {
            return redirect()->back()->withErrors(['error' => 'Invalid directory type']);
        }


        $typeIdField = $type . '_id';

        $directory = Directory::with('files.uploader')->where($typeIdField, $id)->first();

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
        try {
            // Define allowed types
            $allowedTypes = ['equipment', 'service', 'sale', 'test', 'repair', 'price', 'move', 'contragent'];

            // Validate the type parameter
            if (!in_array($type, $allowedTypes)) {
                return response()->json(['error' => 'Invalid directory type'], 400);
            }

            // Validate request data
            $validatedData = $request->validate([
                'files.*' => 'nullable|file',
                'commentary' => 'required|string',
            ]);

            // Determine if we are creating or updating a record
            $typeIdField = $type . '_id';
            $record = Directory::where($typeIdField, $id)->first();

            // Create or update the directory
            if ($record) {
                $record->update([
                    'commentary' => $validatedData['commentary'],
                ]);
            } else {
                $record = Directory::create([
                    $typeIdField => $id,
                    'commentary' => $validatedData['commentary'],
                ]);
            }

            if ($request->hasFile('files')) {
                $uploadedFiles = $request->file('files');

                foreach ($uploadedFiles as $file) {
                    $fileName = $file->getClientOriginalName() . '_' . uniqid();
                    $filePath = 'files/' . $fileName;

                    $file->move(public_path('files'), $fileName);

                    $record->files()->create([
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'mime_type' => $file->getClientMimeType(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            return redirect()->back()->with('message', 'Данные сохранены.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());

        }
    }

    public function deleteFile($id)
    {
        try {
            $file = DirectoryFiles::find($id);

            $file->delete();
            return back()->with('message', 'Файл удален.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
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
