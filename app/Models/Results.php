<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    use HasFactory;
    protected $fillable = [
        'score',
        'grade',
        'enrollment_id',
        'exam_id',
    ];

    public function enrollment() {
        return $this->belongsTo(Enrollments::class, 'enrollment_id');
    }

    public function exam() {
        return $this->belongsTo(Exams::class, 'exam_id');
    }
}
