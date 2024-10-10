<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'size_id',
        'contragent_id',
        'category_id',
        'title',
        'store_date',
        'notes',
        'price',
        'archive'
    ];
}
