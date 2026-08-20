<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floors extends Model
{
    use HasFactory;
    protected $fillable = [
        'floor_number',
        'building_id',
    ];

    public function building() {
        return $this->belongsTo(Buildings::class, 'building_id');
    }

    public function rooms() {
        return $this->hasMany(Rooms::class, 'floor_id');
    }
}
