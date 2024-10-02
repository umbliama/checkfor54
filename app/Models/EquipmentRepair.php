<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentRepair extends Model
{
    use HasFactory;

    protected $fillable = ['repair_date', 'location_id', 'expense','description','category_id','size_id','series'];
}
