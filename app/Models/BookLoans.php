<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoans extends Model
{
    use HasFactory;
    protected $fillable = [
        'due_date',
        'loan_date',
        'return_date',
        'student_id',
        'book_id',
        'user_id',
    ];

    public function student() {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function book() {
        return $this->belongsTo(Books::class, 'book_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fines() {
        return $this->hasMany(fines::class, 'book_loan_id');
    }
}
