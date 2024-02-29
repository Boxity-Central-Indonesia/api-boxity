<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DeliveryNoteItem;
use App\Events\formCreated;

class DeliveryNoteItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($deliveryNoteId)
    {
        $deliveryNoteItems = DeliveryNoteItem::where('delivery_note_id', $deliveryNoteId)
            ->with('deliveryNote', 'order', 'product')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $deliveryNoteItems,
            'message' => 'Delivery Note Items retrieved successfully.',
        ]);
    }

    public function show($deliveryNoteId, $id)
    {
        $deliveryNoteItem = DeliveryNoteItem::where('delivery_note_id', $deliveryNoteId)
            ->with('deliveryNote', 'order', 'product')
            ->find($id);

        if (!$deliveryNoteItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note Item not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $deliveryNoteItem,
            'message' => 'Delivery Note Item retrieved successfully.',
        ]);
    }

    public function store(Request $request, $deliveryNoteId)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
        ], [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The selected order ID is invalid.',
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product ID is invalid.',
        ]);

        $deliveryNoteItem = DeliveryNoteItem::create([
            'delivery_note_id' => $deliveryNoteId,
            'order_id' => $request->input('order_id'),
            'product_id' => $request->input('product_id'),
        ]);
broadcast(new formCreated('New Delivery Note Item created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $deliveryNoteItem,
            'message' => 'Delivery Note Item created successfully.',
        ]);
    }
    public function update(Request $request, $deliveryNoteId, $id)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
        ], [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The selected order ID is invalid.',
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product ID is invalid.',
        ]);

        $deliveryNoteItem = DeliveryNoteItem::where('delivery_note_id', $deliveryNoteId)->find($id);

        if (!$deliveryNoteItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note Item not found.',
            ]);
        }

        $deliveryNoteItem->update([
            'order_id' => $request->input('order_id'),
            'product_id' => $request->input('product_id'),
        ]);

        return response()->json([
            'status' => 201,
            'data' => $deliveryNoteItem,
            'message' => 'Delivery Note Item updated successfully.',
        ]);
    }

    public function destroy($deliveryNoteId, $id)
    {
        $deliveryNoteItem = DeliveryNoteItem::where('delivery_note_id', $deliveryNoteId)->find($id);

        if (!$deliveryNoteItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note Item not found.',
            ]);
        }

        $deliveryNoteItem->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Delivery Note Item deleted successfully.',
        ]);
    }
}
