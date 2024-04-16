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
        Schema::table('products_categories', function (Blueprint $table) {
            $table->string('image')->default('https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_categories', function (Blueprint $table) {
            $table->dropColumn('image');

        });
    }
};
