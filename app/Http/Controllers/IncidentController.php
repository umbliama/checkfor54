<?php

namespace App\Http\Controllers;
use App\Models\Block;
use App\Models\Column;
use App\Models\Notification;
use App\Models\Contragents;
use App\Models\User;
use Inertia\Inertia;

use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasksColumns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('type','tasks')->where('isArchive',0)
            ->paginate(10);
        $advColumns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('type','adv')->where('isArchive',0)
            ->paginate(10);
        $tasksColumnsArchived = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('type','tasks')->where('isArchive',1)
            ->paginate(10);
        $advColumnsArchived = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('type','adv')->where('isArchive',1)
            ->paginate(10);
        $contragents = Contragents::all();
        $employees = User::where('isAdmin', 0)->get();



        return Inertia::render('Incident/Index', ['tasksColumns' => $tasksColumns, 'advColumns' => $advColumns, 'tasksColumnsArchived' => $tasksColumnsArchived, 'advColumnsArchived'=> $advColumnsArchived ,'contragents' => $contragents, 'employees' => $employees]);
    }

    public function history()
    {
        $columns = Column::with('blocks.equipment.category', 'blocks.equipment.size', 'blocks.contragent')
            ->orderBy('position')->where('isArchive',1)
            ->paginate(10);
        $contragents = Contragents::all();



        return Inertia::render('Incident/History', ['archivedColumns' => $columns, 'contragents' => $contragents]);
    }

    public function createColumn(Request $request)
    {
        $position = Column::max('position') + 1;
        $column = Column::create(['position' => $position, 'type' => $request->input('type')]);
        foreach (User::all() as $user) {
            Notification::create([
                'type' => 'Создана новая колонка',
                'data' => ['position' => $position],
                'user_id' => $user->id
            ]);
        }
        return redirect()->route('incident.index')->with('success', 'Column updated successfully.');
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
                    'name' => $request->input('name'),
                    'status' => $request->input('status')
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
            'commentary' => $request->input('text', $block->commentary), 
            'employee_id' => $data['employee_id']
        ]);
    
        return redirect()->route('incident.index')->with('success', 'Block updated successfully.');
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
