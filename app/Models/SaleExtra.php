<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;


use Illuminate\Database\Eloquent\Model;

class SaleExtra extends Model
{
    protected $table = 'sale_extra';

    protected $fillable = [
        'shipping_date',
        'type',
        'sale_id',
        'commentary',
        'price',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
