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
        $columns = Column::with('blocks.equipment.category','blocks.equipment.size')
        ->orderBy('position')
        ->paginate(10);
        $contragents = Contragents::all();



        return Inertia::render('Incident/Index', ['columns' => $columns, 'contragents' => $contragents]);
    }

    public function createColumn()
    {
        $position = Column::max('position') + 1;
        $column = Column::create(['position' => $position]);
        foreach (User::all() as $user){
            Notification::create([
                'type' => 'Создана новая колонка',
                'data' => ['position'=>$position],
                'user_id' => $user->id
            ]);
        }
        return redirect()->route('incident.index')->with('success', 'Column updated successfully.');
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
    
        $block = $column->blocks()->create([
            'type' => $type,
            'content' => $content,
            'position' => $position
        ]);
    
        return redirect()->route('incident.index')->with('success', 'Block updated successfully.');
    }



    protected function prepareBlockContent($type, Request $request)
{
    switch ($type) {
        case 'Equipment':
            return [
                'equipment_id' => $request->input('equipment_id'),
                'name' => $request->input('name'),
                'status' => $request->input('status')
            ];
        case 'Customer':
            return [
                'customer_id' => $request->input('customer_id'),
                'name' => $request->input('name'),
                'contact' => $request->input('contact')
            ];
        case 'Commentary':
            return [
                'text' => $request->input('text')
            ];
        case 'Media':
            return [
                'media_url' => $request->input('media_url'),
                'caption' => $request->input('caption')
            ];
        case 'Files':
            return [
                'file_name' => $request->input('file_name'),
                'file_path' => $request->input('file_path')
            ];
        default:
            return [];
    }
}


    public function deleteBlock(Block $block)
    {
        $block->delete();
        return response()->json(['message' => 'Block deleted']);
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
