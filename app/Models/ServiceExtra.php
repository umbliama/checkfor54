<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceExtra extends Model
{
    protected $fillable = [
        'shipping_date',
        'type',
        'commentary',
        'service_id',
        'price',
    ];
    protected $table = 'services_extra';
}
