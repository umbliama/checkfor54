<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChMessage extends Model
{
    use UUID;

    protected $fillable = ['group_id','to_id','from_id'];

    public function group() {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }
    
}
