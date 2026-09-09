<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'time_in',
        'time_out',
        'status',
        'user_id',
        'teacher_course_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teacher_course() {
        return $this->belongsTo(TeacherCourses::class, 'teacher_course_id');
    }
}
