<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackingRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackingController extends Controller
{
    /**
     * Fetch all packing records with customer details in descending order.
     */
    public function index()
    {
        try {
            // Added eager loading for customer relationship
            $records = PackingRecord::with('customer')
                                    ->orderBy('packing_date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
            return response()->json($records, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching packing records: " . $e->getMessage());
            return response()->json(['message' => 'Failed to load packing history'], 500);
        }
    }

    /**
     * Store a new packing record including customer association.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'batch_reference' => 'required|string|max:100',
            'pack_size' => 'required|string',
            'quantity_packed' => 'required|integer|min:1',
            'packing_date' => 'required|date',
        ]);

        try {
            $record = PackingRecord::create($validated);

            return response()->json([
                'message' => 'Packing record created successfully',
                'data' => $record->load('customer')
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error saving packing record: " . $e->getMessage());
            return response()->json(['message' => 'Failed to save packing record'], 500);
        }
    }

    /**
     * Update an existing packing record and its customer reference.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'batch_reference' => 'required|string|max:100',
            'pack_size' => 'required|string',
            'quantity_packed' => 'required|integer|min:1',
            'packing_date' => 'required|date',
        ]);

        try {
            $record = PackingRecord::findOrFail($id);
            $record->update($validated);

            return response()->json([
                'message' => 'Packing record updated successfully',
                'data' => $record->load('customer')
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating packing record: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update packing record'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = PackingRecord::findOrFail($id);
            $record->delete();
            return response()->json(['message' => 'Packing record deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting packing record: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete record'], 500);
        }
    }
}