<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Account;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'warehouse_id',
        'status',
        'details',
        'total_price',
        'taxes',
        'shipping_cost',
        'order_status',
        'order_type',
        'no_ref',
    ];
    protected $appends = ['kode_order'];
    public function getKodeOrderAttribute()
    {
        // Cek apakah ada relasi vendor terkait
        if ($this->vendor && $this->vendor->transaction_type) {
            $transactionType = $this->vendor->transaction_type;
            $prefix = ($transactionType == 'inbound') ? 'PO' : 'SO';
        } else {
            // Jika tidak ada informasi transaction_type, kembalikan format standar ORD
            $prefix = 'ORD';
        }

        if ($this->created_at) {
            return $prefix . '/' . $this->created_at->format('Y') . '/' . $this->created_at->format('m') . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
        }

        // Default value jika created_at null
        return $prefix . '/unknown_date/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    // Hubungan ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Hubungan ke Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function processingActivities()
    {
        return $this->hasMany(ProcessingActivity::class);
    }
    public function vendorTransaction()
    {
        return $this->hasOne(VendorTransaction::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('quantity', 'price_per_unit', 'total_price')
            ->withTimestamps();
    }
    public function calculateTotalPrice()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price_per_unit;
        }) + ($this->taxes ?? 0) + ($this->shipping_cost ?? 0);
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        static::saving(function ($order) {
            if ($order->products->isNotEmpty()) {
                $totalPrice = $order->products->reduce(function ($carry, $product) {
                    return $carry + ($product->pivot->quantity * $product->pivot->price_per_unit);
                }, 0);

                $order->total_price = $totalPrice + ($order->taxes ?? 0) + ($order->shipping_cost ?? 0);
            }
        });
        static::created(function ($order) {
            // DB::transaction(function () use ($order) {
            //     $invoice = $order->createInvoice();
            // });
        });
    }
    public function createInvoice()
    {
        // perhitungan total harga dan pembuatan invoice
        $totalAmount = $this->total_price;

        $invoice = $this->invoices()->create([
            'total_amount' => $totalAmount,
            'balance_due' => $totalAmount,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'unpaid',
        ]);

        // menentukan tipe transaksi dan membuat transaksi akun
        $transactionType = $this->vendor->transaction_type === 'outbound' ? 'credit' : 'debit';
        $accountId = $this->determineAccountId($transactionType);

        $description = "Invoice #{$invoice->id} created based on order #{$this->kode_order}";
        AccountsTransaction::create([
            'account_id' => $accountId,
            'date' => now(),
            'type' => $transactionType,
            'amount' => $invoice->total_amount,
            'description' => $description,
        ]);

        // penyesuaian saldo akun terkait
        if ($transactionType === 'credit') {
            $accountsReceivable = Account::firstOrCreate([
                'name' => 'Piutang Usaha',
                'type' => 'Aset',
            ]);
            $accountsReceivable->balance += $invoice->total_amount;
            $accountsReceivable->save();
        } elseif ($transactionType === 'debit') {
            $cashOrBankAccount = Account::firstOrCreate([
                'name' => 'Kas',
                'type' => 'Aset',
            ]);
            $cashOrBankAccount->balance -= $invoice->total_amount;
            $cashOrBankAccount->save();
        }

        return $invoice;
    }




    protected function determineAccountId(string $transactionType): ?int
    {
        if ($transactionType === 'credit') {
            // Cari atau buat record untuk akun 'Piutang Usaha'
            $account = Account::firstOrCreate([
                'name' => 'Piutang Usaha',
                'type' => 'Aset',
            ]);
        } elseif ($transactionType === 'debit') {
            // Cari atau buat record untuk akun 'Kas'
            $account = Account::firstOrCreate([
                'name' => 'Kas',
                'type' => 'Aset',
            ]);
        }

        // Kembalikan ID akun jika ditemukan, jika tidak, kembalikan null
        return $account ? $account->id : null;
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
