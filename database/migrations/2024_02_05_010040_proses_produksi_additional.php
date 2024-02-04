<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('manufacturer_processing_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carcass_id'); // Mengganti 'reference_id' menjadi 'carcass_id'
            $table->enum('activity_type', ['scalding', 'feather_removal', 'deboning', 'parting', 'weighting'])->nullable();
            $table->json('details')->nullable(); // Menyimpan detail spesifik aktivitas
            $table->timestamps();

            // Menetapkan foreign key constraint
            $table->foreign('carcass_id')->references('id')->on('manufacturer_carcasses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturer_processing_activities');
    }
};
