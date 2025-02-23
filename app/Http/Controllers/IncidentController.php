<?php
namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\NotificationCountUpdated;
use App\Models\Block;
use App\Models\Column;
use App\Models\Contragents;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page    = request()->get('page', 1);
        $search  = $request->input('search', null);

        // Query for Tasks
        $tasksQuery = Column::with('blocks.contragent', 'blocks', 'blocks.user')
            ->where('type', 'tasks')
            ->where('isArchive', 0);

        // Query for Advertisements
        $advQuery = Column::with('blocks.contragent', 'blocks', 'blocks.user')
            ->where('type', 'adv')
            ->where('isArchive', 0);

        // Apply search filter if provided
        if ($search) {
            $tasksQuery->whereHas('blocks', function ($query) use ($search) {
                $query->where('commentary', 'LIKE', "%{$search}%")
                    ->orWhere('equipment', 'LIKE', "%{$search}%");
            });

            $advQuery->whereHas('blocks', function ($query) use ($search) {
                $query->where('commentary', 'LIKE', "%{$search}%")
                    ->orWhere('equipment', 'LIKE', "%{$search}%");
            });
        }

        $tasksColumns = $tasksQuery->orderBy('position')->paginate($perPage);
        $advColumns   = $advQuery->orderBy('position')->paginate($perPage);

        // Archived Queries
        $tasksColumnsArchived = Column::with('blocks.contragent', 'blocks.user', 'blocks')
            ->where('type', 'tasks')
            ->where('isArchive', 1)
            ->orderBy('position')
            ->get()
            ->groupBy('contragent_id');

        $paginatedGroups = $tasksColumnsArchived->forPage($page, $perPage);

        $advColumnsArchived = Column::with('blocks.contragent', 'blocks.user', 'blocks')
            ->where('type', 'adv')
            ->where('isArchive', 1)
            ->orderBy('position')
            ->get()
            ->groupBy('contragent_id');

        $advPaginatedGroups = $advColumnsArchived->forPage($page, $perPage);

        // Pagination for Archived Data
        $tasksColumnsArchivedCount = $tasksColumnsArchived->count();
        $advColumnsArchivedCount   = $advColumnsArchived->count();

        $paginator = new LengthAwarePaginator(
            $paginatedGroups,
            $tasksColumnsArchivedCount,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $advPaginator = new LengthAwarePaginator(
            $advPaginatedGroups,
            $advColumnsArchivedCount,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Additional Data
        $contragents = Contragents::all();
        $employees   = User::where('isAdmin', 0)->get();

        $tasksColumns->getCollection()->transform(function ($column) {
            $column->blocks->transform(function ($block) {
                $block->equipment = json_decode($block->equipment, true); // Decode equipment field
                return $block;
            });
            return $column;
        });

        $advColumns->getCollection()->transform(function ($column) {
            $column->blocks->transform(function ($block) {
                $block->equipment = json_decode($block->equipment, true); // Decode equipment field
                return $block;
            });
            return $column;
        });

        return Inertia::render('Incident/Index', [
            'advColumnsArchivedCount'   => $advColumnsArchivedCount,
            'tasksColumnsArchivedCount' => $tasksColumnsArchivedCount,
            'tasksColumns'              => $tasksColumns,
            'advColumns'                => $advColumns,
            'tasksColumnsArchived'      => $paginator,
            'advColumnsArchived'        => $advPaginator,
            'contragents'               => $contragents,
            'employees'                 => $employees,
        ]);
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
        try {
            // Вычисляем позицию для новой колонки
            $position = Column::max('position') + 1;
            $user_id  = Auth::id();

            $column = Column::create([
                'position'   => $position,
                'type'       => $request->input('type'),
                'creator_id' => $user_id,
            ]);

            $notification = Notification::create([
                'type'       => 'Создана новая колонка',
                'data'       => ['position' => $position],
                'created_by' => $user_id,
            ]);

            event(new NewNotification($notification));

            $otherUserIds = User::where('id', '!=', $user_id)->pluck('id')->toArray();

            foreach ($otherUserIds as $userId) {
                NotificationRead::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $userId,
                    'read_at'         => null,
                ]);

                $unreadCount = NotificationRead::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();

                \Log::info("Отправка NotificationCountUpdated для пользователя $userId", ['count' => $unreadCount]);
                event(new NotificationCountUpdated($unreadCount, $userId));
            }

            $this->createBlock($column, 'customer');
            $this->createBlock($column, 'equipment');

            return back()->with('message', 'Колонка успешно создана');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function archiveColumn(Request $request, Column $column)
    {
        try {
            $column->isArchive = 1;

            $column->archive_date = now();

            $column->save();
            return back()->with('message', 'Колонка успешно архивирована');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteColumn(Column $column)
    {
        try {
            $column->delete();
            return back()->with('message', 'Колонка удалена');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createBlock(Column $column, $typeOrRequest)
    {
        try {
            if (is_string($typeOrRequest)) {
                $type    = $typeOrRequest;
                $request = request();
            } elseif ($typeOrRequest instanceof Request) {
                $request = $typeOrRequest;
                $type    = $request->input('type');
            } else {
                throw new \InvalidArgumentException('Argument #2 must be a string or a Request object.');
            }
            $position = Column::max('position') + 1;
            $content  = $this->prepareBlockContent($type, $request);

            Block::create([
                'column_id'  => $column->id,
                'type'       => $type,
                'creator_id' => Auth::id(),
                'position'   => $position,
                'content'    => json_encode($content),
            ]);
            return back()->with('message', 'Блок успешно создан');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());

        }
    }

    protected function prepareBlockContent($type, Request $request)
    {
        switch ($type) {
            case 'equipment':
                return [
                    'equipment' => $request->input('equipment'),
                ];
            case 'customer':
                return [
                    'contragent_id' => $request->input('contragent_id'),
                ];
            case 'commentary':
                return [
                    'contragent_id' => $request->input('contragent_id', null),
                    'equipment_id'  => $request->input('equipment_id', null),
                    'commentary'    => $request->input('commentary'),
                ];
            case 'mediafiles':
                $existingMediaUrls = $request->input('existing_media_urls', []);
                if ($request->hasFile('media_file')) {
                    $uploadedFiles = $request->file('media_file');

                    foreach ($uploadedFiles as $mediaFile) {
                        $fileName            = time() . '_' . uniqid() . '.' . $mediaFile->getClientOriginalExtension();
                        $filePath            = $mediaFile->storeAs('media_files', $fileName, 'public');
                        $existingMediaUrls[] = 'media_files/' . $fileName;
                    }
                }

                return [
                    'media_url' => $existingMediaUrls,
                ];

            case 'files':
                $existingFileUrls = $request->input('existing_file_urls', []);
                if ($request->hasFile('files')) {
                    $uploadedFiles = $request->file('files');

                    foreach ($uploadedFiles as $file) {
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension    = $file->getClientOriginalExtension();
                        $shortUniqid  = substr(uniqid(), 0, 6);
                        $fileName     = $originalName . '_' . time() . '_' . $shortUniqid . '.' . $extension;
                        $file->move(public_path('files'), $fileName);
                        $existingFileUrls[] = 'files/' . $fileName;
                    }
                }

                return [
                    'file_url' => $existingFileUrls,
                ];

            case 'employee':
                return [
                    'employee_id' => $request->input('employee_id', null),
                ];

            default:
                return [];
        }
    }

    protected function handleFileUpload(Request $request, $folder)
    {
        $request->validate([
            'file'    => 'required|file|mimes:jpg,jpeg,png,mp4,mov,doc,docx,pdf,xls,xlsx|max:50480',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store($folder, 'public');
            return [
                'file_path' => $filePath,
                'caption'   => $request->input('caption', ''),
            ];
        }

        return [];
    }

    public function deleteBlock(Block $block)
    {
        try {
            $block->delete();
            return back()->with('message', 'Блок успешно удален');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function saveBlockInfo(Request $request, Block $block)
    {
        try {
            $type = $block->type;
            $data = $this->prepareBlockContent($type, $request);

            // Retrieve existing equipment data (if any)
            $existingEquipment = json_decode($block->equipment, true) ?? [];

            // Merge new equipment data with existing data
            if ($type === 'equipment' && isset($data['equipment'])) {
                $newEquipment = $data['equipment'];
                if (! is_array($newEquipment)) {
                    $newEquipment = [$newEquipment]; // Ensure it's an array
                }
                $mergedEquipment   = array_merge($existingEquipment, $newEquipment);
                $data['equipment'] = json_encode($mergedEquipment); // Save as JSON
            }

            // Update the block with merged data
            $block->update([
                'media_url'     => $data['media_url'] ?? $block->media_url,
                'file_url'      => $data['file_url'] ?? $block->file_url,
                'contragent_id' => $request->input('contragent_id', $block->contragent_id),
                'commentary'    => $request->input('commentary', $block->commentary),
                'employee_id'   => $request->input('employee_id', $block->employee_id),
                'equipment'     => $data['equipment'] ?? $block->equipment,
            ]);

            // Additional logic for specific block types
            if ($block->type == 'customer') {
                $block->column()->update([
                    'contragent_id' => $request->input('contragent_id', $block->column->contragent_id),
                ]);
            }

            if ($request->input('subEquipmentArray')) {
                $this->saveSubequipmentAssociations($block, $request->input('subEquipmentArray'));
            }

            return back()->with('message', 'Данные блока сохранены');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }private function saveSubequipmentAssociations(Block $block, array $subEquipmentArray)
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

    public function deleteImageFromBlock($columnId, $blockId, $imageIndex)
    {
        $taskColumn = Column::find($columnId);

        if (! $taskColumn) {
            return response()->json(['message' => 'Task Column not found.'], 404);
        }

        $block = Block::find($blockId);

        if (! $block) {
            return response()->json(['message' => 'Block not found.'], 404);
        }

        if (is_array($block->media_url)) {
            if (isset($block->media_url[$imageIndex])) {
                $mediaUrls    = $block->media_url;
                $fileToDelete = public_path('storage/' . $block->media_url[$imageIndex]);

                if (File::exists($fileToDelete)) {
                    File::delete($fileToDelete);
                }

                unset($mediaUrls[$imageIndex]);

                $block->media_url = array_values($mediaUrls);

                $block->save();

            } else {
                return response()->json(['message' => 'Image index not found.'], 404);
            }
        }
    }
    public function deleteFile($columnId, $blockId, $fileIndex)
    {
        try {
            $taskColumn = Column::find($columnId);
            if (! $taskColumn) {
                return response()->json(['message' => 'Task Column not found.'], 404);
            }

            $block = Block::find($blockId);
            if (! $block) {
                return response()->json(['message' => 'Block not found.'], 404);
            }

            if (is_array($block->file_url)) {
                if (isset($block->file_url[$fileIndex])) {
                    $fileUrls     = $block->file_url;
                    $fileToDelete = public_path('storage/' . $block->file_url[$fileIndex]);

                    if (File::exists($fileToDelete)) {
                        File::delete($fileToDelete);
                    }

                    unset($fileUrls[$fileIndex]);

                    $block->file_url = array_values($fileUrls);
                    $block->save();

                } else {
                    return response()->json(['message' => 'File index not found.'], 404);
                }
            } else {
                return response()->json(['message' => 'Invalid file list.'], 400);
            }

            return back()->with('message', 'Файл успешно удален');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
