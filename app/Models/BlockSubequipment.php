<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Block;

class BlockSubequipment extends Model
{
    protected $fillable = ['block_id', 'subequipment_id'];


    public function blocks()
    {
        return $this->belongsToMany(Block::class, 'block_subequipment');
    }
}
