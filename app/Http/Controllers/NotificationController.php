<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
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

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        NotificationRead::updateOrCreate(
            ['notification_id' => $notification->id, 'user_id' => auth()->id()],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead($userId)
    {
        $notifications = Notification::all(); 
        foreach ($notifications as $notification) {
            NotificationRead::updateOrCreate(
                [
                    'notification_id' => $notification->id,
                    'user_id' => $userId
                ],
                [
                    'read_at' => now() 
                ]
            );
        }
        return response()->json(['message' => 'All notifications marked as read for the user']);
    }
}
