<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class EquipmentCategories extends Model
{    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    public function sizes()
{
    return $this->hasMany(EquipmentSize::class, 'category_id', 'id');
}

    use HasFactory;
}
