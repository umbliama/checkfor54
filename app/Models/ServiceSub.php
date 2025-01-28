<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ServiceSub extends Model
{

    protected $table = 'service_subequipment';

    protected $fillable = [
        'subequipment_id',
        'shipping_date',
        'period_start_date',
        'commentary',
        'return_date',
        'period_end_date',
        'store',
        'operating',
        'service_id',
        'income',
        'return_reason',
        'service_equipment_id',
    ];


    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'subequipment_id', 'id'); 
    }
    public function serviceEquip(): BelongsTo
    {
        return $this->belongsTo(ServiceEquip::class, 'service_equipment_id', 'id');
    }
   

    use HasFactory;
}
