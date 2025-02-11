<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceContragent extends Model
{
    protected $fillable = [
        'contragent_id',
        'shipping_date',
        'commentary'
    ];
}
