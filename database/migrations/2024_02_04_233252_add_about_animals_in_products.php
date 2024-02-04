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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('description'); // Menambahkan untuk berat hewan
            $table->string('animal_type')->nullable()->after('weight'); // Menambahkan untuk jenis hewan
            $table->integer('age')->nullable()->after('animal_type'); // Menambahkan untuk usia hewan
            $table->string('health_status')->nullable()->after('age'); // Menambahkan untuk status kesehatan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight');
            $table->dropColumn('animal_type');
            $table->dropColumn('age');
            $table->dropColumn('health_status');
        });
    }
};
