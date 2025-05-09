<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    public function tests()
    {
        return $this->hasMany(EquipmentTest::class, 'equipment_id', 'id');
    }

    public function repairs()
    {
        return $this->hasMany(EquipmentRepair::class, 'equipment_id', 'id');
    }
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

    public function directory()
    {
        return $this->hasOne(Directory::class, 'equipment_id', 'id');
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
        'hyperlink',
        'ownership'
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id');
    }

    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, ServiceEquip::class, 'equipment_id', 'id', 'id', 'service_id');
    }
    public function activeServices()
    {
        return $this->hasManyThrough(Service::class, ServiceEquip::class, 'equipment_id', 'id', 'id', 'service_id')
            ->where('services.active', 1)
            ->where(function ($query) {
                $query->whereHas('serviceEquipment', function ($q) {
                    $q->whereNotNull('service_equipment.period_start_date')
                      ->whereNotNull('service_equipment.return_date');
                })
                ->orWhereHas('serviceSubequipment', function ($q) {
                    $q->whereNotNull('service_subequipment.period_start_date')
                      ->whereNotNull('service_subequipment.return_date');
                });
            });
    }

    public function getUsedAttribute()
    {
        return $this->activeServices()->exists() || $this->repairs()->exists() || $this->tests()->exists();
    }

    public function serviceSubs()
    {
        return $this->hasMany(ServiceSub::class, 'subequipment_id', 'id');
    }
    public static function getOwnershipOptions()
    {
        return [
            ['title' => 'Собственное', 'value' => 'our'],
            ['title' => 'Субаренда', 'value' => 'sub'],
        ];
    }
    public function location()
    {
        return $this->belongsTo(EquipmentLocation::class, 'location_id');
    }       
}
