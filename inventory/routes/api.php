<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers Import
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\BrnController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\BlendingController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\PackingController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ReportController; 
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FinalProductionController;
use App\Http\Controllers\Api\DistributionController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/reset-password-direct', [UserController::class, 'resetPasswordDirect']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/reports/dashboard-stats', [ReportController::class, 'getDashboardStats']);
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);

    // Masters & Resources
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('customers', CustomerController::class); // Added Customer Route
    Route::apiResource('materials', RawMaterialController::class);
    
    // Manual Resource Overrides/Extras
    Route::put('/materials/{id}', [RawMaterialController::class, 'update']);
    Route::delete('/materials/{id}', [RawMaterialController::class, 'destroy']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Added for Customers

    // Inventory & BRN
    Route::apiResource('brn', BrnController::class);
    Route::apiResource('stock', StockController::class);
    // Compatibility routes
    Route::get('/stocks', [StockController::class, 'index']); 
    Route::get('/stocks/location/{location}', [StockController::class, 'getByLocation']);

    // Blending & Production
    Route::apiResource('blending', BlendingController::class);
    Route::get('/blending-records', [BlendingController::class, 'index']);
    Route::get('/blending-records/location/{location}', [BlendingController::class, 'getByLocation']);
    Route::get('/blending-records/batch/{batch}', [BlendingController::class, 'getByBatch']);
    Route::get('/blending-records/date/{date}', [BlendingController::class, 'getByDate']);
    Route::get('/blending-records/operator/{operator}', [BlendingController::class, 'getByOperator']);
    Route::get('/blending-records/material/{material}', [BlendingController::class, 'getByMaterial']);
    
    Route::apiResource('kitchen-issue', KitchenController::class);
    Route::get('/kitchen-issues', [KitchenController::class, 'index']);
    Route::get('/kitchen-issues/date/{date}', [KitchenController::class, 'getByDate']);
    Route::get('/kitchen-issues/operator/{operator}', [KitchenController::class, 'getByOperator']);
    Route::get('/kitchen-issues/issue/{issue}', [KitchenController::class, 'getByIssue']);

    // Standard CRUD for Packing
    Route::apiResource('packing', PackingController::class);
    Route::get('/packing-records', [PackingController::class, 'index']);

    // Reports Section - FIXING 404 BY ORDERING & NAMING
    Route::prefix('reports')->group(function () {
        Route::get('/costs', [ReportController::class, 'getCostAnalytics']);
        Route::get('/stocks', [ReportController::class, 'getStockReports']);
        Route::get('/traceability', [ReportController::class, 'getTraceabilityReport']);
        
        // Use 'packing-report' to avoid conflict with 'apiResource('packing')'
        Route::get('/packing-report', [ReportController::class, 'getPackingReports']);
    });

    // Customer Routes (Cleanup duplicate logic)
    // apiResource and destroy are already defined above, keeping this group for safety as per original
    Route::apiResource('customers', CustomerController::class);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

    // User Management
    Route::apiResource('users', UserController::class);
    Route::put('/users/{id}/status', [UserController::class, 'updateStatus']);

    // Final Production Custom Module Routes
    Route::prefix('final-production')->group(function () {
        // Items Routes
        Route::get('/items', [FinalProductionController::class, 'getItems']);
        Route::post('/items', [FinalProductionController::class, 'storeItem']);
        Route::put('/items/{id}', [FinalProductionController::class, 'updateItem']);
        Route::delete('/items/{id}', [FinalProductionController::class, 'deleteItem']);

        // Handlers (Staff)
        Route::get('/handlers', [FinalProductionController::class, 'getHandlers']);
        Route::post('/handlers', [FinalProductionController::class, 'storeHandler']);
        Route::put('/handlers/{id}', [FinalProductionController::class, 'updateHandler']); // Added for Edit
        Route::patch('/handlers/{id}/status', [FinalProductionController::class, 'toggleStatus']); // Added for Status Toggle
        Route::delete('/handlers/{id}', [FinalProductionController::class, 'deleteHandler']);

        // Production Intakes
        Route::get('/intake-logs', [FinalProductionController::class, 'getIntakeLogs']);
        Route::post('/intake-store', [FinalProductionController::class, 'storeIntake']);
        Route::put('/intake/{id}', [FinalProductionController::class, 'updateIntake']);
        Route::delete('/intake/{id}', [FinalProductionController::class, 'deleteIntake']);
        Route::get('/stock', [FinalProductionController::class, 'getMainStock']);
        // Route::get('/items-dropdown-all', [FinalProductionController::class, 'getAllItemsForDropdown']);

        // Production Intake Logs History & Main Stock PDF Reports
        Route::get('/production-history-pdf', [FinalProductionController::class, 'downloadProductionPdf']);
        Route::get('/main-stock-pdf', [FinalProductionController::class, 'downloadStockPdf']);
    });

    // ==========================================================================
    // Distribution & Mobile Stock Tracking Routes
    // ==========================================================================
    Route::prefix('distribution')->group(function () {
        // Master Setup Configurations
        Route::get('/vehicles', [DistributionController::class, 'getVehicles']);
        Route::post('/vehicles', [DistributionController::class, 'storeVehicle']);
        Route::get('/sales-reps', [DistributionController::class, 'getSalesReps']);
        Route::post('/sales-reps', [DistributionController::class, 'storeSalesRep']);

        // Operations Handlers
        Route::get('/pending-trips', [DistributionController::class, 'getPendingDispatches']);
        Route::post('/load-stock', [DistributionController::class, 'loadStock']); // Morning Dispatch
        Route::post('/reconcile/{id}', [DistributionController::class, 'reconcileDispatch']); // Evening Reconciliation
        Route::get('/main-stock-items', [DistributionController::class, 'getMainStockItems']);
        Route::get('/vehicle-stock/{vehicleId}', [DistributionController::class, 'getVehicleStock']);

        // History & Report Management Routes
        Route::get('/dispatch-history', [DistributionController::class, 'getDispatchHistory']);
        Route::get('/dispatch-history-pdf', [DistributionController::class, 'downloadHistoryPdf']);

        // --- Vehicles Management ---
        Route::get('/vehicles/active', [DistributionController::class, 'getActiveVehicles']);
        Route::put('/vehicles/{id}', [DistributionController::class, 'updateVehicle']);
        Route::patch('/vehicles/{id}/toggle-status', [DistributionController::class, 'toggleVehicleStatus']);

        // --- Sales Reps Management ---
        Route::get('/sales-reps/active', [DistributionController::class, 'getActiveSalesReps']);
        Route::put('/sales-reps/{id}', [DistributionController::class, 'updateSalesRep']);
        Route::patch('/sales-reps/{id}/toggle-status', [DistributionController::class, 'toggleSalesRepStatus']);

    });

    Route::get('/login', function () {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    })->name('login');


});