<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentCategories;
use App\Models\EquipmentSize;
use App\Models\Block;

class BlockSubequipment extends Model
{

    protected $table = 'block_subequipment';
    protected $fillable = ['block_id', 'subequipment_id'];


    public function blocks()
    {
        return $this->belongsToMany(
            Block::class,                    
            'block_subequipment',            
            'subequipment_id',               
            'block_id'
        )->withPivot('id');                 
    }
    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id');
    }

    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }

    
    
}
