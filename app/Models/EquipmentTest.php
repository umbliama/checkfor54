<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentTest extends Model
{
    use HasFactory;

    protected $fillable = ['test_date', 'location_id', 'expense','description','category_id','size_id','series'];

    public function directory()
    {
        return $this->hasOne(Directory::class, 'test_id', 'id');
    }
}
