<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BrnHeader;
use App\Models\BrnItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrnController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.batch_number' => 'required|string',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.purchase_price' => 'required|numeric|gt:0',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($validated) {
            $brnCode = 'BRN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $brnHeader = BrnHeader::create([
                'brn_code' => $brnCode,
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
            ]);

            foreach ($validated['items'] as $item) {
                $this->processBrnItem($brnHeader, $item);
            }

            return response()->json([
                'message' => 'BRN processed successfully',
                'data' => $brnHeader->load(['supplier', 'items.rawMaterial'])
            ], 201);
        });
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.batch_number' => 'required|string',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.purchase_price' => 'required|numeric|gt:0',
        ]);

        return DB::transaction(function () use ($validated, $id) {
            $header = BrnHeader::findOrFail($id);

            // 1. Reverse Stock for existing items
            foreach ($header->items as $oldItem) {
                $stock = Stock::where('raw_material_id', $oldItem->raw_material_id)
                    ->where('batch_number', $oldItem->batch_number)
                    ->first();
                if ($stock) {
                    $stock->decrement('quantity', $oldItem->quantity);
                    if ($stock->quantity <= 0) $stock->delete();
                }
            }

            // 2. Clear old items and update header
            $header->items()->delete();
            $header->update([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
            ]);

            // 3. Process new items
            foreach ($validated['items'] as $item) {
                $this->processBrnItem($header, $item);
            }

            return response()->json(['message' => 'BRN updated successfully']);
        });
    }

    private function processBrnItem($header, $item)
    {
        $materialId = $item['raw_material_id'];
        $qty = $item['quantity'];
        $price = $item['purchase_price'];

        // WAC Calculation
        $currentStockQty = Stock::where('raw_material_id', $materialId)->sum('quantity');
        $lastBrnItem = BrnItem::where('raw_material_id', $materialId)
            ->join('brn_headers', 'brn_items.brn_header_id', '=', 'brn_headers.id')
            ->orderBy('brn_headers.purchase_date', 'desc')
            ->select('brn_items.avg_cost')
            ->first();

        $oldAvgCost = $lastBrnItem ? $lastBrnItem->avg_cost : 0;
        $totalValue = ($currentStockQty * $oldAvgCost) + ($qty * $price);
        $totalQty = $currentStockQty + $qty;
        $calculatedAvgCost = $totalQty > 0 ? round($totalValue / $totalQty, 2) : round($price, 2);

        $header->items()->create([
            'raw_material_id' => $materialId,
            'batch_number' => $item['batch_number'],
            'quantity' => $qty,
            'purchase_price' => $price,
            'avg_cost' => $calculatedAvgCost,
            'expiry_date' => $item['expiry_date'] ?? null,
        ]);

        Stock::create([
            'raw_material_id' => $materialId,
            'batch_number' => $item['batch_number'],
            'quantity' => $qty,
            'location' => 'Raw',
            'unit_cost' => $price,
            'expiry_date' => $item['expiry_date'] ?? null,
        ]);
    }

    public function index()
    {
        return response()->json(
            BrnHeader::with(['supplier', 'items.rawMaterial'])->latest()->get()
        );
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $header = BrnHeader::with('items')->findOrFail($id);
            foreach ($header->items as $item) {
                $stock = Stock::where('raw_material_id', $item->raw_material_id)
                    ->where('batch_number', $item->batch_number)
                    ->first();

                if ($stock && $stock->quantity >= $item->quantity) {
                    $stock->decrement('quantity', $item->quantity);
                    if ($stock->quantity <= 0) $stock->delete();
                } else {
                    return response()->json(['message' => 'Cannot delete. Stock already used.'], 422);
                }
            }
            $header->items()->delete();
            $header->delete();
            return response()->json(['message' => 'BRN deleted successfully']);
        });
    }
}