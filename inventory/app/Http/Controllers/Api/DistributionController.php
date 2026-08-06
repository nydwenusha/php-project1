<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Vehicle;
use App\Models\SalesRep;
use App\Models\VehicleStock;
use App\Models\DailyDispatch;
use App\Models\DailyDispatchItem;
use App\Models\DamagedInventoryStock;

class DistributionController extends Controller
{
    // ==========================================
    // MASTER DATA CONFIGURATIONS
    // ==========================================
    
    /**
     * Get all vehicles with live stocks
     */
    public function getVehicles()
    {
        $vehicles = Vehicle::with('liveStocks')->get();
        return response()->json(['success' => true, 'data' => $vehicles]);
    }

    /**
     * Get ONLY ACTIVE vehicles for selection dropdowns
     */
    public function getActiveVehicles()
    {
        $vehicles = Vehicle::where('is_active', 1)->get();
        return response()->json(['success' => true, 'data' => $vehicles]);
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'vehicle_no' => 'required|string|unique:vehicles,vehicle_no',
            'route_area' => 'required|string'
        ]);

        $vehicle = Vehicle::create($validated);
        return response()->json(['success' => true, 'message' => 'Mobile location vehicle registered.', 'data' => $vehicle]);
    }

    /**
     * Update vehicle details
     */
    public function updateVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'vehicle_no' => 'required|string|unique:vehicles,vehicle_no,' . $id,
            'route_area' => 'required|string'
        ]);

        $vehicle->update($validated);
        return response()->json(['success' => true, 'message' => 'Vehicle details updated successfully.', 'data' => $vehicle]);
    }

    /**
     * Toggle Vehicle Active/Inactive Status
     */
    public function toggleVehicleStatus($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->is_active = !$vehicle->is_active;
        $vehicle->save();

        $status = $vehicle->is_active ? 'Activated' : 'Deactivated';
        return response()->json(['success' => true, 'message' => "Vehicle successfully {$status}!", 'data' => $vehicle]);
    }

    /**
     * Get all sales representatives
     */
    public function getSalesReps()
    {
        return response()->json(['success' => true, 'data' => SalesRep::all()]);
    }

    /**
     * Get ONLY ACTIVE sales representatives for selection dropdowns
     */
    public function getActiveSalesReps()
    {
        $reps = SalesRep::where('is_active', 1)->get();
        return response()->json(['success' => true, 'data' => $reps]);
    }

    public function storeSalesRep(Request $request)
    {
        $validated = $request->validate([
            'rep_code' => 'required|string|unique:sales_reps,rep_code',
            'name' => 'required|string',
            'phone' => 'required|string'
        ]);

        $rep = SalesRep::create($validated);
        return response()->json(['success' => true, 'message' => 'Sales representative registered.', 'data' => $rep]);
    }

    /**
     * Update sales representative details
     */
    public function updateSalesRep(Request $request, $id)
    {
        $rep = SalesRep::findOrFail($id);

        $validated = $request->validate([
            'rep_code' => 'required|string|unique:sales_reps,rep_code,' . $id,
            'name' => 'required|string',
            'phone' => 'required|string'
        ]);

        $rep->update($validated);
        return response()->json(['success' => true, 'message' => 'Sales representative updated successfully.', 'data' => $rep]);
    }

    /**
     * Toggle Sales Rep Active/Inactive Status
     */
    public function toggleSalesRepStatus($id)
    {
        $rep = SalesRep::findOrFail($id);
        $rep->is_active = !$rep->is_active;
        $rep->save();

        $status = $rep->is_active ? 'Activated' : 'Deactivated';
        return response()->json(['success' => true, 'message' => "Sales representative successfully {$status}!", 'data' => $rep]);
    }

    public function getMainStockItems()
    {
        try {
            $formatted = DB::table('main_inventory_stocks')
                ->join('final_items', 'main_inventory_stocks.final_item_id', '=', 'final_items.id')
                ->select(
                    'final_items.id as id',
                    'final_items.item_name',
                    'final_items.item_code',
                    'final_items.uom',
                    'main_inventory_stocks.available_qty'
                )
                ->get();

            return response()->json(['success' => true, 'data' => $formatted]);
        } catch (\Exception $e) {
            Log::error('Main stock items fetch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch main stock items: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🌟 NEW (Phase 2): Get vehicle stock level
     */
    public function getVehicleStock($vehicleId)
    {
        try {
            $stock = DB::table('vehicle_stocks')
                ->join('final_items', 'vehicle_stocks.final_item_id', '=', 'final_items.id')
                ->where('vehicle_stocks.vehicle_id', $vehicleId)
                ->select(
                    'final_items.id as final_item_id',
                    'final_items.item_name',
                    'final_items.item_code',
                    'final_items.uom',
                    'vehicle_stocks.quantity as current_qty'
                )
                ->get();

            return response()->json(['success' => true, 'data' => $stock]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch vehicle live stock: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ☀️ MORNING PROCESS: BULK LOADING LOGIC
    // ==========================================
    
    public function loadStock(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'sales_rep_id' => 'required|exists:sales_reps,id',
            'dispatch_date' => 'required|date',
            'route' => 'nullable|string|max:255', // 🌟 NEW: Added validation for the daily dispatch lorry route
            'items' => 'required|array|min:1',
            'items.*.final_item_id' => 'required',
            'items.*.new_loaded_qty' => 'required|integer|min:0'
        ]);

        $pendingTrip = DailyDispatch::where('vehicle_id', $request->vehicle_id)
            ->where('status', 'loaded')
            ->exists();

        if ($pendingTrip) {
            return response()->json([
                'success' => false, 
                'message' => 'Dispatch Rejected! This vehicle has an outstanding pending trip that must be reconciled first.'
            ], 422);
        }

        $gatePassNo = 'GP-' . strtoupper(\Illuminate\Support\Str::random(5)) . '-' . date('Ymd');

        DB::beginTransaction();
        try {
            $dispatch = DailyDispatch::create([
                'dispatch_date' => $request->dispatch_date,
                'gate_pass_no' => $gatePassNo,
                'vehicle_id' => $request->vehicle_id,
                'sales_rep_id' => $request->sales_rep_id,
                'route' => $request->route, // 🌟 NEW: Save the typed text route directly into the dispatch tracking log
                'status' => 'loaded',
                'user_id' => auth()->id() ?? 1
            ]);

            foreach ($request->items as $itemInput) {
                $newLoaded = (int)$itemInput['new_loaded_qty'];

                $currentVehicleStock = VehicleStock::where('vehicle_id', $request->vehicle_id)
                    ->where('final_item_id', $itemInput['final_item_id'])
                    ->first();

                $carriedForward = $currentVehicleStock ? $currentVehicleStock->quantity : 0;
                $totalQty = $carriedForward + $newLoaded;

                if ($totalQty <= 0) continue; 

                if ($newLoaded > 0) {
                    $mainStock = \App\Models\MainInventoryStock::where('final_item_id', $itemInput['final_item_id'])->first();

                    if (!$mainStock) {
                        return response()->json([
                            'success' => false,
                            'message' => "Item (ID: {$itemInput['final_item_id']}) not found in main stock!"
                        ], 404);
                    }

                    if ($mainStock->available_qty < $newLoaded) {
                        $itemDetails = DB::table('final_items')->where('id', $itemInput['final_item_id'])->first();
                        $itemName = $itemDetails ? $itemDetails->item_name : "Selected Item";

                        return response()->json([
                            'success' => false,
                            'message' => "{$itemName} current stock level is {$mainStock->available_qty} . Main stock quantity is not enough for loading {$newLoaded} units!"
                        ], 400);
                    }

                    $mainStock->decrement('available_qty', $newLoaded);
                }

                DailyDispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'final_item_id' => $itemInput['final_item_id'],
                    'carried_forward_qty' => $carriedForward,
                    'new_loaded_qty' => $newLoaded,
                    'total_qty' => $totalQty
                ]);

                VehicleStock::updateOrCreate(
                    ['vehicle_id' => $request->vehicle_id, 'final_item_id' => $itemInput['final_item_id']],
                    ['quantity' => $totalQty]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Lorry stock dispatch generated successfully.', 'gate_pass' => $gatePassNo]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dispatch error occurred: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to initialize dispatch routing logs: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 🌙 EVENING PROCESS: RECONCILIATION LOGIC
    // ==========================================
    
    public function reconcileDispatch(Request $request, $id)
    {
        $request->validate([
            'action_type' => 'required|in:carry_forward,unload_to_store',
            'items' => 'required|array',
            'items.*.final_item_id' => 'required',
            'items.*.actual_sales' => 'required|integer|min:0',
            'items.*.damaged_qty' => 'required|integer|min:0',
            'items.*.remaining_qty' => 'required|integer|min:0'
        ]);

        $dispatch = DailyDispatch::where('id', $id)->where('status', 'loaded')->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemRecon) {
                $childItem = DailyDispatchItem::where('dispatch_id', $dispatch->id)
                    ->where('final_item_id', $itemRecon['final_item_id'])
                    ->first();

                if (!$childItem) continue;

                $sumCheck = (int)$itemRecon['actual_sales'] + (int)$itemRecon['damaged_qty'] + (int)$itemRecon['remaining_qty'];
                if ($sumCheck !== $childItem->total_qty) {
                    throw new \Exception("Reconciliation mismatch for item ID: " . $itemRecon['final_item_id'] . ". Computed balances must equate loaded limits.");
                }

                $childItem->update([
                    'actual_sales' => $itemRecon['actual_sales'],
                    'damaged_qty' => $itemRecon['damaged_qty'],
                    'remaining_qty' => $itemRecon['remaining_qty']
                ]);

                // =================================================================
                // 🌟 NEW (Phase 1): DAMAGED ITEMS LOGIC 
                // =================================================================
                $damagedQty = (int)$itemRecon['damaged_qty'];
                if ($damagedQty > 0) {
                    $damagedStock = DamagedInventoryStock::firstOrNew(['final_item_id' => $itemRecon['final_item_id']]);
                    $damagedStock->quantity += $damagedQty;
                    $damagedStock->save();
                }
                // =================================================================

                if ($request->action_type === 'carry_forward') {
                    VehicleStock::updateOrCreate(
                        ['vehicle_id' => $dispatch->vehicle_id, 'final_item_id' => $itemRecon['final_item_id']],
                        ['quantity' => $itemRecon['remaining_qty']]
                    );
                } else {
                    VehicleStock::updateOrCreate(
                        ['vehicle_id' => $dispatch->vehicle_id, 'final_item_id' => $itemRecon['final_item_id']],
                        ['quantity' => 0]
                    );

                    $remainingQty = (int)$itemRecon['remaining_qty'];
                    if ($remainingQty > 0) {
                        $mainStock = \App\Models\MainInventoryStock::where('final_item_id', $itemRecon['final_item_id'])->first();

                        if ($mainStock) {
                            $mainStock->increment('available_qty', $remainingQty);
                        } else {
                            throw new \Exception("Item (ID: " . $itemRecon['final_item_id'] . ") not found in main stock!");
                        }
                    }
                }
            }

            $dispatch->status = $request->action_type === 'carry_forward' ? 'reconciled' : 'unloaded';
            $dispatch->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Evening stock clearance authenticated and closed successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function getPendingDispatches()
    {
        $logs = DailyDispatch::with(['vehicle', 'salesRep', 'items'])
            ->where('status', 'loaded')
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }

    // ==========================================
    // 📈 PHASE 3: HISTORY & REPORTING GENERATION
    // ==========================================

    /**
     * Fetch filtered dispatch logs based on an authenticated date range query context.
     */
    public function getDispatchHistory(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        try {
            $history = DailyDispatch::with(['vehicle', 'salesRep', 'items.item'])
                ->whereBetween('dispatch_date', [$request->start_date, $request->end_date])
                ->orderBy('dispatch_date', 'DESC')
                ->get();

            return response()->json(['success' => true, 'data' => $history]);
        }  catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracking history metrics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process compilation matrix and stream computed reports into downloadable PDF.
     */
    public function downloadHistoryPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $history = DailyDispatch::with(['vehicle', 'salesRep', 'items.item'])
            ->whereBetween('dispatch_date', [$request->start_date, $request->end_date])
            ->orderBy('dispatch_date', 'ASC')
            ->get();

        // Render the view template directly using barryvdh/laravel-dompdf facade interface hooks
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.dispatch_history', compact('history', 'request'));
        
        // Return structured attachment stream
        return $pdf->download('Logistics_Report_' . $request->start_date . '.pdf');
    }
}