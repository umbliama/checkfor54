<?php
namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Inertia\Inertia;



class ChatController extends Controller
{

    public function index()
    {
        return Inertia::render('Chat/Index');
    }
    public function sendMessage(Request $request)
    {
        try {
            $group_id = $request->input('group_id');

            if ($group_id) {
                $from_id = Auth::id();
                $to_id = $request->input('recipient_id');
                $messageText = $request->input('message');

                // Create the message and store it in the database
                $message = Message::create([
                    'user_id' => $from_id,
                    'recipient_id' => $to_id,
                    'group_id' => $group_id,
                    'message' => $messageText,
                ]);

                // Broadcast group message event
                broadcast(new GroupMessageSent($message))->toOthers();

                return back()->with('success', 'Отправлено');

            } else {
                $from_id = Auth::id();
                $to_id = $request->input('recipient_id');
                $messageText = $request->input('message');

                // Create the message and store it in the database
                $message = Message::create([
                    'user_id' => $from_id,
                    'recipient_id' => $to_id,
                    'message' => $messageText,
                ]);

                // Pass the newly created Message model instance to the event
                broadcast(new MessageSent($message))->toOthers();

                return back()->with('success', 'Отправлено');

            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



    public function searchContactByName(Request $request)
    {
        $name = $request->input('name');

        $contacts = User::where(function ($query) use ($name) {
            $query->where('name', 'LIKE', "%{$name}%")
                ->orWhere('last_name', 'LIKE', "%{$name}%");
        })
            ->get();



        return response()->json($contacts);
    }
    public function getContacts()
    {
        try {
            $userId = auth()->id();

            $contacts = \App\Models\User::whereIn('id', function ($query) use ($userId) {
                $query->select('user_id')
                    ->from('messages')
                    ->where('recipient_id', $userId)
                    ->union(
                        \DB::table('messages')
                            ->select('recipient_id')
                            ->where('user_id', $userId)
                    );
            })->get();

            return response()->json($contacts);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function getGroups()
    {
        try {
            $userId = auth()->id();

            $groups = ChatGroup::whereIn('id', function ($query) use ($userId) {
                $query->select('group_id')
                    ->from('chat_group_members')
                    ->where('user_id', $userId);
            })->with([
                        'members' => function ($query) use ($userId) {
                            $query->where('user_id', '!=', $userId); // Исключаем текущего пользователя из списка участников
                        }
                    ])->get();

            return response()->json($groups);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUserMessages($id)
    {
        try {
            $currentUserId = auth()->id();

            $user = \App\Models\User::findOrFail($id);

            $messages = \App\Models\Message::with(['user', 'recipient'])
                ->where(function ($query) use ($currentUserId, $id) {
                    $query->where('user_id', $currentUserId)
                        ->where('recipient_id', $id);
                })->orWhere(function ($query) use ($currentUserId, $id) {
                    $query->where('user_id', $id)
                        ->where('recipient_id', $currentUserId);
                })->orderBy('created_at', 'asc')->get();


            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function getGroupMessages($id)
    {
        try {
            $currentUserId = auth()->id();

            // Находим группу
            $group = \App\Models\ChatGroup::findOrFail($id);

            // Проверяем, является ли текущий пользователь участником группы
            if (!$group->members()->where('user_id', $currentUserId)->exists()) {
                return response()->json(['error' => 'You are not a member of this group'], 403);
            }

            // Получаем сообщения группы
            $messages = \App\Models\Message::with(['user'])
                ->where('group_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }



    public function getChat($id)
    {
        $user = \App\Models\User::findOrFail($id);

        return Inertia::render('Chat/ContactChat', [
            'user' => $user,
        ]);
    }

    public function getUserInfo(Request $request)
    {
        $id = $request->input('id');

        $user = User::where('id', $id)->first();

        return response()->json($user);
    }

    public function getAllUsers()
    {
        $currentUserId = auth()->id();
        $users = User::where('id', '!=', $currentUserId)->get();

        return response()->json($users);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array'
        ]);

        $group = ChatGroup::create([
            'name' => $request->name,
            'created_by' => Auth::id()
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);
        foreach ($request->members as $memberId) {
            ChatGroupMember::create([
                'group_id' => $group->id,
                'user_id' => $memberId
            ]);
        }
        return response()->json(['message' => 'Group created successfully', 'group' => $group], 201);

    }

    public function getGroupInfo(Request $request)
    {
        $id = $request->input('id');


        $group = ChatGroup::where('id', $id)->first();

        return response()->json($group);
    }
}
