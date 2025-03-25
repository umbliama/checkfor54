<?php
namespace App\Http\Controllers;

use App\Models\Message;
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
            $from_id = Auth::id();
            $to_id   = $request->input('recipient_id');
            $messageText = $request->input('message');
    
            // Create the message and store it in the database
            $message = Message::create([
                'user_id'      => $from_id,
                'recipient_id' => $to_id,
                'message'      => $messageText,
            ]);
    
            // Pass the newly created Message model instance to the event
            broadcast(new MessageSent($message))->toOthers();
    
            return back()->with('success', 'Отправлено');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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

    public function getChat($id)
    {
        $user = \App\Models\User::findOrFail($id);

        return Inertia::render('Chat/ContactChat', [
            'user' => $user,
        ]);
    }
}
