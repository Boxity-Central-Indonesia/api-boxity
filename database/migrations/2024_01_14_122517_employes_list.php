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
        Schema::create('employes_list', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip');
            $table->string('ttl');
            $table->string('alamat');
            $table->string('no_handphone');
            $table->string('email');
            $table->string('finger_code');
            $table->string('jenis_employes');
            $table->string('tanggal_mulai_kerja');
            $table->string('tanggal_resign');
            $table->string('bank');
            $table->string('no_rek');
            $table->string('jabatan');
            $table->string('atasan');
            $table->string('gaji_pokok');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes_list');
    }
};
