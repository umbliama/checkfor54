<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $fillable = [
        'sale_id',
        'equipment_id',
        'service_id',
        'commentary',
        'files'
    ];
}
