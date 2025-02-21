<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;



class NotificationController extends Controller
{

    public function index()
    {
        $user_id = Auth::id();
        $notifications = Notification::with('user')->where('user_id',"!=",$user_id)->get();
        // $notifications = Notification::all();
        $read_notifications = NotificationRead::all();
        return Inertia::render('Notifications/Index', ['notifications' => $notifications, 'user_id' => $user_id,'read_notifications' => $read_notifications]);
    }



    public function getNotificationsByUserId($currentUserId)
    {
        $notifications = Notification::where('user_id', '!=', $currentUserId)
            ->whereNotIn('id', function ($query) use ($currentUserId) {
                $query->select('notification_id')
                    ->from('notification_reads')
                    ->where('user_id', $currentUserId)
                    ->whereNotNull('read_at');
            })
            ->get();
    
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id, $userId)
    {
        $notification = Notification::findOrFail($id);

        NotificationRead::updateOrCreate(
            ['notification_id' => $notification->id, 'user_id' => $userId],
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
