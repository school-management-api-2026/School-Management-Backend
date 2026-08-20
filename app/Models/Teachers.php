<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    use HasFactory;
    protected $fillable = [
        'hire_date',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payrolls() {
        return $this->hasMany(Payrolls::class, 'teacher_id');
    }

    public function teacher_courses() {
        return $this->hasMany(TeacherCourses::class, 'teacher_id');
    }

    public function exams() {
        return $this->hasMany(Exams::class, 'teacher_id');
    }

}
