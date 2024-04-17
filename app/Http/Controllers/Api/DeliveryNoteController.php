<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Events\formCreated;

class DeliveryNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryNotes = DeliveryNote::with('warehouse', 'vendor', 'deliveryNoteItems.order', 'deliveryNoteItems.product')->get();

        return response()->json([
            'status' => 200,
            'data' => $deliveryNotes,
            'message' => 'Delivery Notes retrieved successfully.',
        ]);
    }

    public function show($id)
    {
        $deliveryNote = DeliveryNote::with('warehouse', 'vendor', 'deliveryNoteItems.order', 'deliveryNoteItems.product')->find($id);

        if (!$deliveryNote) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $deliveryNote,
            'message' => 'Delivery Note retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'details' => 'nullable|string',
            'deliveryNoteItems' => 'required|array',
            'deliveryNoteItems.*.order_id' => 'required|exists:orders,id',
            'deliveryNoteItems.*.product_id' => 'required|exists:products,id',
            'deliveryNoteItems.*.quantity' => 'required|integer',
        ], [
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',
            'warehouse_id.required' => 'The warehouse ID is required.',
            'warehouse_id.exists' => 'The selected warehouse ID is invalid.',
            'vendor_id.required' => 'The vendor ID is required.',
            'vendor_id.exists' => 'The selected vendor ID is invalid.',
            'details.string' => 'The details must be a string.',
            'deliveryNoteItems.required' => 'At least one delivery note item is required.',
            'deliveryNoteItems.array' => 'The delivery note items must be an array.',
            'deliveryNoteItems.*.order_id.required' => 'The order ID in delivery note item is required.',
            'deliveryNoteItems.*.order_id.exists' => 'The selected order ID in delivery note item is invalid.',
            'deliveryNoteItems.*.product_id.required' => 'The product ID in delivery note item is required.',
            'deliveryNoteItems.*.product_id.exists' => 'The selected product ID in delivery note item is invalid.',
            'deliveryNoteItems.*.quantity.required' => 'Quantity delivery item must be required',
        ]);

        $deliveryNote = DeliveryNote::create($request->all());

        foreach ($request->deliveryNoteItems as $item) {
            // Simpan informasi pengiriman item beserta jumlahnya
                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'order_id' => $item['order_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],

                ]);
        }
        broadcast(new formCreated('New Delivery Note created successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $deliveryNote,
            'message' => 'Delivery Note created successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'number' => 'required|string',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'details' => 'nullable|string',
            'deliveryNoteItems' => 'required|array',
            'deliveryNoteItems.*.order_id' => 'required|exists:orders,id',
            'deliveryNoteItems.*.product_id' => 'required|exists:products,id',
            'deliveryNoteItems.*.quantity' => 'required|integer',
        ], [
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',
            'warehouse_id.required' => 'The warehouse ID is required.',
            'warehouse_id.exists' => 'The selected warehouse ID is invalid.',
            'vendor_id.required' => 'The vendor ID is required.',
            'vendor_id.exists' => 'The selected vendor ID is invalid.',
            'details.string' => 'The details must be a string.',
            'deliveryNoteItems.required' => 'At least one delivery note item is required.',
            'deliveryNoteItems.array' => 'The delivery note items must be an array.',
            'deliveryNoteItems.*.order_id.required' => 'The order ID in delivery note item is required.',
            'deliveryNoteItems.*.order_id.exists' => 'The selected order ID in delivery note item is invalid.',
            'deliveryNoteItems.*.product_id.required' => 'The product ID in delivery note item is required.',
            'deliveryNoteItems.*.product_id.exists' => 'The selected product ID in delivery note item is invalid.',
            'deliveryNoteItems.*.quantity.required' => 'Quantity delivery item must be required',
        ]);

        $deliveryNote = DeliveryNote::find($id);

        if (!$deliveryNote) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note not found.',
            ]);
        }

        $deliveryNote->update($request->all());

        $deliveryNote->deliveryNoteItems()->delete();

        foreach ($request->deliveryNoteItems as $item) {
            // Simpan informasi pengiriman item beserta jumlahnya
                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'order_id' => $item['order_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
        }

        broadcast(new formCreated('Delivery Note updated successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $deliveryNote,
            'message' => 'Delivery Note updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $deliveryNote = DeliveryNote::find($id);

        if (!$deliveryNote) {
            return response()->json([
                'status' => 404,
                'message' => 'Delivery Note not found.',
            ]);
        }

        $deliveryNote->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Delivery Note deleted successfully.',
        ]);
    }
}