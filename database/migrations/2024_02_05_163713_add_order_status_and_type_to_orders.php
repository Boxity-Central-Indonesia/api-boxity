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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_status', ['Pending Confirmation', 'In Production', 'Packaging', 'Completed', 'Cancelled', 'Shipped']);
            $table->enum('order_type', ['Direct Order', 'Production Order']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_status', 'order_type']);
        });
    }
};
