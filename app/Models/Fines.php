<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fines extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'paid_status',
        'book_loan_id',
    ];

    public function book_loan() {
        return $this->belongsTo(BookLoans::class, 'book_loan_id');
    }
}
