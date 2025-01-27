<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BlockSubequipment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Column;
class Block extends Model
{
    protected $fillable = ['column_id', 'type', 'commentary', 'file_url', 'employee_id', 'media_url', 'position', 'contragent_id', 'equipment','creator_id'];

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
        return $this->belongsToMany(
            Equipment::class,
            'block_subequipment',
            'block_id',
            'subequipment_id'
        )->with('category', 'size');
    }



    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment');
    }

    public function contragent(): BelongsTo
    {
        return $this->belongsTo(Contragents::class, 'contragent_id');
    }

}
