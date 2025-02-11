<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentMove extends Model
{
    protected $fillable = [
        'send_date',
        'from',
        'to',
        'reason',
        'expense',
        'category_id',
        'size_id',
        'series',
        'equipment_id',
    ];
    
}
