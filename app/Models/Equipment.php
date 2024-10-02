<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Equipment extends Model
{
    use HasFactory;


    protected $fillable = [
        'manufactor',
        'category_id',
        'series',
        'manufactor_date',
        'status',
        'notes',
        'size_id',
        'price',
        'commentary'
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id');
    }

    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }

}
