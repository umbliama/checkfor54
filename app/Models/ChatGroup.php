<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChMessage as Message;

class ChatGroup extends Model {
    use HasFactory;

    protected $fillable = ['name', 'created_by'];

    public function members() {
        return $this->hasMany(ChatGroupMember::class, 'group_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'group_id');
    }
}
