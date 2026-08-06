<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitchenIssue;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
    /**
     * Fetch all kitchen issue records for history tracking.
     */
    public function index()
    {
        try {
            $issues = KitchenIssue::with('rawMaterial')
                ->orderBy('issue_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($issues, 200);
        } catch (\Exception $e) {
            Log::error("Kitchen History Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to load issue history'], 500);
        }
    }

    /**
     * Handle material issuance to the kitchen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|numeric|min:0.001',
            'source_level' => 'required|in:Raw,Intermediate',
        ]);

        DB::beginTransaction(); 
        try {
            $stock = Stock::lockForUpdate()->findOrFail($validated['stock_id']);

            if ($stock->location !== $validated['source_level']) {
                DB::rollBack();
                return response()->json(['message' => "Source mismatch."], 400);
            }

            if ($stock->quantity < $validated['quantity']) {
                DB::rollBack();
                return response()->json(['message' => "Insufficient stock. Max available: {$stock->quantity}"], 400);
            }

            $stock->decrement('quantity', $validated['quantity']);

            $issue = KitchenIssue::create([
                'issue_date' => $validated['issue_date'],
                'raw_material_id' => $stock->raw_material_id,
                'batch_number' => $stock->batch_number,
                'quantity' => $validated['quantity'],
                'source_level' => $stock->location,
            ]);

            DB::commit();
            return response()->json(['message' => 'Material successfully issued', 'data' => $issue->load('rawMaterial')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing Kitchen Issue.
     */
    public function update(Request $request, $id)
    {
        $issue = KitchenIssue::findOrFail($id);
        
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|numeric|min:0.001',
            'source_level' => 'required|in:Raw,Intermediate',
        ]);

        return DB::transaction(function () use ($validated, $issue) {
            // 1. Revert the old stock deduction
            $oldStock = Stock::where('batch_number', $issue->batch_number)
                ->where('location', $issue->source_level)->first();
            if ($oldStock) {
                $oldStock->increment('quantity', $issue->quantity);
            }

            // 2. Apply the new stock deduction
            $newStock = Stock::lockForUpdate()->findOrFail($validated['stock_id']);
            
            if ($newStock->quantity < $validated['quantity']) {
                return response()->json(['message' => "Insufficient stock for update."], 422);
            }

            $newStock->decrement('quantity', $validated['quantity']);

            // 3. Update issue record
            $issue->update([
                'issue_date' => $validated['issue_date'],
                'raw_material_id' => $newStock->raw_material_id,
                'batch_number' => $newStock->batch_number,
                'quantity' => $validated['quantity'],
                'source_level' => $newStock->location,
            ]);

            return response()->json(['message' => 'Issue record updated successfully.']);
        });
    }

    /**
     * Delete an issue and revert stock.
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $issue = KitchenIssue::findOrFail($id);
            
            $stock = Stock::where('batch_number', $issue->batch_number)
                ->where('location', $issue->source_level)->first();
            
            if ($stock) {
                $stock->increment('quantity', $issue->quantity);
            }

            $issue->delete();
            return response()->json(['message' => 'Issue deleted and stock reverted.']);
        });
    }
}