<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


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

    public function equipment():BelongsTo
    {
        return $this->belongsTo(Equipment::class,'equipment_id');
    }

    public function serviceSubs(): HasMany
    {
        return $this->hasMany(ServiceSub::class, 'service_equipment_id', 'id');
    }
}
