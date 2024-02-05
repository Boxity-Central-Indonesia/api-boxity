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
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->date('activity_date')->nullable(); // Tanggal aktivitas, bisa jadi tanggal penyembelihan atau tanggal aktivitas lain
            $table->enum('activity_type', [
                'weight_based_ordering', // Pemesanan Berdasarkan Berat
                'unloading', // Unloading
                'weighing', // Penimbangan
                'slaughtering', // Penyembelihan
                'scalding', // Scalding Tank
                'feather_removal', // Pencabutan Bulu
                'carcass_washing', // Pencucian Karkas
                'viscera_removal', // Pengeluaran Jeroan
                'viscera_handling', // Penanganan Jeroan
                'carcass_washing_post', // Pencucian Karkas (Proses Kerja Bersih)
                'carcass_grading', // Seleksi Karkas (Grading)
                'carcass_weighing', // Penimbangan Karkas (Cutting)
                'deboning', // Pemisahan Daging dari Tulang (Deboning)
                'parting', // Pemotongan Karkas (Parting)
                'cut_weighing', // Penimbangan Hasil Potong
                'packaging', // Pengemasan
                'packaging_weighing' // Penimbangan Packaging
            ])->nullable();
            $table->json('details')->nullable(); // Menyimpan detail spesifik aktivitas, termasuk metode, berat setelah penyembelihan, grade kualitas, tipe dan metode penanganan jeroan, dll.
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
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
