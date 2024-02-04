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
        Schema::create('manufacturer_slaughtering', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Mengacu pada ID hewan dalam tabel `products`
            $table->date('slaughter_date');
            $table->enum('method', ['halal', 'electrical stunning', 'kosher', 'captive bolt', 'gas killing', 'sticking'])->default('halal');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
        Schema::create('manufacturer_carcasses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slaughtering_id');
            $table->decimal('weight_after_slaughter', 8, 2);
            $table->string('quality_grade');
            $table->timestamps();

            $table->foreign('slaughtering_id')->references('id')->on('manufacturer_slaughtering');
        });
        Schema::create('manufacturer_viscera', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carcass_id');
            $table->string('type');
            $table->string('handling_method');
            $table->timestamps();

            $table->foreign('carcass_id')->references('id')->on('manufacturer_carcasses');
        });
        Schema::create('packaging', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Mengacu pada produk yang dikemas
            $table->decimal('weight', 8, 2);
            $table->string('package_type');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturer_slaughtering');
        Schema::dropIfExists('manufacturer_carcasses');
        Schema::dropIfExists('manufacturer_viscera');
        Schema::dropIfExists('packaging');
    }
};
