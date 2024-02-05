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
        Schema::table('manufacturer_processing_activities', function (Blueprint $table) {
            $table->string('status_activities')->after('activity_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufacturer_processing_activities', function (Blueprint $table) {
            $table->dropColumn('status_activities');
        });
    }
};
