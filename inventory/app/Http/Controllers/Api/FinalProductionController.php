<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FinalItem;
use App\Models\ProductionHandler;
use App\Models\FinalProductionIntakeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\MainInventoryStock;

class FinalProductionController extends Controller
{
    // ==========================================
    // 1. FINAL ITEMS MANAGEMENT
    // ==========================================

    /**
     * Get paginated and searchable final items list ordered by name
     */
    public function getItems(Request $request)
    {
        $search = $request->query('search');

        $query = FinalItem::orderBy('item_name', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('all') || $request->query('per_page') == 9999) {
            $items = $query->get();
            return response()->json(['success' => true, 'data' => $items], 200);
        }

        $items = $query->paginate(10);
        return response()->json(['success' => true, 'data' => $items], 200);
    }

    // public function getAllItemsForDropdown() {
    //     $items = FinalItem::all();
    //     return response()->json(['success' => true, 'data' => $items]);
    // }

    /**
     * Register a new final item
     */
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|unique:final_items,item_code',
            'uom' => 'required|string', 
            'cost_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
            'shelf_life' => 'nullable|string|max:255',
        ]);

        $validated['cost_price'] = $validated['cost_price'] ?? 0;
        $validated['selling_price'] = $validated['selling_price'] ?? 0;

        $item = FinalItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item registered successfully',
            'data' => $item
        ]);
    }

    /**
     * Update an existing final item
     */
    public function updateItem(Request $request, $id)
    {
        $item = FinalItem::findOrFail($id);
        
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|unique:final_items,item_code,' . $id,
            'uom' => 'required|string', 
            'cost_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
            'shelf_life' => 'nullable|string|max:255',
        ]);

        $validated['cost_price'] = $validated['cost_price'] ?? 0;
        $validated['selling_price'] = $validated['selling_price'] ?? 0;

        $item->update($validated);
        return response()->json(['success' => true, 'message' => 'Item updated successfully!', 'data' => $item]);
    }

    /**
     * Delete a final item with safety constraints check
     */
    public function deleteItem($id)
    {
        $item = FinalItem::findOrFail($id);
        
        $hasLogs = \App\Models\FinalProductionIntakeLog::where('final_item_id', $id)->exists();
        if ($hasLogs) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete this item because it has recorded production logs!'
            ], 400);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully!']);
    }

    // ==========================================
    // 2. PRODUCTION HANDLERS MANAGEMENT
    // ==========================================

    /**
     * Get paginated and searchable production handlers list ordered by name
     */
    public function getHandlers(Request $request)
    {
        $search = $request->query('search');

        $query = ProductionHandler::orderBy('handler_name', 'asc');

        // Server-side global search for handlers by database ID, name, or phone number
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('handler_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $handlers = $query->paginate(10);
        return response()->json(['success' => true, 'data' => $handlers], 200);
    }

    /**
     * Register a new production handler / staff member
     */
    public function storeHandler(Request $request)
    {
        $validated = $request->validate([
            'handler_name' => 'required|string|max:255',
            'handler_code' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $handler = ProductionHandler::create($validated);

        return response()->json(['success' => true, 'message' => 'Production handler added successfully!', 'data' => $handler], 201);
    }

    /**
     * Update an existing production handler's details
     */
    public function updateHandler(Request $request, $id)
    {
        $handler = ProductionHandler::findOrFail($id);
        
        $validated = $request->validate([
            'handler_name' => 'required|string|max:255',
            'handler_code' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $handler->update($validated);
        return response()->json(['success' => true, 'message' => 'Handler updated successfully!', 'data' => $handler]);
    }

    /**
     * Toggle the active state status of a production handler
     */
    public function toggleStatus($id)
    {
        $handler = ProductionHandler::findOrFail($id);
        $handler->is_active = !$handler->is_active;
        $handler->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully!', 'data' => $handler]);
    }

    /**
     * Delete a production handler
     */
    public function deleteHandler($id)
    {
        $handler = ProductionHandler::findOrFail($id);
        $handler->delete();

        return response()->json(['success' => true, 'message' => 'Handler deleted successfully!']);
    }

    // ==========================================
    // 3. FINAL PRODUCTION INTAKE (MAIN LOGIC)
    // ==========================================

    /**
     * Record a new daily production stock entry
     */
    public function storeIntake(Request $request)
    {
        $request->validate([
            'final_item_id' => 'required|exists:final_items,id',
            'handler_id' => 'required|exists:production_handlers,id',
            'quantity' => 'required|integer|min:1',
            'created_at' => 'nullable|date', 
        ]);

        DB::beginTransaction();

        try {
            $intakeDate = $request->created_at ? $request->created_at : now();

            $model = new FinalProductionIntakeLog();
            $model->timestamps = false; 

            $model->final_item_id = $request->final_item_id;
            $model->handler_id = $request->handler_id;
            $model->quantity = $request->quantity;
            $model->system_user_id = Auth::id() ?? 1;
            $model->created_at = $intakeDate; 
            $model->updated_at = now();
            $model->save();

            DB::commit();

            $responseData = FinalProductionIntakeLog::with(['item', 'handler', 'user'])->find($model->id);

            return response()->json([
                'success' => true,
                'message' => 'Production stock recorded successfully!',
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated and searchable inventory entry intake records
     */
    public function getIntakeLogs(Request $request)
    {
        $search = $request->query('search');

        $query = FinalProductionIntakeLog::with(['item', 'handler', 'user'])
            ->orderBy('created_at', 'desc');

        // Server-side conditional filtering across multiple nested relationship attributes
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('created_at', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($itemQuery) use ($search) {
                      $itemQuery->where('item_code', 'like', "%{$search}%")
                                ->orWhere('item_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('handler', function ($handlerQuery) use ($search) {
                      $handlerQuery->where('handler_code', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(10);
        return response()->json(['success' => true, 'data' => $logs], 200);
    }

    /**
     * Update an existing production stock intake entry
     */
    public function updateIntake(Request $request, $id)
    {
        $log = FinalProductionIntakeLog::findOrFail($id);

        $request->validate([
            'final_item_id' => 'required|exists:final_items,id',
            'handler_id' => 'required|exists:production_handlers,id',
            'quantity' => 'required|integer|min:1',
            'created_at' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $log->timestamps = false; // Prevent Laravel from overriding created_at automatically
            
            $log->final_item_id = $request->final_item_id;
            $log->handler_id = $request->handler_id;
            $log->quantity = $request->quantity;
            if ($request->created_at) {
                $log->created_at = $request->created_at;
            }
            $log->updated_at = now();
            $log->save();

            DB::commit();

            $responseData = FinalProductionIntakeLog::with(['item', 'handler', 'user'])->find($log->id);

            return response()->json([
                'success' => true,
                'message' => 'Production log updated successfully!',
                'data' => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a production stock intake entry
     */
    public function deleteIntake($id)
    {
        try {
            $log = FinalProductionIntakeLog::findOrFail($id);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Production record deleted successfully!'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the production record!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get current stock levels for all final items in the main inventory
    public function getMainStock()
    {
        try {
            $stock = \App\Models\MainInventoryStock::orderBy('item_name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $stock
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock data could not be retrieved: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exports the production intake logs history within a date range as a PDF report.
     */
    public function downloadProductionPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $history = FinalProductionIntakeLog::with(['item', 'handler', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('pdf.production_history', compact('history', 'request'));
        
        return $pdf->download("Production_History_Report_{$request->start_date}_to_{$request->end_date}.pdf");
    }

    /**
     * Exports the main inventory stock data as a PDF report.
     */
    public function downloadStockPdf()
    {
        $stockItems = MainInventoryStock::with(['item'])->orderBy('id', 'asc')->get();

        $pdf = PDF::loadView('pdf.main_stock', compact('stockItems'));
        
        return $pdf->download("Main_Inventory_Stock_Report_" . date('Y-m-d') . ".pdf");
    }
}