<?php

echo "Testing Laravel bootstrap...<br>";

// Test 1: Check if autoloader exists
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "✅ autoload.php found at: " . $autoloadPath . "<br>";
    require $autoloadPath;
    echo "✅ Composer autoloader loaded<br>";
} else {
    echo "❌ autoload.php NOT found at: " . $autoloadPath . "<br>";
}

// Test 2: Check if app.php exists
$appPath = __DIR__ . '/../bootstrap/app.php';
if (file_exists($appPath)) {
    echo "✅ bootstrap/app.php found at: " . $appPath . "<br>";
} else {
    echo "❌ bootstrap/app.php NOT found at: " . $appPath . "<br>";
}

// Test 3: Check if .env exists
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "✅ .env found at: " . $envPath . "<br>";
} else {
    echo "❌ .env NOT found at: " . $envPath . "<br>";
}

// Test 4: Check storage permissions
$storagePath = __DIR__ . '/../storage';
if (is_writable($storagePath)) {
    echo "✅ storage is writable<br>";
} else {
    echo "❌ storage is NOT writable<br>";
}

$logsPath = __DIR__ . '/../storage/logs';
if (is_writable($logsPath)) {
    echo "✅ logs is writable<br>";
} else {
    echo "❌ logs is NOT writable<br>";
}

echo "<br>All tests complete!";
