<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        try {
            $stats = [
                'total_suppliers' => Supplier::count(),
                'total_materials' => RawMaterial::count(),
                'low_stock_alerts' => Stock::where('quantity', '<', 10)->count(), // උදාහරණයකට 10 ට අඩු ඒවා
                'recent_activities' => [] // පසුවට activities එකතු කරමු
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching stats'], 500);
        }
    }
}