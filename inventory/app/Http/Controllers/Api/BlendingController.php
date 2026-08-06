<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlendingRecord;
use App\Models\Stock;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlendingController extends Controller
{
    public function index()
    {
        return BlendingRecord::with('rawMaterial')->orderBy('blending_date', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blending_date' => 'required|date',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'input_batch_number' => 'required|string',
            'input_weight' => 'required|numeric|min:0.01',
            'output_weight' => 'required|numeric|min:0.01',
        ]);

        // Removed the hardcoded 'Dal' and 'Chili' restriction to allow all raw materials
        $material = RawMaterial::findOrFail($validated['raw_material_id']);

        return DB::transaction(function () use ($validated) {
            $rawStock = Stock::where('raw_material_id', $validated['raw_material_id'])
                ->where('batch_number', $validated['input_batch_number'])
                ->where('location', 'Raw')->lockForUpdate()->first();

            if (!$rawStock || $rawStock->quantity < $validated['input_weight']) {
                return response()->json(['message' => "Insufficient stock."], 422);
            }

            $rawStock->decrement('quantity', $validated['input_weight']);
            $newBatch = 'INT-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            $blending = BlendingRecord::create([
                'blending_date' => $validated['blending_date'],
                'raw_material_id' => $validated['raw_material_id'],
                'input_batch_number' => $validated['input_batch_number'],
                'input_weight' => $validated['input_weight'],
                'output_weight' => $validated['output_weight'],
                'new_batch_number' => $newBatch
            ]);

            Stock::create([
                'raw_material_id' => $validated['raw_material_id'],
                'batch_number' => $newBatch,
                'quantity' => $validated['output_weight'],
                'location' => 'Intermediate',
                'unit_cost' => $rawStock->unit_cost,
            ]);

            return response()->json(['message' => 'Processed Successfully.', 'data' => $blending], 201);
        });
    }

    public function update(Request $request, $id)
    {
        $record = BlendingRecord::findOrFail($id);
        
        $validated = $request->validate([
            'blending_date' => 'required|date',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'input_batch_number' => 'required|string',
            'input_weight' => 'required|numeric|min:0.01',
            'output_weight' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated, $record) {
            // 1. First, REVERT the old stock impact
            $oldRawStock = Stock::where('batch_number', $record->input_batch_number)
                ->where('location', 'Raw')->first();
            if ($oldRawStock) {
                $oldRawStock->increment('quantity', $record->input_weight);
            }

            // 2. Apply the NEW stock impact (Check if new stock is sufficient)
            $newRawStock = Stock::where('raw_material_id', $validated['raw_material_id'])
                ->where('batch_number', $validated['input_batch_number'])
                ->where('location', 'Raw')->lockForUpdate()->first();

            if (!$newRawStock || $newRawStock->quantity < $validated['input_weight']) {
                // If weight check fails, rollback the increment before returning
                if ($oldRawStock) {
                    $oldRawStock->decrement('quantity', $record->input_weight);
                }
                return response()->json(['message' => "Update failed: Insufficient stock for new weight."], 422);
            }

            $newRawStock->decrement('quantity', $validated['input_weight']);

            // 3. Update the Intermediate stock quantity
            Stock::where('batch_number', $record->new_batch_number)
                ->where('location', 'Intermediate')
                ->update([
                    'raw_material_id' => $validated['raw_material_id'],
                    'quantity' => $validated['output_weight']
                ]);

            // 4. Update the record
            $record->update($validated);

            return response()->json(['message' => 'Record updated successfully.']);
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $record = BlendingRecord::findOrFail($id);
            Stock::where('batch_number', $record->new_batch_number)->delete();
            $rawStock = Stock::where('batch_number', $record->input_batch_number)->where('location', 'Raw')->first();
            if ($rawStock) {
                $rawStock->increment('quantity', $record->input_weight);
            }
            $record->delete();
            return response()->json(['message' => 'Record deleted and stock reverted.']);
        });
    }
}