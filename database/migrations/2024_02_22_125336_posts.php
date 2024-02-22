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
        Schema::create('posts_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('posts_categories');
            $table->timestamps();
        });
        Schema::create('posts_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('posts_id')->nullable();
            $table->foreign('posts_id')->references('id')->on('posts');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts_categories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('posts_comments');
    }
};
