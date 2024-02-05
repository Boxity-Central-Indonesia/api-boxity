<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $accounts = [
            [
                'name' => 'Kas',
                'type' => 'Aset',
                'balance' => 0.00,
            ],
            [
                'name' => 'Piutang Usaha',
                'type' => 'Aset',
                'balance' => 0.00,
            ],
            [
                'name' => 'Persediaan',
                'type' => 'Aset',
                'balance' => 0.00,
            ],
            [
                'name' => 'Aset Tetap',
                'type' => 'Aset',
                'balance' => 0.00,
            ],
            [
                'name' => 'Utang Usaha',
                'type' => 'Liabilitas',
                'balance' => 0.00,
            ],
            [
                'name' => 'Utang Bank',
                'type' => 'Liabilitas',
                'balance' => 0.00,
            ],
            [
                'name' => 'Utang Gaji',
                'type' => 'Liabilitas',
                'balance' => 0.00,
            ],
            [
                'name' => 'Modal Saham',
                'type' => 'Ekuitas',
                'balance' => 0.00,
            ],
            [
                'name' => 'Laba Ditahan',
                'type' => 'Ekuitas',
                'balance' => 0.00,
            ],
            [
                'name' => 'Pendapatan Penjualan',
                'type' => 'Pendapatan',
                'balance' => 0.00,
            ],
            [
                'name' => 'Pendapatan Jasa',
                'type' => 'Pendapatan',
                'balance' => 0.00,
            ],
            [
                'name' => 'Beban Gaji',
                'type' => 'Biaya',
                'balance' => 0.00,
            ],
            [
                'name' => 'Beban Sewa',
                'type' => 'Biaya',
                'balance' => 0.00,
            ],
            [
                'name' => 'Beban Listrik',
                'type' => 'Biaya',
                'balance' => 0.00,
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
