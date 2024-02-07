<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class vendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendors')->insert([
            'name' => 'PT SUPPLIER ABC',
            'address' => 'Jalan Industri No 5',
            'phone_number' => '089876543210',
            'email' => 'vendor@example.com',
            'date_of_birth' => '1985-05-05', // Contoh tanggal, sesuaikan jika perlu
            'transaction_type' => 'inbound',
        ]);
        DB::table('vendors')->insert([
            'name' => 'PT CUSTOMER XYZ',
            'address' => 'Jalan Krakatau Gg Aman No 8',
            'phone_number' => '089876543210',
            'email' => 'customer-xyz@example.com',
            'date_of_birth' => '1985-05-05', // Contoh tanggal, sesuaikan jika perlu
            'transaction_type' => 'outbound',
        ]);
    }
}
