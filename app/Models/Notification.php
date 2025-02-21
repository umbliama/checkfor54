<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'data', 'user_id', 'read_at'];
    protected $casts = ['data' => 'array'];



    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }
}
