<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book_loan() {
        return $this->belongsTo(BookLoans::class, 'student_id');
    }

    public function student_parent() {
        return $this->belongsTo(StudentParents::class, 'student_id');
    }

    public function enrollment() {
        return $this->belongsTo(Enrollments::class, 'student_id');
    }
}
