<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_amount',
        'due_date',
        'status',
        'enrollment_id',
    ];

    public function enrollment() {
        return $this->belongsTo(Enrollments::class, 'enrollment_id');
    }

    public function payments() {
        return $this->hasMany(Payments::class, 'invoice_id');
    }
}
