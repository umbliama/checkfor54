<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $fillable = [
       'equipment_id',
       'contragent_id',
       'shipping_date',
       'period_start_date',
       'return_date',
       'period_end_date',
       'store',
       'operating',
       'return_reason',
       'active',
       'income'

    ];
    use HasFactory;
}
