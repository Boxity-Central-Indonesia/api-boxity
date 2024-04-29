<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class Account extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = auth()->id();
        });
        self::updating(function ($model) {
            $model->user_updated = auth()->id();
        });
    }
    public function transactions()
    {
        return $this->hasMany(AccountsTransaction::class);
    }

    public function balances()
    {
        return $this->hasMany(AccountsBalance::class);
    }
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }
}
