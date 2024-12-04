<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BlockSubequipment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Block extends Model
{
    protected $fillable = ['column_id', 'type', 'commentary', 'file_url', 'media_url', 'position','contragent_id','equipment_id'];

    protected $casts = [
        'media_url' => 'array',
        'file_url' => 'array',
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
        return match ($this->type) {
            'Equipment' => 'Equipment Block',
            'Customer' => 'Customer Block',
            'Commentary' => 'Commentary Block',
            'Media' => 'Media Block',
            'Files' => 'Files Block',
            default => 'Unknown Block'
        };
    }

    public function subequipment()
    {
        return $this->belongsToMany(BlockSubequipment::class, 'block_subequipment');
    }

    public function equipment():BelongsTo
    {
        return $this->belongsTo(Equipment::class,'equipment_id');
    }

    public function contragent(): BelongsTo
    {
        return $this->belongsTo(Contragents::class,'contragent_id');
    }


}
