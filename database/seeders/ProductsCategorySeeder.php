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
                'name' => 'Ayam Karkas',
                'description' => 'Daging ayam utuh tanpa kepala dan ceker yang sudah dibersihkan dari bulunya juga jeroannya.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273620/ptDHKManufacturing/kategori/dagingayamsegar_gq4pt8.png',
                'type' => 'Karkas',
            ],
            [
                'name' => 'Daging Ayam Olahan',
                'description' => 'Produk-produk yang dibuat dari daging ayam, seperti nugget ayam, sosis ayam, dan produk olahan lainnya.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273622/ptDHKManufacturing/kategori/dagingayamolahan_jwq3mw.png',
                'type' => 'Olahan',
            ],
            [
                'name' => 'Telur Ayam',
                'description' => 'Telur ayam segar yang dihasilkan oleh unggas.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273619/ptDHKManufacturing/kategori/telurayam_enr1oh.png',
                'type' => 'Telur',
            ],
            [
                'name' => 'Pakan Ayam',
                'description' => 'Pakan yang digunakan untuk memberi makan ayam dan menjaga kesehatan serta pertumbuhan mereka.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273621/ptDHKManufacturing/kategori/pakanayam_a0sfyk.png',
                'type' => 'Pakan',
            ],
            [
                'name' => 'Obat-obatan dan Vaksin',
                'description' => 'Produk-produk kesehatan hewan yang digunakan untuk mencegah atau mengobati penyakit ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274345/ptDHKManufacturing/kategori/obatvaksin_upc2nd.png',
                'type' => 'Vaksin',
            ],
            [
                'name' => 'Peralatan Kandang',
                'description' => 'Peralatan seperti kandang, tempat makan, tempat minum, dan peralatan lain yang digunakan untuk pemeliharaan ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273621/ptDHKManufacturing/kategori/peralatankandang_kwcfin.png',
                'type' => 'Peralatan',
            ],
            [
                'name' => 'Produk Limbah Organik',
                'description' => 'Produk-produk yang dihasilkan dari limbah organik ayam, seperti pupuk organik.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Organik',
            ],
            [
                'name' => 'Produk Sampingan',
                'description' => 'Produk-produk sampingan yang dihasilkan selama pemrosesan ayam, seperti bulu ayam, darah ayam, dan tulang yang dapat digunakan untuk berbagai tujuan.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Sampingan',
            ],
            [
                'name' => 'Produk Kesehatan Hewan',
                'description' => 'Produk-produk kesehatan hewan seperti suplemen nutrisi, obat-obatan, dan vaksin untuk menjaga kesehatan ayam.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Sampingan',
            ],
            [
                'name' => 'Produk Organik',
                'description' => 'Produk-produk ayam organik yang diproduksi sesuai dengan standar organik dan non-GMO.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Organik',
            ],
            [
                'name' => 'Produk Ayam Beku',
                'description' => 'Daging ayam yang telah diolah dan dibekukan untuk penyimpanan jangka panjang.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Olahan',
            ],
            [
                'name' => 'Ayam',
                'description' => 'Daging ayam segar yang dapat digunakan untuk berbagai jenis masakan.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273620/ptDHKManufacturing/kategori/ayam_wobgug.png',
                'type' => 'Ayam',
            ],
            [
                'name' => 'Boneless + Kulit',
                'description' => 'Kombinasi daging ayam tanpa tulang dengan kulit, memberikan rasa dan tekstur yang lezat.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273621/ptDHKManufacturing/kategori/bonelesskulit_qokct2.png',
                'type' => 'Parting',
            ],
            [
                'name' => 'Dada',
                'description' => 'Potongan daging ayam dada yang siap untuk dimasak, cocok untuk berbagai resep.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Parting',
            ],
            [
                'name' => 'Daging Sapi',
                'description' => 'Daging sapi segar yang dapat digunakan dalam berbagai hidangan daging sapi.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274344/ptDHKManufacturing/kategori/dagingsapi_cd9vva.png',
                'type' => 'Sapi',
            ],
            [
                'name' => 'Fillet',
                'description' => 'Potongan fillet daging ayam atau daging sapi, ideal untuk hidangan tanpa tulang.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274345/ptDHKManufacturing/kategori/fillet_cxcrys.png',
                'type' => 'Fillet',
            ],
            [
                'name' => 'Hati & Ampla',
                'description' => 'Hati ayam yang lezat dan potongan daging ayam ampla, memberikan variasi dalam menu masakan Anda.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274349/ptDHKManufacturing/kategori/hatiamplaayam_zv1c1d.png',
                'type' => 'Jeroan',
            ],
            [
                'name' => 'Kaki & Leher',
                'description'
                => 'Potongan kaki dan leher ayam, dapat digunakan untuk membuat kaldu atau hidangan tradisional lainnya.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274346/ptDHKManufacturing/kategori/kakileherayam_rdvqmu.png',
                'type' => 'Parting',
            ],
            [
                'name' => 'Kerongkong & Tunggir',
                'description' => 'Potongan kerongkong ayam dan tunggir, memberikan pilihan unik untuk hidangan Anda.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Jeroan',
            ],
            [
                'name' => 'Kulit',
                'description' => 'Kulit ayam yang garing, cocok sebagai camilan atau tambahan pada hidangan.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Parting',
            ],
            [
                'name' => 'Lemak & MDM',
                'description' => 'Lemak ayam dan Meat Defatted Mechanically (MDM), dapat digunakan sebagai bahan tambahan pada masakan atau untuk membuat saus.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Parting',
            ],
            [
                'name' => 'Paha Ayam',
                'description' => 'Potongan daging ayam paha, memberikan pilihan berbeda untuk hidangan Anda.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274515/ptDHKManufacturing/kategori/pahaayam_hlqtl4.png',
                'type' => 'Parting',
            ],
            [
                'name' => 'Sayap',
                'description' => 'Sayap ayam yang lezat, dapat digoreng atau dipanggang sesuai selera Anda.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Parting',
            ],
            [
                'name' => 'Trimming',
                'description' => 'Potongan daging ayam dengan sedikit trimming, memudahkan dalam persiapan masakan.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713271699/ptDHKManufacturing/no-image_erzvuc.jpg',
                'type' => 'Parting',
            ],
            [
                'name' => 'Bebek',
                'description' => 'Daging bebek segar yang dapat digunakan dalam berbagai hidangan bebek.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273620/ptDHKManufacturing/kategori/bebek_zkry4s.png',
                'type' => 'Bebek',
            ],
            [
                'name' => 'Sapi',
                'description' => 'Daging sapi segar yang dapat digunakan dalam berbagai hidangan daging sapi.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713274347/ptDHKManufacturing/kategori/sapi_ggqken.png',
                'type' => 'Sapi',
            ],
            [
                'name' => 'Seafood',
                'description' => 'Berbagai jenis seafood seperti ikan, udang, dan kerang, dapat digunakan untuk berbagai hidangan laut.',
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1713273622/ptDHKManufacturing/kategori/seafood_zixph0.png',
                'type' => 'Seafood',
            ],
        ];

        // Masukkan data kategori produk ke dalam tabel 'products_categories'
        foreach ($categories as $category) {
            DB::table('products_categories')->insert($category);
        }
    }
}