<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Equipment extends Model
{
    use HasFactory;


    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function subservice()
    {
        return $this->belongsTo(ServiceSub::class, 'subservice_id', 'id');
    }
    public function serviceEquipment()
    {
        return $this->hasMany(ServiceEquip::class);
    }
    

    protected $fillable = [
        'manufactor',
        'category_id',
        'series',
        'location_id',
        'manufactor_date',
        'location_id',
        'length',
        'operating',
        'stator_rotor',
        'zahodnost',
        'dlina_ds',
        'narabotka_ds',
        'rezbi',
        'length_rezba',
        'diameter',
        'status',
        'notes',
        'size_id',
        'price',
        'commentary',
        'hyperlink'
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id');
    }

    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }
    
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
