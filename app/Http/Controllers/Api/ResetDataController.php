<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\AccountsBalance;
use App\Models\AccountsTransaction;
use App\Models\Asset;
use App\Models\AssetCondition;
use App\Models\AssetDepreciation;
use App\Models\AssetLocation;
use App\Models\CompaniesBranch;
use App\Models\CompaniesDepartment;
use App\Models\Company;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Employee;
use App\Models\EmployeeCategory;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Packaging;
use App\Models\Payment;
use App\Models\ProcessingActivity;
use App\Models\Product;
use App\Models\ProductsCategory;
use App\Models\ProductsMovement;
use App\Models\ProductsPrice;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Models\VendorTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use DB;
use Illuminate\Http\Request;

class ResetDataController extends Controller
{
    public function reset(){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AccountsBalance::truncate();
        AccountsTransaction::truncate();
        JournalEntry::truncate();
        Account::truncate();
        Asset::truncate();
        AssetLocation::truncate();
        AssetCondition::truncate();
        AssetDepreciation::truncate();
        Company::truncate();
        CompaniesBranch::truncate();
        CompaniesDepartment::truncate();
        CompaniesDepartment::truncate();
        DeliveryNote::truncate();
        DeliveryNoteItem::truncate();
        Employee::truncate();
        EmployeeCategory::truncate();
        GoodsReceipt::truncate();
        GoodsReceiptItem::truncate();
        Invoice::truncate();
        Lead::truncate();
        Order::truncate();
        OrderProduct::truncate();
        Package::truncate();
        Packaging::truncate();
        PackageProduct::truncate();
        Payment::truncate();
        ProcessingActivity::truncate();
        Product::truncate();
        ProductsCategory::truncate();
        ProductsMovement::truncate();
        ProductsPrice::truncate();
        Vendor::truncate();
        VendorContact::truncate();
        VendorTransaction::truncate();
        Warehouse::truncate();
        WarehouseLocation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return response()->json(
            [
                'status'=>201,
                'message'=>'Data berhasil di-reset dan di-format.'
            ]);
    }
}
