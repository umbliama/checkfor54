<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContrSale extends Model
{
    protected $fillable = [
        'shipping_date',
        'commentary',
        'contragent_id',
        'sale_id',
    ];
}
