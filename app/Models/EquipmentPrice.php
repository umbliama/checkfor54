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
        'store_price',
        'operation_price',
        'archive',
        'hyperlink'
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id');
    }

    /**
     * Get the size associated with this price.
     */
    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }

    public function contragent()
    {
        return $this->belongsTo(Contragents::class, 'contragent_id');
    }

    public function directory()
    {
        return $this->hasOne(Directory::class, 'price_id', 'id');
    }
}
