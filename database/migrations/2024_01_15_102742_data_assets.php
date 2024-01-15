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

        Schema::create('asset_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->timestamps();
        });

        Schema::create('asset_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('condition');
            $table->timestamps();
        });
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['tangible', 'intangible'])->default('tangible');
            $table->text('description')->nullable();
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 10, 2);
            $table->decimal('book_value', 10, 2);
            $table->foreignId('location_id')->nullable()->references('id')->on('asset_locations');
            $table->foreignId('condition_id')->nullable()->references('id')->on('asset_conditions');
            $table->timestamps();
        });

        Schema::create('asset_depreciation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained();
            $table->enum('method', ['linear', 'declining_balance', 'sum_of_the_years_digits', 'units_of_production', 'double_declining_balance'])->default('linear');
            $table->integer('useful_life');
            $table->decimal('residual_value', 10, 2);
            $table->date('start_date');
            $table->decimal('current_value', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_locations');
        Schema::dropIfExists('asset_conditions');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_depreciation');
    }
};
