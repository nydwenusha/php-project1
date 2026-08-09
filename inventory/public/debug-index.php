<?php

// Enable maximum error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "=== DEBUGGING LARAVEL BOOTSTRAP ===<br><br>";

try {
    // Step 1: Check vendor/autoload.php
    echo "Step 1: Loading autoloader... ";
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception("autoload.php not found at: " . $autoloadPath);
    }
    require $autoloadPath;
    echo "✅ DONE<br>";
    
    // Step 2: Check bootstrap/app.php
    echo "Step 2: Loading app.php... ";
    $appPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($appPath)) {
        throw new Exception("app.php not found at: " . $appPath);
    }
    $app = require_once $appPath;
    echo "✅ DONE<br>";
    
    // Step 3: Check if app is instance of Application
    echo "Step 3: Checking application... ";
    if (!$app instanceof Illuminate\Foundation\Application) {
        throw new Exception("App is not an instance of Application");
    }
    echo "✅ DONE<br>";
    
    // Step 4: Check if kernel exists
    echo "Step 4: Getting HTTP kernel... ";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ DONE<br>";
    
    // Step 5: Capture request
    echo "Step 5: Capturing request... ";
    $request = Illuminate\Http\Request::capture();
    echo "✅ DONE<br>";
    echo "Request URI: " . $request->getRequestUri() . "<br>";
    
    // Step 6: Handle request
    echo "Step 6: Handling request... ";
    $response = $kernel->handle($request);
    echo "✅ DONE<br>";
    
    // Step 7: Check response
    echo "Step 7: Response status... ";
    $status = $response->status();
    echo "Status: " . $status . "<br>";
    
    // Send response
    echo "Step 8: Sending response... ";
    $response->send();
    echo "✅ DONE<br>";
    
    // Terminate
    $kernel->terminate($request, $response);
    
} catch (Throwable $e) {
    echo "<br><br>❌ ERROR:<br>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<br><strong>Stack trace:</strong><br><pre>" . $e->getTraceAsString() . "</pre>";
}
