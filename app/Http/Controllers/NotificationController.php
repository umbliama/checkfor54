<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;



class NotificationController extends Controller
{

    public function index()
    {
        $notifications = Notification::all();
        return Inertia::render('Notifications/Index', ['notifications' => $notifications]);
    }



    public function getNotificationsByUserId($currentUserId)
    {
        $notifications = Notification::where('user_id', '!=', $currentUserId)->get();

        return response()->json($notifications);
    }
}
