<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SaleEquip extends Model
{

    protected $table = 'sale_equipment';
    protected $fillable = [
        'equipment_id',
        'sale',
        'shipping_date',
        'commentary',
        'sale_equipment_id',
        'price',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale', 'id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }

    public function subequipment(): HasMany
    {
        return $this->hasMany(SaleSub::class, 'sale_equipment_id', 'id');
    }

}
