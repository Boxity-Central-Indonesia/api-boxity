<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\GoodsReceiptItem;

class GoodsReceiptItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($goodsReceiptId)
{
    $goodsReceiptItems = GoodsReceiptItem::where('goods_receipt_id', $goodsReceiptId)
        ->with('goodsReceipt', 'product')
        ->get()
        ->groupBy('goods_receipt_id'); // Group by goods_receipt_id

    $groupedItems = $goodsReceiptItems->map(function ($items) {
        return $items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity_ordered' => $item->quantity_ordered,
                'quantity_received' => $item->quantity_received,
                'quantity_due' => $item->quantity_due,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'goods_receipt' => $item->goodsReceipt,
                'product' => $item->product,
            ];
        });
    });

    return response()->json([
        'status' => 200,
        'data' => $groupedItems->values(), // Convert to array and reset keys
        'message' => 'Goods Receipt Items retrieved successfully.',
    ]);
}


    public function show($goodsReceiptId, $id)
    {
        $goodsReceiptItem = GoodsReceiptItem::where('goods_receipt_id', $goodsReceiptId)
            ->with('goodsReceipt', 'product')
            ->where('product_id', $id)->first();

        if (!$goodsReceiptItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt Item not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $goodsReceiptItem,
            'message' => 'Goods Receipt Item retrieved successfully.',
        ]);
    }

    public function store(Request $request, $goodsReceiptId)
    {
        $request->validate([
            'product_id' => 'required',
        'quantity_ordered' => 'required',
            // Add other validation rules as needed
        ]);

        $goodsReceiptItem = GoodsReceiptItem::create([
            'goods_receipt_id' => $goodsReceiptId,
            'product_id' => $request->input('product_id'),
            'quantity_ordered' => $request->input('quantity_ordered'),
            'quantity_received' => $request->input('quantity_received', 0),
            'quantity_due' => $request->input('quantity_ordered') - $request->input('quantity_received', 0),
        ]);

        return response()->json([
            'status' => 201,
            'data' => $goodsReceiptItem,
            'message' => 'Goods Receipt Item created successfully.',
        ]);
    }

    public function update(Request $request, $goodsReceiptId, $id)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity_ordered' => 'required',
            // Add other validation rules as needed
        ]);

        $goodsReceiptItem = GoodsReceiptItem::where('goods_receipt_id', $goodsReceiptId)->find($id);

        if (!$goodsReceiptItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt Item not found.',
            ]);
        }

        $goodsReceiptItem->update([
            'product_id' => $request->input('product_id'),
            'quantity_ordered' => $request->input('quantity_ordered'),
            'quantity_received' => $request->input('quantity_received', 0),
            'quantity_due' => $request->input('quantity_ordered') - $request->input('quantity_received', 0),
        ]);

        return response()->json([
            'status' => 200,
            'data' => $goodsReceiptItem,
            'message' => 'Goods Receipt Item updated successfully.',
        ]);
    }

    public function destroy($goodsReceiptId, $id)
    {
        $goodsReceiptItem = GoodsReceiptItem::where('goods_receipt_id', $goodsReceiptId)->find($id);

        if (!$goodsReceiptItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Goods Receipt Item not found.',
            ]);
        }

        $goodsReceiptItem->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Goods Receipt Item deleted successfully.',
        ]);
    }
}
