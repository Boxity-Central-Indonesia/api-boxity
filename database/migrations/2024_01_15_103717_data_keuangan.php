<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense'])->default('asset');
            $table->decimal('balance', 10, 2);
            $table->timestamps();
        });

        Schema::create('accounts_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->foreignId('account_id')->constrained();
            $table->timestamps();
        });

        Schema::create('accounts_balances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('balance', 10, 2);
            $table->foreignId('account_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('accounts_transactions');
        Schema::dropIfExists('accounts_balances');
    }
};
