<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'type')) {
                $table->dropColumn('type');
            }
            if (!Schema::hasColumn('accounts', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('accounts', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('code');
                $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade');
            }
        });

        $this->generateAccountCodes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', ['Aset', 'Liabilitas', 'Ekuitas', 'Pendapatan', 'Pengeluaran', 'Biaya'])->default('Aset')->after('id');
            $table->dropColumn('code');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }

    private function generateAccountCodes(): void
    {
        // Data akun dengan kode
        $accounts = [
            // Kategori
            ['code' => '1000', 'name' => 'Aset', 'parent_id' => null],
            ['code' => '2000', 'name' => 'Liabilitas', 'parent_id' => null],
            ['code' => '3000', 'name' => 'Ekuitas', 'parent_id' => null],
            ['code' => '4000', 'name' => 'Pendapatan', 'parent_id' => null],
            ['code' => '5000', 'name' => 'Biaya', 'parent_id' => null],
        ];

        foreach ($accounts as $accountData) {
            // Create the account without assigning parent_id
            $account = \App\Models\Account::create([
                'name' => $accountData['name'],
                'code' => $accountData['code'],
                'parent_id' => $accountData['parent_id'],
            ]);
        }

        // Create child accounts after parent accounts
        $childAccounts = [
            // Aset
            ['code' => '1101', 'name' => 'Kas', 'parent_code' => '1000'],
            ['code' => '1201', 'name' => 'Piutang Usaha', 'parent_code' => '1000'],
            ['code' => '1301', 'name' => 'Persediaan', 'parent_code' => '1000'],
            ['code' => '1401', 'name' => 'Aset Tetap', 'parent_code' => '1000'],

            // Liabilitas
            ['code' => '2101', 'name' => 'Utang Usaha', 'parent_code' => '2000'],
            ['code' => '2201', 'name' => 'Utang Bank', 'parent_code' => '2000'],
            ['code' => '2301', 'name' => 'Utang Gaji', 'parent_code' => '2000'],

            // Ekuitas
            ['code' => '3101', 'name' => 'Modal Saham', 'parent_code' => '3000'],
            ['code' => '3201', 'name' => 'Laba Ditahan', 'parent_code' => '3000'],

            // Pendapatan
            ['code' => '4101', 'name' => 'Pendapatan Penjualan', 'parent_code' => '4000'],
            ['code' => '4201', 'name' => 'Pendapatan Jasa', 'parent_code' => '4000'],

            // Biaya
            ['code' => '5101', 'name' => 'Beban Gaji', 'parent_code' => '5000'],
            ['code' => '5201', 'name' => 'Beban Sewa', 'parent_code' => '5000'],
            ['code' => '5301', 'name' => 'Beban Listrik', 'parent_code' => '5000'],
        ];

        foreach ($childAccounts as $accountData) {
            $parentAccount = \App\Models\Account::where('code', $accountData['parent_code'])->first();
            if ($parentAccount) {
                // Create the child account with the parent_id
                \App\Models\Account::create([
                    'name' => $accountData['name'],
                    'code' => $accountData['code'],
                    'parent_id' => $parentAccount->id,
                ]);
            }
        }
    }





};
