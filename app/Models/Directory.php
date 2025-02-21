<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Directory extends Model
{
    protected $fillable = [
        'sale_id',
        'equipment_id',
        'service_id',
        'test_id',
        'repair_id',
        'move_id',
        'price_id',
        'contragent_id',
        'commentary',
        'file_path'
    ];
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function repair()
    {
        return $this->belongsTo(EquipmentRepair::class);
    }
    public function test()
    {
        return $this->belongsTo(EquipmentTest::class);
    }
    public function price()
    {
        return $this->belongsTo(EquipmentPrice::class);
    }
    public function move()
    {
        return $this->belongsTo(EquipmentMove::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(DirectoryFiles::class);
    }

    public function directory()
    {
        return $this->belongsTo(Contragents::class);
    }
}
