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
use App\Events\formCreated;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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
                    'no_ref' => $order->no_ref,
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
                    'total_price' => (int)$order->total_price,
                    'order_status' => $order->order_status,
                    'order_type' => $order->order_type,
                    'taxes' => (int)$order->taxes,
                    'shipping_cost' => (int)$order->shipping_cost,
                    'created_at' => $order->created_at, // Tambahkan created_at jika diperlukan
                ];
            }),
            'message' => 'Orders retrieved successfully.',
        ]);
    }
    public function notCompleted()
    {
        $orders = Order::where('order_status','!=','Completed')->with(['vendor', 'products', 'warehouse', 'invoices'])->get();
        return response()->json([
            'status' => 200,
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'no_ref' => $order->no_ref,
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
                    'total_price' => (int)$order->total_price,
                    'order_status' => $order->order_status,
                    'order_type' => $order->order_type,
                    'taxes' => (int)$order->taxes,
                    'shipping_cost' => (int)$order->shipping_cost,
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

        $order = null; // Deklarasi variabel $order di luar blok try

        try {
            // Asumsikan data yang masuk sudah valid atau lakukan validasi manual sederhana
            $validatedData = $request->all(); // Menggunakan data langsung tanpa validasi dari OrderRequest
            $order = new Order([
                'no_ref' => $validatedData['no_ref'],
                'vendor_id' => $validatedData['vendor_id'],
                'warehouse_id' => $validatedData['warehouse_id'] ?? null,
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
                    'unit_of_measure' => $product['unit_of_measure'] ?? "Kg",
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
            // Buat invoice
            $invoice = $order->createInvoice();
            // Proses logika akuntansi dan pencatatan lainnya
            $this->handleAccounting($validatedData, $order, $vendor);
            $this->recordProductMovement($validatedData, $vendor);
            $this->updateProductPrices($validatedData, $vendor);

            DB::commit();
            return response()->json(['status' => 201, 'data' => $order, 'message' => 'Order created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Menggunakan operator null coalescing untuk memastikan $order tidak null sebelum digunakan
            return response()->json(['status' => 500, 'message' => 'Gagal membuat order. Kesalahan: ' . $e->getMessage(), 'data' => $order ?? null], 500);
        }
    }


    public function addProductToOrder(Request $request, $orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ]);
    }

    try {
        // Asumsikan data produk yang masuk sudah valid atau lakukan validasi manual
        $productData = $request->input('product');

        // Tambahkan produk ke order
        $productTotalPrice = $productData['quantity'] * $productData['price_per_unit'];
        $order->products()->attach($productData['product_id'], [
            'quantity' => $productData['quantity'],
            'price_per_unit' => $productData['price_per_unit'],
            'total_price' => $productTotalPrice,
        ]);

        // Update total_price di order
        $order->total_price += $productTotalPrice;
        $order->save();
        // Proses logika akuntansi dan pencatatan lainnya
        $vendor = Vendor::find($order->vendor_id);
        $this->recordProductMovement($request->all(), $vendor);
        $this->updateProductPrices($request->all(), $vendor);
        return response()->json([
            'status' => 201,
            'data' => $order,
            'message' => 'Product added to order successfully.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Failed to add product to order. Error: ' . $e->getMessage(),
        ]);
    }
}
public function editProductInOrder(Request $request, $orderId, $productId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ]);
    }

    try {
        // Asumsikan data produk yang masuk sudah valid atau lakukan validasi manual
        $productData = $request->input('product');

        // Perbarui data produk dalam pesanan
        $existingProduct = $order->products()->where('product_id', $productId)->first();

        if ($existingProduct) {
            // Update quantity dan price_per_unit
            $existingProduct->pivot->update([
                'quantity' => $productData['quantity'],
                'price_per_unit' => $productData['price_per_unit'],
                'total_price' => $productData['quantity'] * $productData['price_per_unit'],
            ]);

            // Perbarui total_price di pesanan
            $order->total_price = $order->products()->sum(DB::raw('quantity * price_per_unit'));
            $order->save();

                    // Proses logika akuntansi dan pencatatan lainnya
        $vendor = Vendor::find($order->vendor_id);
        $this->recordProductMovement($request->all(), $vendor);
        $this->updateProductPrices($request->all(), $vendor);

            return response()->json([
                'status' => 201,
                'data' => $order,
                'message' => 'Product in order updated successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found in order.',
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Failed to update product in order. Error: ' . $e->getMessage(),
        ]);
    }
}
public function getProductByOrderIdAndProductId($orderId, $productId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ]);
    }

    $product = $order->products()->where('products.id', $productId)->first();

    if (!$product) {
        return response()->json([
            'status' => 404,
            'message' => 'Product not found in order.',
        ]);
    }

    return response()->json([
        'status' => 200,
        'data' => [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $product->pivot->quantity,
            'price_per_unit' => (int)$product->pivot->price_per_unit,
            'total_price' => (int)$product->pivot->total_price,
        ],
        'message' => 'Product retrieved successfully.',
    ]);
}
public function removeProductFromOrder(Request $request, $orderId, $productId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
            ]);
        }

        try {
            // Detach the product from the order
            $order->products()->detach($productId);

            // Recalculate total_price and save the order
            $order->total_price = $order->calculateTotalPrice();
            $order->save();

            return response()->json([
                'status' => 200,
                'data' => $order,
                'message' => 'Product removed from order successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to remove product from order. Error: ' . $e->getMessage(),
            ]);
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

    $journalEntries = [];

    if ($vendor->transaction_type === 'outbound') {
        // Penjualan (Outbound)
        $journalEntries[] = [
            'date' => now(),
            'description' => 'Penjualan Order ' . $order->kode_order,
            'account_id' => $accountReceivable->id,
            'debit' => $order->total_price,
        ];

        $journalEntries[] = [
            'date' => now(),
            'description' => 'Pengurangan Persediaan',
            'account_id' => $inventoryAccount->id,
            'credit' => $order->total_price,
        ];
    } else {
        // Pembelian (Inbound)
        $journalEntries[] = [
            'date' => now(),
            'description' => 'Pembelian Order ' . $order->kode_order,
            'account_id' => $inventoryAccount->id,
            'debit' => $order->total_price,
        ];

        $journalEntries[] = [
            'date' => now(),
            'description' => 'Utang Usaha',
            'account_id' => $accountPayable->id,
            'credit' => $order->total_price,
        ];
    }

    // Simpan jurnal entries
    foreach ($journalEntries as $entry) {
        JournalEntry::create($entry);
    }
}

