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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->decimal('capacity', 10, 2);
            $table->timestamps();
        });

        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('number');
            $table->decimal('capacity', 10, 2);
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        Schema::create('products_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('type')->nullable();
            $table->string('subtype')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->boolean('raw_material')->default(false);
            $table->string('unit_of_measure')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('products_categories');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
        Schema::create('products_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('selling_price', 10, 2);
            $table->decimal('buying_price', 10, 2);
            $table->decimal('discount_price', 10, 2);
            $table->timestamps();

            // Buat constraint untuk kolom product_id
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::create('products_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->enum('movement_type', ['purchase', 'sale', 'transfer']);
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('products_categories');
        Schema::dropIfExists('products_prices');
        Schema::dropIfExists('products_movements');
    }
};
