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

    public function users() {
        return $this->hasMany(User::class, 'user_id');
    }

    public function payroll() {
        return $this->belongsTo(Payrolls::class, 'teacher_id');
    }

    public function teacher_course() {
        return $this->belongsToMany(Courses::class, 'teacher_courses','teacher_Id','course_id');
    }

    public function exam() {
        return $this->belongsTo(Exams::class, 'teacher_id');
    }

}