private function recordProductMovement($validatedData, $vendor)
{
    // Periksa apakah kunci 'products' ada dalam data yang diterima
    if (!isset($validatedData['products'])) {
        // Kembalikan respons dengan pesan kesalahan yang sesuai
        Log::error('Products data not found in validated data.');
        return response()->json([
            'status' => 400,
            'message' => 'Products data not found in validated data.',
        ]);
    }

    try {
        foreach ($validatedData['products'] as $productData) {
            $productMovement = ProductsMovement::create([
                'product_id' => $productData['product_id'],
                'warehouse_id' => $validatedData['warehouse_id'] ?? null,
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
        return response()->json([
            'status' => 200,
            'message' => 'Product movement recorded successfully.',
        ]);
    } catch (\Exception $e) {
        Log::error('Error recording product movement: ' . $e->getMessage());
        return response()->json([
            'status' => 500,
            'message' => 'Failed to record product movement. Error: ' . $e->getMessage(),
        ]);
    }
}

private function updateProductPrices($validatedData, $vendor)
{
    // Periksa apakah kunci 'products' ada dalam data yang diterima
    if (!isset($validatedData['products'])) {
        // Kembalikan respons dengan pesan kesalahan yang sesuai
        Log::error('Products data not found in validated data.');
        return response()->json([
            'status' => 400,
            'message' => 'Products data not found in validated data.',
        ]);
    }

    try {
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
                    $newSellingPrice = $this->calculateNewSellingPrice($latestCost, $productData['product_id'], $vendor->id);

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
        return response()->json([
            'status' => 200,
            'message' => 'Product prices updated successfully.',
        ]);
    } catch (\Exception $e) {
        Log::error('Error updating product prices: ' . $e->getMessage());
        return response()->json([
            'status' => 500,
            'message' => 'Failed to update product prices. Error: ' . $e->getMessage(),
        ]);
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

    private function calculateNewSellingPrice($latestCost, $productId, $vendorId)
    {
        // Get past selling prices from related orders with the same product and vendor's transaction_type as 'outbound'
        $pastSellingPrices = Order::whereHas('vendor', function ($query) {
            $query->where('transaction_type', 'outbound');
        })
            ->whereHas('products', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('vendor_id', $vendorId)
            ->pluck('total_price')
            ->toArray();

        // If there are past selling prices, calculate the average
        if (!empty($pastSellingPrices)) {
            $averageSellingPrice = array_sum($pastSellingPrices) / count($pastSellingPrices);
        } else {
            // If no past selling prices, use a default calculation or set to the latest cost
            $averageSellingPrice = $latestCost;
        }

        return $averageSellingPrice;
    }


    // Menampilkan satu order
    public function show($id)
    {
        $order = Order::with(['vendor', 'products', 'warehouse', 'invoices', 'processingActivities'])->find($id);

        if ($order) {
            // Menghitung selisih_quantity
            $timbangKotor = 0;
            foreach ($order->processingActivities as $activity) {
                if (isset($activity->details['qty_weighing'])) {
                    $timbangKotor += $activity->details['qty_weighing'];
                }
            }

            // Menghitung total berat keranjang
            $totalBeratKeranjang = 0;
            foreach ($order->processingActivities as $activity) {
                if (isset($activity->details['basket_weight'])) {
                    $totalBeratKeranjang += $activity->details['basket_weight'];
                }
            }

            // Menghitung total jumlah item
            $totalCountItem = 0;
            foreach ($order->processingActivities as $activity) {
                if (isset($activity->details['number_of_item'])) {
                    $totalCountItem += $activity->details['number_of_item'];
                }
            }

            $averageWeight = 0;
            if ($totalCountItem > 0) {
                $averageWeight = ($timbangKotor - $totalBeratKeranjang) / $totalCountItem;
            }

            // Menghitung total qty pada products dan susut persentase per produk
            $productsData = [];
            foreach ($order->products as $product) {
                $totalProductQty = $product->pivot->quantity;
                $selisihQuantity = $totalProductQty - ($timbangKotor - $totalBeratKeranjang);
                $susutPercentage = $totalProductQty != 0 ? ($selisihQuantity / $totalProductQty) * 100 : 0;

                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity_pesanan' => $totalProductQty,
                    'timbang_kotor' => $timbangKotor,
                    'total_berat_keranjang' => $totalBeratKeranjang,
                    'total_keranjang' => $order->processingActivities->count(),
                    'selisih_quantity' => $selisihQuantity,
                    'timbang_bersih' => $timbangKotor - $totalBeratKeranjang,
                    'total_jumlah_item' => $totalCountItem,
                    'rata_rata_berat_hewan' => $averageWeight,
                    'susut_percentage' => $susutPercentage,
                    'price_per_unit' => (int) $product->pivot->price_per_unit,
                    'total_price' => (int) $product->pivot->total_price,
                ];
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $order->id,
                    'no_ref' => $order->no_ref,
                    'kode_order' => $order->kode_order,
                    'products' => $productsData,
                    'vendor' => $order->vendor,
                    'warehouse' => $order->warehouse,
                    'invoices' => $order->invoices,
                    'processing_activities' => $order->processingActivities,
                    'total_price' => (int) $order->total_price,
                    'order_status' => $order->order_status,
                    'order_type' => $order->order_type,
                    'taxes' => (int) $order->taxes,
                    'shipping_cost' => (int) $order->shipping_cost,
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






    public function downloadOrderDetail($orderId)
{
    // Get order details
    $order = Order::with(['vendor', 'products', 'warehouse', 'invoices'])
        ->find($orderId);

    // Validasi jika order tidak ditemukan
    if (!$order) {
        return response()->json(['message' => 'Order not found.', 'status' => 404], 404);
    }

    // Format order data
    $formattedOrder = [
        'id' => $order->id,
        'no_ref'=> $order->no_ref,
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
        'details' => $order->details,
        'created_at' => $order->created_at,
        'warehouse' => $order->warehouse,
        'invoices' => $order->invoices,
        'total_price' => (int)$order->total_price,
        'order_status' => $order->order_status,
        'order_type' => $order->order_type,
        'taxes' => (int)$order->taxes,
        'shipping_cost' => (int)$order->shipping_cost,
    ];

    // Generate nama file dengan menambahkan tanggal
    $fileName = 'order_detail_'.$formattedOrder['id']."_". Carbon::now()->format('Ymd_His') . '.pdf';

    // Mendapatkan URL untuk di-download
    $pdfUrl = url('pdf/' . $fileName);
    $now = Carbon::now();
    $filenameQR = 'qrcode_' . $now->format('Ymd_His') . '.png';
    $qrCodePath = public_path('qrcodes/' . $filenameQR);

    // Generate QR Code
    $qrCode = QrCode::size(100)->generate($pdfUrl);

    // Save QR Code as an image
Storage::disk('public')->put('qrcodes/' . $filenameQR, $qrCode);
    $pdf = PDF::loadView('pdf.order_detail', compact('formattedOrder', 'pdfUrl', 'qrCodePath'));

    // Simpan file PDF di storage dengan nama yang baru
    $pdf->save(public_path('pdf/' . $fileName));

    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
    ]);
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
                'orders.no_ref as no_ref',
                DB::raw('CASE
                            WHEN vendors.transaction_type = "inbound" THEN CONCAT("SO/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                            WHEN vendors.transaction_type = "outbound" THEN CONCAT("PO/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                            ELSE CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                        END as kodeOrder'),
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
                ->leftJoin('vendors', 'orders.vendor_id', '=', 'vendors.id')
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
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve order details. Error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve order details. Error: ' . $e->getMessage(),
            ]);
        }
    }


    // Mengupdate order
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ]);
    }

    DB::beginTransaction();

    try {
        // Perbarui order dengan data yang tidak termasuk produk
        $orderDataToUpdate = request()->except(['products']);
        $order->update($orderDataToUpdate);

        // Perbarui total_price di order (jika perlu)
        $order->total_price = request('total_price') ?? $order->total_price;
        $order->save();

        // Hitung perubahan harga
        $priceChange = request('total_price') ?? $order->total_price - $order->total_price;

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
            ['order_id' => $order->id],
            [
                'vendors_id' => $vendor->id,
                'amount' => $order->total_price,
                'total_price' => $order->total_price,
                'taxes' => request('taxes') ?? null,
                'shipping_cost' => request('shipping_cost') ?? null,
            ]
        );

        DB::commit();
        return response()->json([
            'status' => 201,
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
    public function getProductByOrderId($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ]);
    }

    $products = $order->products()->get();

    if ($products->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'No products found in the order.',
        ]);
    }

    return response()->json([
        'status' => 200,
        'data' => $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->pivot->quantity,
                'price_per_unit' => (int)$product->pivot->price_per_unit,
                'total_price' => (int)$product->pivot->total_price,
            ];
        }),
        'message' => 'Products retrieved successfully.',
    ]);
}

}