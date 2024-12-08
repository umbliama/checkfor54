<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    protected $table = 'sale';
    
    protected $fillable = [
        'equipment_id',
        'contragent_id',
        'shipping_date',
        'sale_number',
        'sale_date',
        'commentary',
        'status',
        'price',
        'subequipment_ids'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); 
    }

    public function subservices()
    {
        return $this->hasMany(SaleSub::class, 'sale_number', 'id');
    }
    public function subequipment()
    {
        return $this->belongsToMany(Equipment::class, 'sale_subequipment', 'sale_number', 'subequipment_id');
    }


}
