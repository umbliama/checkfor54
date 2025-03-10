<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContrDocuments extends Model
{

    protected $table = 'contr_documents'; 
    protected $fillable = [
        'contragent_id',
        'file_path',
        'user_id',
        'notes',
        'status',
        'type',
        'commercial_type'
        
    ];

    public function contragent()
    {
        return $this->belongsTo(Contragents::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
