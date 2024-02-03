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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('photo_profile')->default('https://res.cloudinary.com/boxity-id/image/upload/v1704964166/rrsa0vdjtiat56tw000t.png');
            $table->text('full_address');
            $table->string('phone_number');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bisnis');
            $table->text('full_address');
            $table->string('email')->unique();
            $table->string('website')->nullable();
            $table->string('phone_number');
            $table->string('pic_business');
            $table->string('bank_account_name');
            $table->string('bank_branch');
            $table->string('bank_account_number');
            // Field tambahan yang saya rekomendasikan
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('businesses');
    }
};
