<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Data kategori produk untuk pabrik produksi daging unggas
        $categories = [
            [
                'name' => 'Daging Ayam Segar',
                'description' => 'Daging ayam segar yang telah dipotong dan siap dijual dalam bentuk potongan tertentu.'
            ],
            [
                'name' => 'Daging Ayam Olahan',
                'description' => 'Produk-produk yang dibuat dari daging ayam, seperti nugget ayam, sosis ayam, dan produk olahan lainnya.'
            ],
            [
                'name' => 'Telur Ayam',
                'description' => 'Telur ayam segar yang dihasilkan oleh unggas.'
            ],
            [
                'name' => 'Pakan Ayam',
                'description' => 'Pakan yang digunakan untuk memberi makan ayam dan menjaga kesehatan serta pertumbuhan mereka.'
            ],
            [
                'name' => 'Obat-obatan dan Vaksin',
                'description' => 'Produk-produk kesehatan hewan yang digunakan untuk mencegah atau mengobati penyakit ayam.'
            ],
            [
                'name' => 'Peralatan Kandang',
                'description' => 'Peralatan seperti kandang, tempat makan, tempat minum, dan peralatan lain yang digunakan untuk pemeliharaan ayam.'
            ],
            [
                'name' => 'Produk Limbah Organik',
                'description' => 'Produk-produk yang dihasilkan dari limbah organik ayam, seperti pupuk organik.'
            ],
            [
                'name' => 'Produk Sampingan',
                'description' => 'Produk-produk sampingan yang dihasilkan selama pemrosesan ayam, seperti bulu ayam, darah ayam, dan tulang yang dapat digunakan untuk berbagai tujuan.'
            ],
            [
                'name' => 'Produk Kesehatan Hewan',
                'description' => 'Produk-produk kesehatan hewan seperti suplemen nutrisi, obat-obatan, dan vaksin untuk menjaga kesehatan ayam.'
            ],
            [
                'name' => 'Produk Organik',
                'description' => 'Produk-produk ayam organik yang diproduksi sesuai dengan standar organik dan non-GMO.'
            ],
            [
                'name' => 'Produk Ayam Beku',
                'description' => 'Daging ayam yang telah diolah dan dibekukan untuk penyimpanan jangka panjang.'
            ],
        ];

        // Masukkan data kategori produk ke dalam tabel 'products_categories'
        foreach ($categories as $category) {
            DB::table('products_categories')->insert($category);
        }
    }
}
