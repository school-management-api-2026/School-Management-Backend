<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount_paid',
        'payment_method',
        'payment_date',
        'status',
        'invoice_id',
    ];

    public function invoice() {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }
}
