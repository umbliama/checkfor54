<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEquip extends Model
{
    protected $table = "service_equipment";

    protected $fillable = [
        'service_id',
        'equipment_id',
        'shipping_date',
        'period_start_date',
        'return_date',
        'period_end_date',
        'store',
        'operating',
        'commentary',
        'return_reason',
        'income',
        'subequipment_id'
    ];
}
