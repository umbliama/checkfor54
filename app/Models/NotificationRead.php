<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $fillable = ['notification_id', 'user_id', 'read_at'];
    protected $table = 'notification_user';
    /**
     * Уведомление, к которому относится данная запись.
     */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Пользователь, для которого хранится статус прочтения.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
