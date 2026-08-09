<?php

echo "Testing index.php...<br><br>";

// Check if index.php exists
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    echo "✅ index.php found at: " . $indexPath . "<br><br>";
    
    echo "Attempting to execute index.php...<br>";
    echo "<hr>";
    
    // Capture output and errors
    try {
        // Include the file but capture any output
        ob_start();
        include $indexPath;
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "⚠️ index.php executed but produced no output (likely a redirect or error)<br>";
        } else {
            echo "✅ index.php produced output:<br>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    } catch (Throwable $e) {
        ob_end_clean();
        echo "❌ ERROR in index.php:<br>";
        echo "Message: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
    }
} else {
    echo "❌ index.php NOT found at: " . $indexPath . "<br>";
}

echo "<hr>";
echo "Test complete!";
