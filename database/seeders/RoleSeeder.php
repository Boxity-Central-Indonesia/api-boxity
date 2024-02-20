<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Memastikan PermissionSeeder dijalankan terlebih dahulu
        $this->call(PermissionSeeder::class);

        // Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $financeRole = Role::firstOrCreate(['name' => 'Finance']);

        // Mendapatkan semua permissions
        $allPermissions = Permission::all();

        // Mendapatkan permissions spesifik
        $reportPermissions = Permission::whereIn('name', [
            'view_sales_report',
            'view_purchase_report',
            'view_revenue_report',
            'view_expenses_report',
            'view_inventory_report',
            'view_production_reports',
        ])->get();

        // Menetapkan semua permissions ke Super Admin
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Menetapkan permissions terpilih ke Admin
        $adminPermissions = $allPermissions->filter(function ($permission) {
            return in_array($permission->name, [
                'view_users',
                'create_users',
                'update_users',
                'delete_users',
            ]);
        });
        $adminRole->permissions()->sync($adminPermissions->pluck('id'));

        // Menetapkan permissions terpilih ke Finance
        $financePermissions = $allPermissions->filter(function ($permission) {
            return in_array($permission->name, [
                'view_accounting',
                'update_accounting',
            ]);
        });
        $financeRole->permissions()->sync($financePermissions->pluck('id'));

        // Menetapkan permission untuk melihat laporan ke Admin dan Finance
        $adminRole->permissions()->syncWithoutDetaching($reportPermissions->pluck('id'));
        $financeRole->permissions()->syncWithoutDetaching($reportPermissions->pluck('id'));
    }
}
