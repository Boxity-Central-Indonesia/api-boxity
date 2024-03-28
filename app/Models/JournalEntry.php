<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class JournalEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'date',
        'debit',
        'credit',
        'description',
        'transaction_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
    }
    public function transaction()
    {
        return $this->belongsTo(AccountsTransaction::class, 'transaction_id');
    }
}
