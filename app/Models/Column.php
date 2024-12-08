<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Column extends Model
{
    protected $fillable = ['name', 'position', 'isArchive','type'];

    // Each column has many blocks
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('position');
    }
}
