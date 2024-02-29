<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Users
            ['name' => 'view_users'],
            ['name' => 'create_users'],
            ['name' => 'update_users'],
            ['name' => 'delete_users'],

            // Companies
            ['name' => 'view_companies'],
            ['name' => 'create_companies'],
            ['name' => 'update_companies'],
            ['name' => 'delete_companies'],

            // Employees
            ['name' => 'view_employees'],
            ['name' => 'create_employees'],
            ['name' => 'update_employees'],
            ['name' => 'delete_employees'],

            // Leads Prospect
            ['name' => 'view_leads'],
            ['name' => 'create_leads'],
            ['name' => 'update_leads'],
            ['name' => 'delete_leads'],

            // Role & Permission
            ['name' => 'view_roles_permissions'],
            ['name' => 'create_roles_permissions'],
            ['name' => 'update_roles_permissions'],
            ['name' => 'delete_roles_permissions'],

            // Accounting
            ['name' => 'view_accounting'],
            ['name' => 'update_accounting'],

            // Warehouse
            ['name' => 'view_warehouse'],
            ['name' => 'create_warehouse'],
            ['name' => 'update_warehouse'],
            ['name' => 'delete_warehouse'],

            // Product
            ['name' => 'view_product'],
            ['name' => 'create_product'],
            ['name' => 'update_product'],
            ['name' => 'delete_product'],

            // Vendor
            ['name' => 'view_vendor'],
            ['name' => 'create_vendor'],
            ['name' => 'update_vendor'],
            ['name' => 'delete_vendor'],

            // Transactions
            ['name' => 'view_transactions'],

            ['name' => 'view_orders'],
            ['name' => 'create_orders'],
            ['name' => 'update_orders'],
            ['name' => 'delete_orders'],

            ['name' => 'view_invoices'],
            ['name' => 'create_invoices'],
            ['name' => 'update_invoices'],
            ['name' => 'delete_invoices'],

            ['name' => 'view_payments'],
            ['name' => 'create_payments'],
            ['name' => 'update_payments'],
            ['name' => 'delete_payments'],

            ['name' => 'view_goods_receipts'],
            ['name' => 'create_goods_receipts'],
            ['name' => 'update_goods_receipts'],
            ['name' => 'delete_goods_receipts'],

            ['name' => 'view_delivery_notes'],
            ['name' => 'create_delivery_notes'],
            ['name' => 'update_delivery_notes'],
            ['name' => 'delete_delivery_notes'],

            // Manufacturer
            ['name' => 'view_manufacturers'],
            ['name' => 'create_manufacturers'],
            ['name' => 'update_manufacturers'],
            ['name' => 'delete_manufacturers'],

            // Process Activity
            ['name' => 'view_process_activities'],
            ['name' => 'create_process_activities'],
            ['name' => 'update_process_activities'],
            ['name' => 'delete_process_activities'],

            // Packaging Data
            ['name' => 'view_packaging_data'],
            ['name' => 'create_packaging_data'],
            ['name' => 'update_packaging_data'],
            ['name' => 'delete_packaging_data'],

            // Production Report
            ['name' => 'view_production_reports'],
            ['name' => 'create_production_reports'],
            ['name' => 'update_production_reports'],
            ['name' => 'delete_production_reports'],

            ['name' => 'view_sales_report'],
            ['name' => 'generate_sales_report'],

            ['name' => 'view_purchase_report'],
            ['name' => 'generate_purchase_report'],

            ['name' => 'view_revenue_report'],
            ['name' => 'generate_revenue_report'],

            ['name' => 'view_expenses_report'],
            ['name' => 'generate_expenses_report'],

            ['name' => 'view_inventory_report'],
            ['name' => 'generate_inventory_report'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
