<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return response()->json(Brand::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|unique:brands,brand_name',
            'description' => 'nullable|string'
        ]);

        $brand = Brand::create($validated);
        return response()->json($brand, 201);
    }

    public function destroy($id)
    {
        Brand::destroy($id);
        return response()->json(null, 204);
    }
}