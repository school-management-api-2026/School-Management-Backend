<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_of_week',
        'time_start',
        'time_out',
        'teacher_course_id',
        'room_id',
    ];

    public function room() {
        return $this->belongsTo(Rooms::class, 'room_id');
    }

    public function teacher_course() {
        return $this->belongsTo(TeacherCourses::class, 'teacher_course_id');
    }
}
