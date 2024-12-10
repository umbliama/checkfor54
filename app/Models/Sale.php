<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id'); 
    }

    public function subservices()
    {
        return $this->hasMany(SaleSub::class, 'sale_number', 'id');
    }
    public function subequipment()
    {
        return $this->belongsToMany(Equipment::class, 'sale_subequipment', 'sale_number', 'subequipment_id');
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
