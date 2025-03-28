<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentRepair extends Model
{
    use HasFactory;
    protected $fillable = ['equipment_id','isLocked','on_repair_date','repair_date', 'location_id', 'expense', 'description', 'category_id', 'size_id', 'series', 'hyperlink'];

    public function directory()
    {
        return $this->hasOne(Directory::class, 'repair_id', 'id');
    }
    
}
