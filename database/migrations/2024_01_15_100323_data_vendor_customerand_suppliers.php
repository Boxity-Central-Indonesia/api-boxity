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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('email');
            $table->date('date_of_birth')->nullable();
            $table->timestamps();
            $table->enum('transaction_type', ['outbound', 'inbound']);
        });
        Schema::create('vendor_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendors_id');
            $table->string('name');
            $table->string('position');
            $table->string('phone_number');
            $table->timestamps();

            $table->foreign('vendors_id')->references('id')->on('vendors')->onDelete('cascade');
        });

        Schema::create('vendor_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendors_id');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('taxes', 10, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('vendors_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('vendor_contacts');
        Schema::dropIfExists('vendor_transactions');
    }
};
