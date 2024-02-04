<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'total_amount',
        'paid_amount',
        'balance_due',
        'invoice_date',
        'due_date',
        'status',
    ];

    // Hubungan ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Hubungan ke Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
