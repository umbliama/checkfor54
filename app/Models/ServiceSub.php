<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'service_id'
    ];


    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    // Subservice has one equipment
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); // Adjust foreign key if needed
    }

    use HasFactory;
}
