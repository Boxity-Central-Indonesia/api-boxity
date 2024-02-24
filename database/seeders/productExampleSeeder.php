<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductsCategory;
use Faker\Factory as Faker;

class productExampleSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $productsList = [
            'Ayam Utuh' => 'Daging ayam utuh yang segar dan siap untuk diproses.',
            'Ayam Giling' => 'Daging ayam yang telah digiling, cocok untuk berbagai jenis masakan.',
            'Boneless Dada (BLD)' => 'Daging ayam dada tanpa tulang, ideal untuk hidangan yang membutuhkan daging tanpa tulang.',
            'Boneless Dada + Kulit (BLD + KULIT)' => 'Kombinasi daging ayam dada tanpa tulang dengan kulit, memberikan rasa yang lezat.',
            'Boneless Dada Special (BLD SP)' => 'Varian spesial dari daging ayam dada tanpa tulang, diracik dengan bumbu istimewa.',
            'Boneless Paha (BLP)' => 'Daging ayam paha tanpa tulang, memberikan pilihan yang berbeda untuk hidangan Anda.',
            'Boneless Paha + Kulit (BLP + KULIT)' => 'Campuran daging ayam paha tanpa tulang dengan kulit, cocok untuk berbagai resep.',
            'Dada Potong' => 'Potongan daging ayam dada yang siap untuk dimasak.',
            'Dada Utuh' => 'Daging ayam dada utuh, cocok untuk dipanggang atau diolah sesuai selera Anda.',
            'Fillet' => 'Potongan fillet daging ayam, sangat baik untuk hidangan yang membutuhkan daging tanpa tulang.',
            'Sayap' => 'Sayap ayam yang lezat, cocok untuk digoreng atau dipanggang.',
            'Kulit' => 'Kulit ayam yang garing, bisa digunakan sebagai camilan atau tambahan pada hidangan.',
            'Trimming Dada' => 'Potongan daging ayam dada dengan sedikit trimming, memudahkan dalam persiapan masakan.',
            'Trimming Paha' => 'Potongan daging ayam paha dengan sedikit trimming, siap untuk dimasak.',
            'Kerongkong' => 'Potongan daging ayam kerongkong, memberikan pilihan unik untuk hidangan Anda.',
            'Hati' => 'Hati ayam yang lezat, cocok untuk dimasak sebagai hidangan sampingan.',
            'Ampla' => 'Potongan daging ayam ampla, memberikan variasi dalam menu masakan Anda.',
            'Kepala Utuh' => 'Kepala ayam utuh, dapat digunakan untuk berbagai resep tradisional.',
            'Tulang Paha' => 'Tulang paha ayam yang dapat digunakan untuk membuat kaldu atau diolah menjadi hidangan lainnya.',
            'Lemak' => 'Lemak ayam yang dapat digunakan sebagai bahan tambahan pada masakan atau untuk membuat saus.',
            'DSSL halus' => 'Potongan daging ayam dada super halus, ideal untuk berbagai hidangan.',
            'DSSL Kasar' => 'Potongan daging ayam dada super kasar, memberikan tekstur yang unik pada masakan Anda.',
            'Sapi Giling' => 'Daging sapi yang telah digiling, dapat digunakan untuk berbagai resep daging sapi.',
        ];

        // Ambil semua kategori produk
        $categories = ProductsCategory::all();
        $codeCounter = 1;

        // Loop melalui list produk dan buat produk
        foreach ($productsList as $productName => $description) {
            // Cari kategori yang sesuai berdasarkan nama produk
            $categoryName = $this->getCategoryName($productName);
            $category = $categories->where('name', $categoryName)->first();

            // Jika kategori ditemukan, buat produk
            if ($category) {
                // Format kode sesuai kebutuhan (contoh: AU001)
                $code = 'AU' . str_pad($codeCounter, 3, '0', STR_PAD_LEFT);

                Product::create([
                    'name' => $productName,
                    'code' => $code,
                    'category_id' => $category->id,
                    'price' => $faker->randomFloat(2, 1000, 99000),
                    'animal_type' => 'Ayam',
                    'stock' => 300,
                    'unit_of_measure' => 'kg',
                    'warehouse_id' => 1,
                    'user_created' => 1,
                    'user_updated' => 1,
                    'description' => $description, // Tambahkan deskripsi
                ]);

                // Increment nomor urut kode
                $codeCounter++;
            }
        }
    }

    // Fungsi untuk mendapatkan nama kategori berdasarkan nama produk
    private function getCategoryName($productName)
    {
        // Dalam implementasi nyata, mungkin perlu logika yang lebih kompleks untuk menentukan kategori
        // Berikut adalah contoh yang sederhana
        $categoryMappings = [
            'Ayam Utuh' => 'Daging Ayam Segar',
            'Ayam Giling' => 'Daging Ayam Olahan',
            'Boneless Dada (BLD)' => 'Daging Ayam Olahan',
            'Boneless Dada + Kulit (BLD + KULIT)' => 'Daging Ayam Olahan',
            'Boneless Dada Special (BLD SP)' => 'Daging Ayam Olahan',
            'Boneless Paha (BLP)' => 'Daging Ayam Olahan',
            'Boneless Paha + Kulit (BLP + KULIT)' => 'Daging Ayam Olahan',
            'Dada Potong' => 'Daging Ayam Segar',
            'Dada Utuh' => 'Daging Ayam Segar',
            'Fillet' => 'Daging Ayam Segar',
            'Sayap' => 'Daging Ayam Segar',
            'Kulit' => 'Daging Ayam Olahan',
            'Trimming Dada' => 'Daging Ayam Olahan',
            'Trimming Paha' => 'Daging Ayam Olahan',
            'Kerongkong' => 'Daging Ayam Olahan',
            'Hati' => 'Daging Ayam Olahan',
            'Ampla' => 'Daging Ayam Olahan',
            'Kepala Utuh' => 'Produk Sampingan',
            'Tulang Paha' => 'Produk Sampingan',
            'Lemak' => 'Produk Sampingan',
            'DSSL halus' => 'Produk Ayam Beku',
            'DSSL Kasar' => 'Produk Ayam Beku',
            'Sapi Giling' => 'Daging Ayam Olahan',
        ];

        // Mencari kategori yang sesuai dengan nama produk
        foreach ($categoryMappings as $productNamePattern => $categoryName) {
            if (str_contains($productName, $productNamePattern)) {
                return $categoryName;
            }
        }

        // Jika tidak ada pemetaan yang sesuai, kembalikan 'Uncategorized'
        return 'Uncategorized';
    }
}
