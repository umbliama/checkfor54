<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContrDocuments extends Model
{

    protected $table = 'contr_documents'; 
    protected $fillable = [
        'contragent_id',
        'commercials',
        'contracts',
        'transport',
        'financial',
        'adddocs'
    ];

    public function contragent()
    {
        return $this->belongsTo(Contragents::class);
    }
}
