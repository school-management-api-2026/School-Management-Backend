<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buildings extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'total_floors',
        'address_location',
    ];

    public function floors() {
        return $this->hasMany(Floors::class, 'building_id');
    }
}
