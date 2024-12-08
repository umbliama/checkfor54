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

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id', 'sale_number');
    }

    public function equipment()
    {
    return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); 
    }
}
