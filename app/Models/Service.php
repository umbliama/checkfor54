<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
       'service_number',
       'service_date',
       'operating',
       'return_reason',
       'active',
       'income',
       'subequipment_ids'
    ];


    public function subequipment()
    {
        return $this->belongsToMany(Equipment::class, 'service_subequipment', 'service_id', 'subequipment_id');
    }

    public function subservices()
    {
        return $this->hasMany(ServiceSub::class, 'service_id', 'id');
    }

    // Assuming Service is related to one equipment directly
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); // Adjust foreign key if needed
    }


    use HasFactory;
}
