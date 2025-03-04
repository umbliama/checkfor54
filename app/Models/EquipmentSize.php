<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentSize extends Model
{
    use HasFactory;

    protected $table = 'equipment_sizes';

    public function equipment()
    {
        return $this->hasOne(Equipment::class);
    }
}
