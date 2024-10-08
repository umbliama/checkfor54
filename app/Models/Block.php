<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Block extends Model
{
    protected $fillable = ['column_id', 'type', 'content', 'position'];

    protected $casts = [
        'content' => 'array', 
    ];

    // Block types
    public const TYPES = ['Equipment', 'Customer', 'Commentary', 'Media', 'Files'];

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    // Get block type label
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'Equipment' => 'Equipment Block',
            'Customer' => 'Customer Block',
            'Commentary' => 'Commentary Block',
            'Media' => 'Media Block',
            'Files' => 'Files Block',
            default => 'Unknown Block'
        };
    }

}
