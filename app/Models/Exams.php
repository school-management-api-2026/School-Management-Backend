<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exams extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_type',
        'exam_date',
        'subject_id',
        'teacher_id',
        'course_id',
    ];

    public function subject() {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function teacher() {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function course() {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function results() {
        return $this->hasMany(Results::class, 'exam_id');
    }
}
