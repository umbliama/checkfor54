<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'data', 'created_by'];
    protected $casts = ['data' => 'array'];

    /**
     * Связь с информацией о прочтении уведомления.
     */
    public function reads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id', 'id');
    }

    /**
     * Связь для получения данных о создателе уведомления.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
