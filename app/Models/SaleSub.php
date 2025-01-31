<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleSub extends Model
{
    protected $table = 'sale_subequipment';

    protected $fillable = [
        'equipment_id',
        'sale_equipment_id',
        'sale_id',
        'shipping_date',
        'commentary',
        'price'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); 
    }

    public function saleEquipment(): BelongsTo
    {
        return $this->belongsTo(SaleEquip::class, 'sale_equipment_id', 'id');
    }

}
