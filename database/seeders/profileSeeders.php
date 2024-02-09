<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class profileSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'nama_lengkap' => 'Bintang Cato Jeremia L Tobing',
                'full_address' => 'Jl Pelita IV Gg Aman No 7',
                'phone_number' => '081262845980',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Bahari Hari',
                'full_address' => 'Jl Alfalah 5 Kos',
                'phone_number' => '085928903321',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Arfiyan Tri Handoko',
                'full_address' => 'Bekasi',
                'phone_number' => '081234567890',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
