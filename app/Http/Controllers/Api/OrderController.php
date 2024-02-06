<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Models\Account;
use App\Models\AccountsTransaction;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductsMovement;
use App\Models\ProductsPrice;
use App\Models\Vendor;
use App\Models\VendorTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Menampilkan semua order
    public function index()
    {
        $orders = Order::with('vendor', 'product', 'warehouse', 'invoices')->get();
        return response()->json([
            'status' => 200,
            'data' => $orders,
            'message' => 'Orders retrieved successfully.',
        ]);
    }
    protected function updateAccountBalance($accountId, $amount, $type)
    {
        $account = Account::find($accountId);
        if (!$account) {
            throw new \Exception("Account not found.");
        }

        if ($type === 'credit') {
            $account->balance += $amount;
        } else if ($type === 'debit') {
            $account->balance -= $amount;
        }

        $account->save();
    }

    // Membuat order baru
    public function store(OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $order = new Order($validatedData);

            // Set status order berdasarkan tipe order
            $order->order_status = $order->order_type == 'Direct Order' ? 'Completed' : 'In Production';
            $order->save();

            $vendor = Vendor::find($validatedData['vendor_id']);

            // Buat transaksi vendor
            VendorTransaction::create([
                'vendors_id' => $validatedData['vendor_id'],
                'amount' => $validatedData['total_price'],
                'product_id' => $validatedData['product_id'],
                'unit_price' => $validatedData['price_per_unit'],
                'total_price' => $validatedData['total_price'],
                'taxes' => null, // Isi sesuai kebutuhan
                'shipping_cost' => null, // Isi sesuai kebutuhan
                'order_id' => $order->id,
            ]);

            // Logika akuntansi
            $this->handleAccounting($validatedData, $vendor);

            // Pencatatan pergerakan produk
            $this->recordProductMovement($validatedData, $vendor);
            // Tambahkan pembaruan harga produk di sini
            $this->updateProductPrices($validatedData, $vendor);

            DB::commit();
            return response()->json(['status' => 200, 'data' => $order, 'message' => 'Order created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => 'Failed to create order. Error: ' . $e->getMessage()]);
        }
    }

    private function handleAccounting($validatedData, $vendor)
    {
        $accountReceivable = Account::where('name', 'Piutang Usaha')->first();
        $accountPayable = Account::where('name', 'Utang Usaha')->first();
        $inventoryAccount = Account::where('name', 'Persediaan')->first();

        if ($vendor->transaction_type === 'outbound') {
            if ($accountReceivable) {
                $this->updateAccountBalance($accountReceivable->id, $validatedData['total_price'], 'debit');
            }
            if ($inventoryAccount) {
                $this->updateAccountBalance($inventoryAccount->id, $validatedData['total_price'], 'credit');
            }
        } else {
            if ($accountPayable) {
                $this->updateAccountBalance($accountPayable->id, $validatedData['total_price'], 'credit');
            }
            if ($inventoryAccount) {
                $this->updateAccountBalance($inventoryAccount->id, $validatedData['total_price'], 'debit');
            }
        }
    }

    private function recordProductMovement($validatedData, $vendor)
    {
        ProductsMovement::create([
            'product_id' => $validatedData['product_id'],
            'warehouse_id' => $validatedData['warehouse_id'],
            'movement_type' => $vendor->transaction_type === 'outbound' ? 'sale' : 'purchase',
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price_per_unit'],
        ]);
        $product = Product::findOrFail($validatedData['product_id']);

        // Perbarui stok produk berdasarkan tipe transaksi
        if ($vendor->transaction_type === 'outbound') {
            // Pastikan tidak mengurangi stok menjadi negatif
            $newQuantity = max($product->stock - $validatedData['quantity'], 0);
            $product->update(['stock' => $newQuantity]);
        } else {
            // Menambah stok untuk pembelian
            $product->update(['stock' => $product->stock + $validatedData['quantity']]);
        }
    }
    private function updateProductPrices($validatedData, $vendor)
    {
        $productPrice = ProductsPrice::where('product_id', $validatedData['product_id'])->first();
        $latestCost = $this->getLatestCost($validatedData['product_id'], $vendor->transaction_type);

        if ($latestCost !== null) {
            if ($vendor->transaction_type === 'inbound') {
                // Untuk transaksi inbound, perbarui buying_price
                if ($productPrice) {
                    $productPrice->update(['buying_price' => $latestCost]);
                } else {
                    // Jika belum ada, buat entri baru dengan buying_price yang valid
                    ProductsPrice::create([
                        'product_id' => $validatedData['product_id'],
                        'buying_price' => $latestCost,
                        'selling_price' => 0, // Inisialisasi atau perhitungan awal selling_price
                        'discount_price' => 0,
                    ]);
                }
            } else if ($vendor->transaction_type === 'outbound') {
                // Untuk transaksi outbound, perhitungkan dan perbarui selling_price
                $newSellingPrice = $this->calculateNewSellingPrice($latestCost);

                if ($productPrice) {
                    $productPrice->update(['selling_price' => $newSellingPrice]);
                } else {
                    // Jika belum ada, buat entri baru dengan selling_price yang valid
                    ProductsPrice::create([
                        'product_id' => $validatedData['product_id'],
                        'selling_price' => $newSellingPrice,
                        'buying_price' => $latestCost, // Mungkin Anda ingin set ini juga, tergantung logika bisnis
                        'discount_price' => 0,
                    ]);
                }
            }
        } else {
            Log::error('Latest cost not found for product ID: ' . $validatedData['product_id'] . ' and transaction type: ' . $vendor->transaction_type);
        }
    }
    private function getLatestCost($productId, $transactionType)
    {
        // Menggunakan Eloquent relationship untuk mendapatkan order terakhir berdasarkan tipe transaksi melalui vendor
        if ($transactionType === 'inbound') {
            $latestOrder = Order::whereHas('vendor', function ($query) {
                $query->where('transaction_type', 'inbound');
            })->where('product_id', $productId)->latest('created_at')->first();
        } else { // asumsi 'outbound'
            $latestOrder = Order::whereHas('vendor', function ($query) {
                $query->where('transaction_type', 'outbound');
            })->where('product_id', $productId)->latest('created_at')->first();
        }

        return $latestOrder ? $latestOrder->price_per_unit : null;
    }

    private function calculateNewSellingPrice($cost)
    {
        $markupPercentage = 20; // Misalkan markup 20%
        return $cost * (1 + $markupPercentage / 100);
    }


    // Menampilkan satu order
    public function show($id)
    {
        $order = Order::with('vendor', 'product', 'warehouse', 'invoices')->find($id);
        if ($order) {
            return response()->json([
                'status' => 200,
                'data' => $order,
                'message' => 'Order retrieved successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
            ]);
        }
    }
    public function getOrderDetails()
    {
        $orderDetails = DB::table('orders')
            ->select(
                'orders.id AS order_id',
                'invoices.id AS invoice_id',
                'payments.id AS payment_id',
                'orders.vendor_id',
                'orders.total_price AS order_total_price',
                'invoices.total_amount AS invoice_total_amount',
                'invoices.balance_due AS invoice_balance_due',
                'invoices.status AS invoice_status',
                'payments.amount_paid AS payment_amount_paid',
                'payments.payment_method',
                'payments.payment_date'
            )
            ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $orderDetails,
            'message' => 'Order details retrieved successfully.',
        ]);
    }
    public function getOrderDetail($orderID)
    {
        try {
            // Mengambil data order berdasarkan orderID dengan join ke invoice dan payments
            $orderDetails = Order::select(
                'orders.id as order_id',
                DB::raw('CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0")) as kodeOrder'),
                'orders.total_price as order_total_price',
                'invoices.id as invoice_id',
                DB::raw('CONCAT("INV/", DATE_FORMAT(invoices.created_at, "%Y"), "/", DATE_FORMAT(invoices.created_at, "%m"), "/", LPAD(invoices.id, 4, "0")) as kodeInvoice'),
                'invoices.total_amount as invoice_total_amount',
                'invoices.balance_due as invoice_balance_due',
                'payments.id as payment_id',
                DB::raw('CONCAT("PAY/", DATE_FORMAT(payments.created_at, "%Y"), "/", DATE_FORMAT(payments.created_at, "%m"), "/", LPAD(payments.id, 4, "0")) as kodePayments'),
                'payments.amount_paid as payment_amount_paid',
                'payments.payment_method as payment_method',
                'payments.payment_date as payment_date'
            )
                ->leftJoin('invoices', 'invoices.order_id', '=', 'orders.id')
                ->leftJoin('payments', 'payments.invoice_id', '=', 'invoices.id')
                ->where('orders.id', $orderID)
                ->get();



            // Pastikan order ditemukan
            if ($orderDetails->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Order not found.',
                ]);
            }

            // Mengembalikan data order beserta informasi terkait
            return response()->json([
                'status' => 200,
                'data' => $orderDetails,
                'message' => 'Order details retrieved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve order details. Error: ' . $e->getMessage(),
            ]);
        }
    }
    // Mengupdate order
    public function update(OrderRequest $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
            ]);
        }
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            // Hitung perubahan harga
            $priceChange = $validatedData['total_price'] ?? $order->total_price - $order->total_price;

            // Perbarui order
            $order->update($validatedData);

            // Dapatkan vendor terkait
            $vendor = Vendor::find($order->vendor_id);

            // Dapatkan akun yang sesuai berdasarkan jenis transaksi
            $accountName = $vendor->transaction_type === 'outbound' ? 'Piutang Usaha' : 'Utang Usaha';
            $account = Account::where('name', $accountName)->first();

            if ($account) {
                // Sesuaikan saldo akun
                $this->updateAccountBalance($account->id, abs($priceChange), $priceChange > 0 ? 'debit' : 'credit');
            }

            // Jika transaksi adalah pembelian (inbound), sesuaikan juga 'Persediaan'
            if ($vendor->transaction_type === 'inbound') {
                $inventoryAccount = Account::where('name', 'Persediaan')->first();
                if ($inventoryAccount) {
                    $this->updateAccountBalance($inventoryAccount->id, abs($priceChange), $priceChange > 0 ? 'debit' : 'credit');
                }
            }

            // Perbarui vendor_transaction yang sesuai
            $vendorTransaction = VendorTransaction::where('order_id', $order->id)->first();
            if ($vendorTransaction) {
                $vendorTransaction->update([
                    'amount' => $validatedData['total_price'],
                    'product_id' => $order->product_id,
                    'unit_price' => $validatedData['price_per_unit'],
                    'total_price' => $validatedData['total_price'],
                    'taxes' => $validatedData['taxes'],
                    'shipping_cost' => $validatedData['shipping_cost'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => $order,
                'message' => 'Order updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update order. Error: ' . $e->getMessage(),
            ]);
        }
    }



    // Menghapus order
    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Order deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
            ]);
        }
    }

    public function processingActivities($orderId)
    {
        $order = Order::with(['processingActivities', 'processingActivities.product'])->find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $order->processingActivities,
            'message' => 'Processing activities for order retrieved successfully.',
        ]);
    }
}
