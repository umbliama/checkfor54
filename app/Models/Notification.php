<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'data', 'user_id', 'read_at'];
    protected $casts = ['data' => 'array'];

}
