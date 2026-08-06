<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    /**
     * Retrieve all active stock records with related raw material details.
     * Filtered to show only available quantities.
     */
    public function index()
    {
        try {
            $stocks = Stock::with(['rawMaterial' => function($query) {
                    $query->select('id', 'item_name', 'item_code', 'unit_of_measure');
                }])
                ->where('quantity', '>', 0)
                ->orderBy('raw_material_id')
                ->orderByRaw('expiry_date IS NULL, expiry_date ASC') // Null expiry dates moved to end
                ->get();
            
            return response()->json($stocks, 200);
        } catch (\Exception $e) {
            Log::error("Inventory Display Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to load stock inventory data'
            ], 500);
        }
    }

    /**
     * Handle stock reduction using logic-based batch selection (FIFO or LIFO).
     */
    public function consumeStock(Request $request)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'required_qty'    => 'required|numeric|min:0.001',
            'location'        => 'required|string',
            'method'          => 'required|in:FIFO,LIFO'
        ]);

        $materialId = $validated['raw_material_id'];
        $needed = $validated['required_qty'];
        $sortOrder = $validated['method'] === 'FIFO' ? 'asc' : 'desc';

        DB::beginTransaction();
        try {
            // Retrieve available batches based on chosen sorting method
            $batches = Stock::where('raw_material_id', $materialId)
                ->where('location', $validated['location'])
                ->where('quantity', '>', 0)
                ->orderBy('created_at', $sortOrder)
                ->get();

            $totalAvailable = $batches->sum('quantity');

            if ($totalAvailable < $needed) {
                return response()->json(['message' => 'Insufficient stock levels to fulfill request'], 422);
            }

            foreach ($batches as $batch) {
                if ($needed <= 0) break;

                $consumption = min($batch->quantity, $needed);
                
                // Deduct consumption from batch quantity
                $batch->decrement('quantity', $consumption);
                $needed -= $consumption;
                
                // Note: Optional Stock Movement Log entry can be added here
            }

            DB::commit();
            return response()->json(['message' => 'Stock successfully consumed from inventory'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Inventory Consumption Failure: " . $e->getMessage());
            return response()->json(['message' => 'Critical error during inventory adjustment'], 500);
        }
    }

    /**
     * Get real-time stock availability for a specific store location.
     */
    public function getByLocation($location)
    {
        // Fixed: Replaced undefined scopes with standard where clauses
        $stocks = Stock::with(['rawMaterial' => function($query) {
                $query->select('id', 'item_name', 'item_code', 'unit_of_measure');
            }])
            ->where('location', $location)
            ->where('quantity', '>', 0)
            ->get();

        return response()->json($stocks);
    }

    public function getAvailableBatches()
    {
        try {
            $stocks = Stock::with(['rawMaterial' => function($query) {
                    $query->select('id', 'item_name', 'item_code');
                }])
                ->where('quantity', '>', 0)
                ->select('id', 'batch_number', 'raw_material_id', 'quantity')
                ->get();
            
            return response()->json($stocks, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error loading batches'], 500);
        }
    }
}