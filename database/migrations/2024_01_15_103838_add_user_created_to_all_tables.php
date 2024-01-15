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
        $tables = ['companies', 'companies_departments', 'companies_branches', 'employees', 'employees_categories', 'warehouses', 'warehouse_locations', 'products_categories', 'products_prices', 'products_movements', 'vendor_transactions', 'vendor_contacts', 'vendors', 'asset_locations', 'asset_conditions', 'assets', 'asset_depreciation', 'accounts', 'accounts_transactions', 'accounts_balances'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->integer('user_created')->unsigned()->nullable();
                $table->integer('user_updated')->unsigned()->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['companies', 'companies_departments', 'companies_branches', 'employees', 'employees_categories', 'warehouses', 'warehouse_locations', 'products_categories', 'products_prices', 'products_movements', 'vendor_transactions', 'vendor_contacts', 'vendors', 'asset_locations', 'asset_conditions', 'assets', 'asset_depreciation', 'accounts', 'accounts_transactions', 'accounts_balances'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('user_created');
                $table->dropColumn('user_updated');
            });
        }
    }
};
