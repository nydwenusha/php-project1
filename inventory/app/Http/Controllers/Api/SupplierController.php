<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Supplier::query();

            if ($request->has('only_active')) {
                $query->active();
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('supplier_name', 'like', "%{$search}%")
                      ->orWhere('supplier_code', 'like', "%{$search}%")
                      ->orWhere('contact_information', 'like', "%{$search}%");
                });
            }

            $suppliers = $query->latest()->get()->map(function($supplier) {
                $supplier->formatted_status = $supplier->status ? 'Active' : 'Inactive';
                return $supplier;
            });

            return response()->json($suppliers, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching suppliers: " . $e->getMessage());
            return response()->json(['message' => 'Error fetching suppliers'], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_information' => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        DB::beginTransaction();
        try {
            $lastSupplier = Supplier::orderBy('id', 'desc')->first();
            $nextId = $lastSupplier ? $lastSupplier->id + 1 : 1;
            $supplierCode = 'SUP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            $supplier = Supplier::create([
                'supplier_code' => $supplierCode,
                'supplier_name' => $validated['supplier_name'],
                'address' => $validated['address'],
                'contact_information' => $validated['contact_information'],
                'status' => $validated['status'] === 'Active' ? 1 : 0
            ]);

            DB::commit();
            return response()->json($supplier, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving supplier: " . $e->getMessage());
            return response()->json(['message' => 'Failed to save supplier'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_information' => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {
            $supplier->update([
                'supplier_name' => $validated['supplier_name'],
                'address' => $validated['address'],
                'contact_information' => $validated['contact_information'],
                'status' => $validated['status'] === 'Active' ? 1 : 0
            ]);

            return response()->json($supplier, 200);
        } catch (\Exception $e) {
            Log::error("Error updating supplier: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update supplier'], 500);
        }
    }

    /**
     * Delete a supplier.
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            // Check if supplier is linked to any purchases or inventory records
            $purchaseCount = \DB::table('purchases')->where('supplier_id', $id)->count();

            $brandCount = \DB::table('brands')->where('supplier_id', $id)->count();
            
            if ($purchaseCount > 0 || $brandCount > 0) {
                return response()->json([
                    'message' => 'This supplier cannot be deleted because they are linked to existing transactions or brands.'
                ], 422);
            }

            $supplier->delete();
            return response()->json(['message' => 'Supplier deleted successfully'], 200);

        } catch (\Exception $e) {
            \Log::error("Supplier Delete Error: " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error while deleting supplier.'], 500);
        }
    }
}