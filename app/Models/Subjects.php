<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];

    public function exam() {
        return $this->belongsTo(Exams::class, 'subject_id');
    }

    public function course() {
        return $this->belongsTo(Courses::class, 'subject_id');
    }
}
