<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Retrieve Costing Analytics based on Purchase Receipts (BRN)
     * Linked with Suppliers and Raw Materials tables.
     */
    public function getCostAnalytics(Request $request) 
    {
        return DB::table('brn_items')
            ->join('brn_headers', 'brn_items.brn_header_id', '=', 'brn_headers.id')
            ->join('raw_materials', 'brn_items.raw_material_id', '=', 'raw_materials.id')
            ->join('suppliers', 'brn_headers.supplier_id', '=', 'suppliers.id')
            ->whereBetween('brn_headers.purchase_date', [$request->start_date, $request->end_date])
            ->select(
                'brn_headers.purchase_date as date',
                'suppliers.supplier_name as supplier_name', 
                'raw_materials.item_name as material_name',
                'brn_items.quantity',
                'brn_items.purchase_price',
                DB::raw('(brn_items.quantity * brn_items.purchase_price) as total_cost')
            )
            ->get();
    }

    /**
     * Retrieve Current Stock Levels across different locations
     * Aggregates quantities and calculates Weighted Average Cost (WAC).
     */
    public function getStockReports() 
    {
        return DB::table('stocks')
            ->join('raw_materials', 'stocks.raw_material_id', '=', 'raw_materials.id')
            ->select(
                'raw_materials.item_name',
                'stocks.location',
                DB::raw('SUM(stocks.quantity) as total_qty'),
                DB::raw('AVG(stocks.unit_cost) as avg_unit_cost')
            )
            ->groupBy('raw_materials.item_name', 'stocks.location')
            ->get();
    }

    /**
     * Material Traceability Report
     * Unions Inward (Purchases) and Outward (Kitchen Issues) for material flow tracking.
     */
    public function getTraceabilityReport(Request $request) 
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $inward = DB::table('brn_items')
            ->join('brn_headers', 'brn_items.brn_header_id', '=', 'brn_headers.id')
            ->join('raw_materials', 'brn_items.raw_material_id', '=', 'raw_materials.id')
            ->whereBetween('brn_headers.purchase_date', [$start, $end])
            ->select(
                'brn_headers.purchase_date as date', 
                'raw_materials.item_name as material_name', 
                'brn_items.batch_number', 
                DB::raw("'INWARD (PURCHASE)' as activity"), 
                'brn_items.quantity'
            );

        $outward = DB::table('kitchen_issues')
            ->join('raw_materials', 'kitchen_issues.raw_material_id', '=', 'raw_materials.id')
            ->whereBetween('issue_date', [$start, $end])
            ->select(
                'issue_date as date', 
                'raw_materials.item_name as material_name', 
                'batch_number', 
                DB::raw("'KITCHEN ISSUE' as activity"), 
                'quantity'
            );

        return $inward->union($outward)
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Retrieve Final Packing Records for Finished Goods
     * Fetches details from packing_records table within the selected date range.
     */
    public function getPackingReports(Request $request)
    {
        return DB::table('packing_records')
            ->leftJoin('customers', 'packing_records.customer_id', '=', 'customers.id')
            ->whereBetween('packing_date', [$request->start_date, $request->end_date])
            ->select(
                'packing_date as date',
                'product_name',
                'batch_reference',
                'pack_size',
                'quantity_packed',
                'customers.name as customer_name'
            )
            ->orderBy('packing_date', 'desc')
            ->get();
    }

    /**
     * General Dashboard Analytics Statistics
     */
    public function getDashboardStats() 
    {
        return response()->json([
            'total_suppliers' => DB::table('suppliers')->count(),
            'total_materials' => DB::table('raw_materials')->count(),
            'stock_value' => DB::table('stocks')->sum(DB::raw('quantity * unit_cost'))
        ]);
    }
}