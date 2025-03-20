<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use App\Events\NotificationCountUpdated;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $unreadNotifications = Notification::whereHas('reads', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                  ->whereNull('read_at');
        })
        ->orWhereDoesntHave('reads', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
        ->with('creator')
        ->get();

    // Read notifications for current user
    $readNotifications = Notification::whereHas('reads', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                  ->whereNotNull('read_at');
        })
        ->with('creator')
        ->get();
        return Inertia::render('Notifications/Index', [
            'notifications' => $unreadNotifications,
            'read_notifications' => $readNotifications,
            'user_id' => $user_id,
        ]);
    }
    /**
     * Получает уведомления, которые ещё не отмечены как прочитанные текущим пользователем.
     */
    public function getNotificationsByUserId($currentUserId)
    {
        $notifications = Notification::whereNotIn('id', function ($query) use ($currentUserId) {
            $query->select('notification_id')
                ->from('notification_reads')
                ->where('user_id', $currentUserId)
                ->whereNotNull('read_at');
        })->get();

        return response()->json($notifications);
    }

    /**
     * Отмечает конкретное уведомление как прочитанное для указанного пользователя.
     */
    public function markAsRead(Request $request, $id, $userId)
    {
        $notification = Notification::findOrFail($id);

        NotificationRead::updateOrCreate(
            ['notification_id' => $notification->id, 'user_id' => $userId],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Отмечает все уведомления как прочитанные для указанного пользователя.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user_id = Auth::id();
    
            $notifications = NotificationRead::where('user_id', $user_id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
    
            $unreadCount = NotificationRead::where('user_id', $user_id)
                ->whereNull('read_at')
                ->count();
    
            event(new NotificationCountUpdated($unreadCount, $user_id));
    
            return back()->with('message', 'Все уведомления прочитаны');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
