<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'isbn',
        'category',
    ];

    public function book_copies() {
        return $this->hasMany(BookCopies::class, 'book_id');
    }

    public function book_loans() {
        return $this->hasMany(BookLoans::class, 'book_id');
    }

    public function book_authors() {
        return $this->hasMany(BookAuthors::class, 'book_id');
    }
}
