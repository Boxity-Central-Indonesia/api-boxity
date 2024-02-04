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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prospek');
            $table->string('email_prospek')->unique();
            $table->string('nomor_telepon_prospek')->nullable();
            $table->enum('tipe_prospek', ['perorangan', 'bisnis', 'rekomendasi'])->default('perorangan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
