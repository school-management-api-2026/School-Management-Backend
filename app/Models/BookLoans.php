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
        'user_id',
        'book_copy_id',
        'library_staff_id',
    ];

    public function student() {
        return $this->belongsTo(User::class, 'library_staff_id');
    }

    public function book() {
        return $this->belongsTo(BookCopies::class, 'book_copy_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fines() {
        return $this->hasMany(fines::class, 'book_loan_id');
    }
}
