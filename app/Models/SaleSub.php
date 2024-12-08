<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleSub extends Model
{
    protected $table = 'sale_subequipment';

    protected $fillable = [
        'equipment_id',
        'sale_number',
        'shipping_date',
        'commentary',
        'price'
    ];
}
