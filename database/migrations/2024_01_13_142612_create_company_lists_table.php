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
        Schema::create('company_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('no_handphone');
            $table->string('alamat');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country');
            $table->string('industry');
            $table->longText('description')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_lists');
    }
};
