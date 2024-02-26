<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Models\Account;
use App\Models\AccountsTransaction;
use App\Models\JournalEntry;
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
        $orders = Order::with(['vendor', 'products', 'warehouse', 'invoices'])->get();
        return response()->json([
            'status' => 200,
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'kode_order' => $order->kode_order,
                    'vendor' => $order->vendor,
                    'products' => $order->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price_per_unit' => (int)$product->pivot->price_per_unit,
                            'total_price' => (int)$product->pivot->total_price,
                        ];
                    }),
                    'warehouse' => $order->warehouse,
                    'invoices' => $order->invoices,
                    'total_price' => $order->total_price,
                    'order_status' => $order->order_status,
                    'order_type' => $order->order_type,
                    'taxes' => $order->taxes,
                    'shipping_cost' => $order->shipping_cost,
                    'created_at' => $order->created_at, // Tambahkan created_at jika diperlukan
                ];
            }),
            'message' => 'Orders retrieved successfully.',
        ]);
    }


    // Membuat order baru
    public function store(Request $request)
    {
        Log::info("Incoming order data:", $request->all());
        DB::beginTransaction();

        try {
            // Asumsikan data yang masuk sudah valid atau lakukan validasi manual sederhana
            $validatedData = $request->all(); // Menggunakan data langsung tanpa validasi dari OrderRequest
            $order = new Order([
                'vendor_id' => $validatedData['vendor_id'],
                'warehouse_id' => $validatedData['warehouse_id'],
                'status' => $validatedData['status'],
                'order_status' => $validatedData['order_type'] == 'Direct Order' ? 'Completed' : 'In Production',
                'details' => $validatedData['details'] ?? null,
                'order_type' => $validatedData['order_type'],
                'taxes' => $validatedData['taxes'] ?? null,
                'shipping_cost' => $validatedData['shipping_cost'] ?? null,
            ]);
            $order->total_price = 0;
            $order->save();
            $vendor = Vendor::find($validatedData['vendor_id']);
            $totalOrderPrice = 0;

            foreach ($validatedData['products'] as $product) {
                $productTotalPrice = $product['quantity'] * $product['price_per_unit'];
                $totalOrderPrice += $productTotalPrice;

                $order->products()->attach($product['product_id'], [
                    'quantity' => $product['quantity'],
                    'price_per_unit' => $product['price_per_unit'],
                    'total_price' => $productTotalPrice,
                ]);

                VendorTransaction::create([
                    'vendors_id' => $validatedData['vendor_id'],
                    'amount' => $productTotalPrice,
                    'product_id' => $product['product_id'],
                    'unit_price' => $product['price_per_unit'],
                    'total_price' => $productTotalPrice,
                    'quantity' => $product['quantity'],
                    'taxes' => null,
                    'shipping_cost' => null,
                    'order_id' => $order->id,
                ]);
            }

            $order->total_price = $totalOrderPrice + ($validatedData['taxes'] ?? 0) + ($validatedData['shipping_cost'] ?? 0);
            $order->save();

            // Proses logika akuntansi dan pencatatan lainnya
            $this->handleAccounting($validatedData, $order, $vendor);
            $this->recordProductMovement($validatedData, $vendor);
            $this->updateProductPrices($validatedData, $vendor);

            DB::commit();
            return response()->json(['status' => 200, 'data' => $order, 'message' => 'Order created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => 'Gagal membuat order. Kesalahan: ' . $e->getMessage(), 'data' => [$validatedData, $order, $vendor]], 500);
        }
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

    private function handleAccounting($validatedData, $order, $vendor)
    {
        $accountReceivable = Account::where('name', 'Piutang Usaha')->first();
        $accountPayable = Account::where('name', 'Utang Usaha')->first();
        $inventoryAccount = Account::where('name', 'Persediaan')->first();

        $journalEntry = [
            'date' => now(),
            'description' => 'Pembayaran Order ' . $order['kode_order'],
        ];

        if ($vendor->transaction_type === 'outbound') {
            // Penjualan (Outbound)
            $journalEntry['account_id'] = $accountReceivable->id;
            $journalEntry['debit'] = $order['total_price'];
            $journalEntry['account_id'] = $inventoryAccount->id;
            $journalEntry['credit'] = $order['total_price'];
        } else {
            // Pembelian (Inbound)
            $journalEntry['account_id'] = $inventoryAccount->id;
            $journalEntry['debit'] = $order['total_price'];
            $journalEntry['account_id'] = $accountPayable->id;
            $journalEntry['credit'] = $order['total_price'];
        }

        // Simpan jurnal
        JournalEntry::create($journalEntry);
    }

    private function recordProductMovement($validatedData, $vendor)
    {
        foreach ($validatedData['products'] as $productData) {
            $productMovement = ProductsMovement::create([
                'product_id' => $productData['product_id'],
                'warehouse_id' => $validatedData['warehouse_id'],
                'movement_type' => $vendor->transaction_type === 'outbound' ? 'sale' : 'purchase',
                'quantity' => $productData['quantity'],
                'price' => $productData['price_per_unit'],
            ]);

            $productModel = Product::findOrFail($productData['product_id']);

            // Perbarui stok produk berdasarkan tipe transaksi
            if ($vendor->transaction_type === 'sale') {
                // Pastikan tidak mengurangi stok menjadi negatif
                $newQuantity = max($productModel->stock - $productMovement['quantity'], 0);
                $productModel->update(['stock' => $newQuantity]);
            } else {
                // Menambah stok untuk pembelian
                $productModel->update(['stock' => $productModel->stock + $productMovement['quantity']]);
            }
        }
    }
    private function updateProductPrices($validatedData, $vendor)
    {
        foreach ($validatedData['products'] as $productData) {
            $productPrice = ProductsPrice::where('product_id', $productData['product_id'])->first();
            $latestCost = $this->getLatestCost($productData['product_id'], $vendor->transaction_type);

            if ($latestCost !== null) {
                if ($vendor->transaction_type === 'inbound') {
                    if ($productPrice) {
                        $productPrice->update(['buying_price' => $latestCost]);
                    } else {
                        ProductsPrice::create([
                            'product_id' => $productData['product_id'],
                            'buying_price' => $latestCost,
                            'selling_price' => 0,
                            'discount_price' => 0,
                        ]);
                    }
                } else if ($vendor->transaction_type === 'outbound') {
                    $newSellingPrice = $this->calculateNewSellingPrice($latestCost);

                    if ($productPrice) {
                        $productPrice->update(['selling_price' => $newSellingPrice]);
                    } else {
                        ProductsPrice::create([
                            'product_id' => $productData['product_id'],
                            'selling_price' => $newSellingPrice,
                            'buying_price' => $latestCost,
                            'discount_price' => 0,
                        ]);
                    }
                }
            } else {
                Log::error('Latest cost not found for product ID: ' . $productData['product_id'] . ' and transaction type: ' . $vendor->transaction_type);
            }
        }
    }

    private function getLatestCost($productId, $transactionType)
    {
        if ($transactionType === 'inbound') {
            $latestOrder = Order::whereHas('vendor', function ($query) {
                $query->where('transaction_type', 'inbound');
            })->whereHas('products', function ($query) use ($productId) { // Menambahkan use ($productId) di sini
                $query->where('products.id', $productId);
            })->latest('created_at')->first();
        } else { // asumsi 'outbound'
            $latestOrder = Order::whereHas('vendor', function ($query) {
                $query->where('transaction_type', 'outbound');
            })->whereHas('products', function ($query) use ($productId) { // Menambahkan use ($productId) di sini
                $query->where('products.id', $productId);
            })->latest('created_at')->first();
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
        $order = Order::with(['vendor', 'products', 'warehouse', 'invoices'])->find($id);

        if ($order) {
            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $order->id,
                    'kode_order' => $order->kode_order,
                    'vendor' => $order->vendor,
                    'products' => $order->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price_per_unit' => $product->pivot->price_per_unit,
                            'total_price' => $product->pivot->total_price,
                        ];
                    }),
                    'warehouse' => $order->warehouse,
                    'invoices' => $order->invoices,
                    'total_price' => $order->total_price,
                    'order_status' => $order->order_status,
                    'order_type' => $order->order_type,
                    'taxes' => $order->taxes,
                    'shipping_cost' => $order->shipping_cost,
                ],
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
            // Perbarui order dengan data yang tidak termasuk produk
            $orderDataToUpdate = collect($validatedData)->except(['products'])->toArray();
            $order->update($orderDataToUpdate);

            $vendor = Vendor::find($order->vendor_id);
            $totalOrderPrice = 0;

            // Hapus relasi produk-order sebelumnya
            $order->products()->detach();
            // Tambahkan produk-produk baru dari request validasi
            foreach ($validatedData['products'] as $product) {
                $productTotalPrice = $product['quantity'] * $product['price_per_unit'];
                $totalOrderPrice += $productTotalPrice;

                // Simpan ke tabel order_products
                $order->products()->attach($product['product_id'], [
                    'quantity' => $product['quantity'],
                    'price_per_unit' => $product['price_per_unit'],
                    'total_price' => $productTotalPrice,
                ]);
            }

            // Update total_price di order
            $order->total_price = $totalOrderPrice;
            $order->save();

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
            $vendorTransaction = VendorTransaction::updateOrCreate(
                ['order_id' => $order->id], // Find by order_id or create new
                [
                    'vendors_id' => $vendor->id,
                    'amount' => $totalOrderPrice,
                    'total_price' => $totalOrderPrice,
                    'taxes' => $validatedData['taxes'] ?? null,
                    'shipping_cost' => $validatedData['shipping_cost'] ?? null,
                ]
            );

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
