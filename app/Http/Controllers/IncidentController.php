<?php

namespace App\Http\Controllers;
use App\Models\Block;
use App\Models\Column;
use App\Models\Notification;
use App\Models\Contragents;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage');
        $tasksColumns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent','blocks.subequipment','blocks.subequipment.category','blocks.subequipment.size')
            ->orderBy('position')->where('type', 'tasks')->where('isArchive', 0)
            ->paginate($perPage);
        $advColumns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent','blocks.subequipment','blocks.subequipment.category','blocks.subequipment.size')
            ->orderBy('position')->where('type', 'adv')->where('isArchive', 0)
            ->paginate($perPage);
        $tasksColumnsArchived = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent','blocks.subequipment','blocks.subequipment.category','blocks.subequipment.size')
            ->orderBy('position')->where('type', 'tasks')->where('isArchive', 1)
            ->paginate($perPage);
        $advColumnsArchived = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent','blocks.subequipment','blocks.subequipment.category','blocks.subequipment.size')
            ->orderBy('position')->where('type', 'adv')->where('isArchive', 1)
            ->paginate($perPage);
        $contragents = Contragents::all();
        $employees = User::where('isAdmin', 0)->get();



        return Inertia::render('Incident/Index', ['tasksColumns' => $tasksColumns, 'advColumns' => $advColumns, 'tasksColumnsArchived' => $tasksColumnsArchived, 'advColumnsArchived' => $advColumnsArchived, 'contragents' => $contragents, 'employees' => $employees]);
    }

    public function history(Request $request)
    {
        $perPage = $request->input("perPage");
        $columns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('isArchive', 1)
            ->paginate($perPage);
        $contragents = Contragents::all();



        return Inertia::render('Incident/History', ['archivedColumns' => $columns, 'contragents' => $contragents]);
    }

    public function createColumn(Request $request)
    {
        $position = Column::max('position') + 1;
        $user_id = Auth::id();
        $column = Column::create(['position' => $position, 'type' => $request->input('type')]);
        Notification::create([
            'type' => 'Создана новая колонка',
            'data' => ['position' => $position],
            'user_id' => $user_id
        ]);
    }

    public function archiveColumn(Column $column)
    {
        $column->isArchive = 1;

        $column->save();

        return redirect()->back()->with('success', 'Column archived successfully.');
    }

    public function deleteColumn(Column $column)
    {
        $column->delete();
        return redirect()->route('incident.index')->with('success', 'Column deleted successfully.');
    }

    public function createBlock(Request $request, Column $column)
    {
        $type = $request->input('type');
        $content = $this->prepareBlockContent($type, $request);

        $position = $column->blocks()->max('position') + 1;


        $block = $column->blocks()->create(
            array_merge([
                'type' => $type,
                'position' => $position,
            ], $content),

        );


        return redirect()->route('incident.index')->with('success', 'Block updated successfully.');
    }



    protected function prepareBlockContent($type, Request $request)
    {
        switch ($type) {
            case 'equipment':
                return [
                    'equipment_id' => $request->input('equipment_id'),
                ];
            case 'customer':
                return [
                    'contragent_id' => $request->input('contragent_id'),
                ];
            case 'commentary':
                return [
                    'contragent_id' => $request->input('contragent_id', null),
                    'equipment_id' => $request->input('equipment_id', null),
                    'commentary' => $request->input('text'),
                ];
            case 'mediafiles':
                if ($request->hasFile('media_file')) {
                    $uploadedFiles = $request->file('media_file');
                    $mediaUrls = [];

                    foreach ($uploadedFiles as $mediaFile) {
                        $fileName = time() . '_' . uniqid() . '.' . $mediaFile->getClientOriginalExtension();
                        $mediaFile->move(public_path('media_files'), $fileName);
                        $mediaUrls[] = 'media_files/' . $fileName;
                    }

                    return [
                        'media_url' => $mediaUrls,
                    ];
                }

                return [
                    'media_url' => [],
                ];

            case 'files':
                if ($request->hasFile('files')) {
                    $uploadedFiles = $request->file('files');
                    $fileUrls = [];

                    foreach ($uploadedFiles as $file) {
                        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('files'), $fileName);
                        $fileUrls[] = 'files/' . $fileName;
                    }

                    return [
                        'file_url' => $fileUrls,
                    ];
                }

                return [
                    'file_url' => [],
                ];
            case 'employee':
                return [
                    'employee_id' => $request->input('employee_id', null)
                ];

            default:
                return [];
        }
    }


    protected function handleFileUpload(Request $request, $folder)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,doc,docx,pdf,xls,xlsx|max:20480',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store($folder, 'public');
            return [
                'file_path' => $filePath,
                'caption' => $request->input('caption', ''),
            ];
        }

        return [];
    }


    public function deleteBlock(Block $block)
    {
        $block->delete();
        return redirect()->route('incident.index')->with('success', 'Block deleted successfully.');
    }

    public function saveBlockInfo(Request $request, Block $block)
    {
        $type = $block->type;

        $data = $this->prepareBlockContent($type, $request);

        $block->update([
            'media_url' => $data['media_url'] ?? $block->media_url,
            'file_url' => $data['file_url'] ?? $block->file_url,
            'contragent_id' => $request->input('contragent_id'),
            'commentary' => $request->input('text', $block->commentary),
            'employee_id' => $request->input('employee_id'),
            'equipment_id' => $request->input('equipment_id'), 
        ]);
        if ($request->input('subEquipmentArray')) {
            $this->saveSubequipmentAssociations($block, $request->input('subEquipmentArray'));
        }
        return redirect()->route('incident.index')->with('success', 'Block updated successfully.');
    }
    private function saveSubequipmentAssociations(Block $block, array $subEquipmentArray)
    {
        $block->subequipment()->detach();

        foreach ($subEquipmentArray as $subequipmentId) {
            $block->subequipment()->attach($subequipmentId);
        }
    }
    public function reorderColumns(Request $request)
    {
        $columns = $request->input('columns');
        foreach ($columns as $index => $columnId) {
            Column::where('id', $columnId)->update(['position' => $index + 1]);
        }
        return response()->json(['message' => 'Columns reordered']);
    }

    public function reorderBlocks(Request $request, Column $column)
    {
        $blocks = $request->input('blocks');
        foreach ($blocks as $index => $blockId) {
            Block::where('id', $blockId)->update(['position' => $index + 1]);
        }
        return response()->json(['message' => 'Blocks reordered']);
    }
}
