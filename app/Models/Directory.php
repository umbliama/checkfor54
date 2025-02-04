<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $fillable = [
        'sale_id',
        'equipment_id',
        'service_id',
        'test_id',
        'repair_id',
        'commentary',
        'files'
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
}
