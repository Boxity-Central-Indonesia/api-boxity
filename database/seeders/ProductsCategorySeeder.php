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
                'description' => 'Daging ayam segar yang telah dipotong dan siap dijual dalam bentuk potongan tertentu.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273620/ptDHKManufacturing/kategori/dagingayamsegar_gq4pt8.png'
            ],
            [
                'name' => 'Daging Ayam Olahan',
                'description' => 'Produk-produk yang dibuat dari daging ayam, seperti nugget ayam, sosis ayam, dan produk olahan lainnya.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273622/ptDHKManufacturing/kategori/dagingayamolahan_jwq3mw.png'
            ],
            [
                'name' => 'Telur Ayam',
                'description' => 'Telur ayam segar yang dihasilkan oleh unggas.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273619/ptDHKManufacturing/kategori/telurayam_enr1oh.png'
            ],
            [
                'name' => 'Pakan Ayam',
                'description' => 'Pakan yang digunakan untuk memberi makan ayam dan menjaga kesehatan serta pertumbuhan mereka.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273621/ptDHKManufacturing/kategori/pakanayam_a0sfyk.png'
            ],
            [
                'name' => 'Obat-obatan dan Vaksin',
                'description' => 'Produk-produk kesehatan hewan yang digunakan untuk mencegah atau mengobati penyakit ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274345/ptDHKManufacturing/kategori/obatvaksin_upc2nd.png'
            ],
            [
                'name' => 'Peralatan Kandang',
                'description' => 'Peralatan seperti kandang, tempat makan, tempat minum, dan peralatan lain yang digunakan untuk pemeliharaan ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273621/ptDHKManufacturing/kategori/peralatankandang_kwcfin.png'
            ],
            [
                'name' => 'Produk Limbah Organik',
                'description' => 'Produk-produk yang dihasilkan dari limbah organik ayam, seperti pupuk organik.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg'
            ],
            [
                'name' => 'Produk Sampingan',
                'description' => 'Produk-produk sampingan yang dihasilkan selama pemrosesan ayam, seperti bulu ayam, darah ayam, dan tulang yang dapat digunakan untuk berbagai tujuan.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg'
            ],
            [
                'name' => 'Produk Kesehatan Hewan',
                'description' => 'Produk-produk kesehatan hewan seperti suplemen nutrisi, obat-obatan, dan vaksin untuk menjaga kesehatan ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg'
            ],
            [
                'name' => 'Produk Organik',
                'description' => 'Produk-produk ayam organik yang diproduksi sesuai dengan standar organik dan non-GMO.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg'
            ],
            [
                'name' => 'Produk Ayam Beku',
                'description' => 'Daging ayam yang telah diolah dan dibekukan untuk penyimpanan jangka panjang.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg'
            ],
        ];

        // Masukkan data kategori produk ke dalam tabel 'products_categories'
        foreach ($categories as $category) {
            DB::table('products_categories')->insert($category);
        }
    }
}
