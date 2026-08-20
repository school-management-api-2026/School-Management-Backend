<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_number',
        'room_type',
        'capacity',
        'floor_id',
    ];

    public function floor() {
        return $this->belongsTo(Floors::class, 'floor_id');
    }

    public function schedules() {
        return $this->hasMany(Schedules::class, 'room_id');
    }
}
