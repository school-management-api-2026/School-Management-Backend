<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;
    protected $fillable = [
        'unit_price',
        'promotion',
        'capacity',
        'start_date',
        'end_date',
        'subject_id',
    ];

    public function subject() {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollments::class ,'course_id');
    }

    public function exams() {
        return $this->hasMany(Exams::class, 'course_id');
    }
    
    public function teacher_courses() {
        return $this->hasMany(TeacherCourses::class, 'course_id');
    }
}
