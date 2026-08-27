<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book_loans() {
        return $this->hasMany(BookLoans::class, 'student_id');
    }

    public function student_parents() {
        return $this->hasMany(StudentParents::class, 'student_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollments::class, 'student_id');
    }
}
