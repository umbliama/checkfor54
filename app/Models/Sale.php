<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Sale extends Model
{

    protected $table = 'sale';
    
    protected $fillable = [
        'equipment_id',
        'contragent_id',
        'shipping_date',
        'sale_number',
        'sale_date',
        'commentary',
        'status',
        'price',
        'subequipment_ids'
    ];

    public function saleEquipment()
    {
        return $this->hasMany(SaleEquip::class, 'sale', 'id'); 
    }
    
    public function saleSubequipment(): HasMany
    {
        return $this->hasMany(SaleSub::class, 'sale_id', 'id');
    }
    
    public function extraServices(): HasMany
    {
        return $this->hasMany(SaleExtra::class, 'sale_id', 'id');
    }
    
    public function contragent(): BelongsTo
    {
        return $this->belongsTo(Contragents::class, 'contragent_id', 'id');
    }
    public function directory(): HasOne
    {
        return $this->hasOne(Directory::class, 'sale_id', 'id');
    }

    public static function getStatusesMapping(): array
    {
        return [
            'full' => 'Полная оплата',
            'pred' => 'Предоплата',
            'credit' => 'Рассрочка',
        ];
    }

    public static function getExtraServices(): array
    {
        return [
            'transfer' => 'Перевозка оборудования',
            'repair_vzd' => 'Ремонт ВЗД',
            'repair_yss' => 'Ремонт ЯСС',
            'test_vzd' => 'Испытание ВЗД',
            'test_yss' => 'Испытание ЯСС',
            'replace_vzd' => 'Замена ЗИП ВЗД',
            'replace_yss' => 'Замена ЗИП ЯСС',
            'kern' => 'Отбор керна'
        ];
    }
}
