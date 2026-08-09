<?php

// Test route to check if Laravel is working
Route::get('/test', function () {
    return 'Laravel is working!';
});

// Test route to check database
Route::get('/db-test', function () {
    try {
        $users = DB::table('users')->get();
        return 'Database working! Found ' . count($users) . ' users.';
    } catch (\Exception $e) {
        return 'Database error: ' . $e->getMessage();
    }
});

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Route to view logs (temporary for debugging)
Route::get('/logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        return '<pre>' . nl2br(file_get_contents($logFile)) . '</pre>';
    }
    return 'No log file found.';
});

Route::get('/{any?}', function () {
    if (file_exists(public_path('index.html'))) {
        return file_get_contents(public_path('index.html'));
    }
    
    return view('welcome');
})->where('any', '^(?!api).*$');
