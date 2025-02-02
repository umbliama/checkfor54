<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Service extends Model
{

    protected $fillable = [
        'equipment_id',
        'contragent_id',
        'shipping_date',
        'period_start_date',
        'return_date',
        'period_end_date',
        'store',
        'service_id',
        'commentary',
        'service_number',
        'service_date',
        'operating',
        'return_reason',
        'active',
        'income',
        'full_income',
        'subequipment_ids',
        'hyperlink'
    ];


    public function subequipment()
    {
        return $this->belongsToMany(Equipment::class, 'service_subequipment', 'service_id', 'subequipment_id');
    }

    public function subservices()
    {
        return $this->hasMany(ServiceSub::class, 'service_id', 'id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function mainServices()
    {
        return $this->hasMany(ServiceEquip::class, 'service_id', 'id');
    }
    
    public function serviceEquipment()
{
    return $this->hasMany(ServiceEquip::class, 'service_id', 'id');
}
    use HasFactory;
}
