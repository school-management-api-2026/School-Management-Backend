<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollments extends Model
{
    use HasFactory;
    protected $fillable = [
        'enrollment_date',
        'status',
        'course_id',
        'student_id',
    ];

    public function course() {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function student() {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function invoices() {
        return $this->hasMany(Invoices::class, 'enrollment_id');
    }

    public function results() {
        return $this->hasMany(Results::class, 'enrollment_id');
    }
}
