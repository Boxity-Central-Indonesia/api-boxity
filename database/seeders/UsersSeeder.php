<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Bintang Tobing',
                'username' => 'bintangtobing',
                'email' => 'bintangjtobing@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('libra2110'), // Ganti dengan password yang sesuai
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1703865472/male_avatar_uhy4qg.svg',
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Bahari Hari',
                'username' => 'baharihari',
                'email' => 'baharihari49@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Ganti dengan password yang sesuai
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1703865472/male_avatar_uhy4qg.svg',
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Arfiyant',
                'username' => 'arfiyant',
                'email' => 'arfiyanth@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Ganti dengan password yang sesuai
                'image' => 'https://res.cloudinary.com/boxity-id/image/upload/v1703865472/male_avatar_uhy4qg.svg',
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
