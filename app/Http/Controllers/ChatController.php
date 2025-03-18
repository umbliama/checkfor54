<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ChatController extends Controller
{

    public function index()
    {
        return Inertia::render('Chat/Index');
    }
    public function sendMessage(Request $request)
    {
        $from_id = Auth::id();
        $to_id = $request->input('to_id');
        $message = $request->input('message');

        Message::create([
            'user_id' => $from_id,
            'recipient_id' => $to_id,
            'message' => $message
        ]);
    }
}
