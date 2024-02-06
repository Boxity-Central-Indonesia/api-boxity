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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts'); // Merujuk ke tabel accounts
            $table->date('date');
            $table->decimal('debit', 10, 2)->default(0); // Jumlah dalam debit
            $table->decimal('credit', 10, 2)->default(0); // Jumlah dalam kredit
            $table->text('description')->nullable(); // Deskripsi transaksi
            $table->unsignedBigInteger('transaction_id')->nullable(); // Opsional: Relasi ke transactions jika ada
            $table->timestamps();

            // Opsional: relasi ke tabel lain seperti orders jika perlu
            $table->foreign('transaction_id')->references('id')->on('accounts_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
