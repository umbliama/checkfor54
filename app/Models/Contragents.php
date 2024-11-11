<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contragents extends Model
{
    use HasFactory;

    protected $fillable = [
        'agentTypeLegal',
        'country',
        'name',
        'fullname',
        'inn',
        'kpp',
        'ogrn',
        'reason',
        'notes',
        'commentary',
        'group',
        'bankname',
        'bank_bik',
        'bank_inn',
        'bank_rs',
        'bank_kpp',
        'bank_ca',
        'bank_commnetary',
        'supplier',
        'customer',
        'address',
        'site',
        'phone',
        'email',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'contact_person_notes',
        'contact_person_commentary',
        'status',
        'avatar',

    ];


    public function size()
    {
        return $this->belongsTo(EquipmentSize::class, 'size_id');
    }

}
