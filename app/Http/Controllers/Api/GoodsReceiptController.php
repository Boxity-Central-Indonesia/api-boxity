<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\ProductsMovement;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;
use App\Events\formCreated;

class GoodsReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goodsReceipts = GoodsReceipt::with('order', 'warehouse', 'goodsReceiptItems.product')->get();

        return response()->json([
            'status' => 200,
            'data' => $goodsReceipts,
            'message' => 'Goods Receipts retrieved successfully.',
        ]);
    }

    public function show($id)
    {
        $goodsReceipt = GoodsReceipt::with('order', 'warehouse', 'goodsReceiptItems.product')->find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $goodsReceipt,
            'message' => 'Goods Receipt retrieved successfully.',
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'details' => 'nullable|string',
    ], [
        'order_id.required' => 'The order ID is required.',
        'order_id.exists' => 'The selected order ID is invalid.',
        'warehouse_id.required' => 'The warehouse ID is required.',
        'warehouse_id.exists' => 'The selected warehouse ID is invalid.',
        'details.string' => 'The details must be a string.',
    ]);

    DB::beginTransaction();

    try {
        $goodsReceipt = GoodsReceipt::create($request->all());

        // Get products from the related OrderProduct
        $orderProducts = OrderProduct::where('order_id', $request->input('order_id'))->get();

        foreach ($orderProducts as $orderProduct) {
            // Create GoodsReceiptItem
            $goodsReceiptItem = GoodsReceiptItem::create([
                'goods_receipt_id' => $goodsReceipt->id,
                'product_id' => $orderProduct->product_id,
                'quantity_ordered' => $orderProduct->quantity,
                'price_per_unit' => $orderProduct->price_per_unit,
                'total_price' => $orderProduct->total_price,
            ]);

            $this->updateProductsMovement($goodsReceiptItem);
        }

        DB::commit();

        return response()->json([
            'status' => 201,
            'data' => $goodsReceipt,
            'message' => 'Goods Receipt created successfully.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'message' => 'Failed to create Goods Receipt. Error: ' . $e->getMessage(),
        ]);
    }
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'details' => 'nullable|string',
        ], [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The selected order ID is invalid.',
            'warehouse_id.required' => 'The warehouse ID is required.',
            'warehouse_id.exists' => 'The selected warehouse ID is invalid.',
            'details.string' => 'The details must be a string.',
        ]);

        $goodsReceipt = GoodsReceipt::find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt not found.',
            ]);
        }

        $goodsReceipt->update($request->all());
        $this->updateProductsMovementForGoodsReceipt($goodsReceipt);

        return response()->json([
            'status' => 200,
            'data' => $goodsReceipt,
            'message' => 'Goods Receipt updated successfully.',
        ]);
    }

    private function updateProductsMovement(GoodsReceiptItem $goodsReceiptItem)
{
    $goodsReceipt = $goodsReceiptItem->goodsReceipt;
    $vendor = $goodsReceipt->order->vendor;
    $order = $goodsReceipt->order;

    $movementType = ($vendor->transaction_type === 'outbound') ? 'sale' : 'purchase';

    ProductsMovement::create([
        'product_id' => $goodsReceiptItem->product_id,
        'warehouse_id' => $goodsReceipt->warehouse_id,
        'movement_type' => $movementType,
        'quantity' => $goodsReceiptItem->quantity_ordered,
        'price' => $order->total_price,
    ]);
}
private function updateProductsMovementForGoodsReceipt(GoodsReceipt $goodsReceipt)
{
    $vendor = $goodsReceipt->order->vendor;
    $order = $goodsReceipt->order;

    $movementType = ($vendor->transaction_type === 'outbound') ? 'sale' : 'purchase';

    ProductsMovement::create([
        'product_id' => $goodsReceipt->id, // Change this to the appropriate product_id for the entire receipt
        'warehouse_id' => $goodsReceipt->warehouse_id,
        'movement_type' => $movementType,
        'quantity' => $goodsReceipt->goodsReceiptItems->sum('quantity_ordered'),
        'price' => $order->total_price,
    ]);
}


    public function destroy($id)
    {
        $goodsReceipt = GoodsReceipt::find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt not found.',
            ]);
        }

        $goodsReceipt->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Goods Receipt deleted successfully.',
        ]);
    }
}
