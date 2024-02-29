<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_method',
        'payment_date',
    ];
    protected $appends = ['kode_payment'];
    public function getKodePaymentAttribute()
    {
        return 'PAYRECEIPT/' . $this->created_at->format('Y') . '/' . $this->created_at->format('m') . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    // Hubungan ke Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    protected $casts = [
        'created_at' => 'datetime:d M, Y',
        'updated_at' => 'datetime:d M, Y',
    ];
}
