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

    public static function getCountryMapping(): array
    {
        return [
            'RU' => 'Российская Федерация',
            'BY' => 'Беларусь',
            'AZ' => 'Азербайджан',
            'KZ' => 'Казахстан',
            'CN' => 'Китай',
        ];
    }
    public static function getLegalMapping(): array
    {
        return [
            'OOO' => 'ООО',
            'ZAO' => 'ЗАО',
            'OAO' => 'ОАО',
            'PAO' => 'ПАО',
            'individual' => 'ИП',
        ];
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'contragent_id');
    }


    public function getFormattedCountryAttribute()
    {
        $countryMapping = self::getCountryMapping();
        $countryCode = $this->country;
        $countryName = $countryMapping[$countryCode] ?? 'Unknown';
        return [
            'value' => $countryCode,
            'title' => $countryName,
        ];
    }

    public function getLegalStatusesAttribute()
    {
        $legalMapping = self::getLegalMapping();
        $legalCode = $this->agentTypeLegal;
        $legalName = $legalMapping[$legalCode] ?? 'Unknown';
        return [
            'value' => $legalCode,
            'title' => $legalName,
        ];
    }

    public function documents()
    {
        return $this->hasMany(ContrDocuments::class,'contragent_id', 'id');
    }

    public function directory()
    {
        return $this->hasOne(Directory::class,'contragent_id','id');
    }

}
