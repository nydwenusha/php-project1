<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RawMaterialController extends Controller
{
    /**
     * Store a newly created raw material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:Raw Spices,Oils & Liquids,Packing Materials,Additives & Preservatives,Finished Goods,Others',
            'unit_of_measure' => 'required|in:g,kg,L,ml',
            'shelf_life' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        // Auto-generate Item Code (ITM-001, ITM-002...)
        $lastItem = RawMaterial::latest()->first();
        $nextId = $lastItem ? $lastItem->id + 1 : 1;
        $itemCode = 'ITM-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $material = RawMaterial::create(array_merge($validated, ['item_code' => $itemCode]));

        return response()->json($material->load('brand'), 201);
    }

    /**
     * Update the specified raw material.
     */
    public function update(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:Raw Spices,Oils & Liquids,Packing Materials,Additives & Preservatives,Finished Goods,Others',
            'unit_of_measure' => 'required|in:g,kg,L,ml',
            'shelf_life' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        $material->update($validated);

        return response()->json($material->load('brand'), 200);
    }

    /**
     * Get all raw materials with brand details.
     */
    public function index(Request $request)
    {
        $query = RawMaterial::with('brand');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('item_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('item_code', 'like', '%' . $searchTerm . '%');
            });
        }

        return response()->json($query->latest()->get());
    }

    public function destroy($id)
    {
        try {
            $material = RawMaterial::findOrFail($id);
            
            // Check usage in blending_records
            $usageCount = \DB::table('blending_records')->where('raw_material_id', $id)->count();
            
            if ($usageCount > 0) {
                return response()->json([
                    'message' => 'This material cannot be deleted because it is already linked to blending records.'
                ], 422); 
            }

            $material->delete();
            return response()->json(['message' => 'Material deleted successfully'], 200);
            
        } catch (\Exception $e) {
            \Log::error("Delete Error: " . $e->getMessage());
            return response()->json(['message' => 'Something went wrong on the server.'], 500);
        }
    }
}