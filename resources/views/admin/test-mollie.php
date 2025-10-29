<?php
/**
 * Mollie Integration Test Script
 * Use this to verify your setup is working correctly
 */

echo "<h1>Mollie Integration Test</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;background:#f5f5f5;}h1{color:#333;}pre{background:#fff;padding:15px;border-radius:8px;border:1px solid #ddd;}.success{color:green;}.error{color:red;}</style>";

// Test 1: Composer Autoload
echo "<h2>1. Composer Autoload</h2>";
$autoloadPath = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    echo "<p class='success'>✅ Composer autoload found and loaded</p>";
    echo "<pre>Path: " . realpath($autoloadPath) . "</pre>";
} else {
    echo "<p class='error'>❌ Composer autoload not found!</p>";
    echo "<pre>Expected at: $autoloadPath</pre>";
    echo "<p>Run: <code>composer install</code></p>";
}

// Test 2: Mollie Class
echo "<h2>2. Mollie API Client Class</h2>";
if (class_exists('\Mollie\Api\MollieApiClient')) {
    echo "<p class='success'>✅ Mollie\Api\MollieApiClient class exists</p>";
} else {
    echo "<p class='error'>❌ Mollie\Api\MollieApiClient class not found!</p>";
    echo "<p>Run: <code>composer require mollie/mollie-api-php</code></p>";
}

// Test 3: .env File
echo "<h2>3. .env File</h2>";
$envPath = dirname(dirname(__DIR__)) . '/../../.env';
if (file_exists($envPath)) {
    echo "<p class='success'>✅ .env file found</p>";
    echo "<pre>Path: " . realpath($envPath) . "</pre>";
    
    // Try to read it
    $envContent = file_get_contents($envPath);
    $lines = explode("\n", $envContent);
    
    $foundApiKey = false;
    $foundProfileId = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, 'MOLLIE_TEST_API_KEY') !== false) {
            $foundApiKey = true;
            // Hide the actual key, just show first few chars
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $value = trim($value, '"\'');
                echo "<p class='success'>✅ MOLLIE_TEST_API_KEY found: " . substr($value, 0, 15) . "...</p>";
            }
        }
        if (strpos($line, 'PROFILE_ID') !== false) {
            $foundProfileId = true;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $value = trim($value, '"\'');
                echo "<p class='success'>✅ PROFILE_ID found: " . $value . "</p>";
            }
        }
    }
    
    if (!$foundApiKey) {
        echo "<p class='error'>❌ MOLLIE_TEST_API_KEY not found in .env</p>";
    }
    if (!$foundProfileId) {
        echo "<p class='error'>❌ PROFILE_ID not found in .env</p>";
    }
} else {
    echo "<p class='error'>❌ .env file not found!</p>";
    echo "<pre>Expected at: $envPath</pre>";
}

// Test 4: Our Mollie Class
echo "<h2>4. MolliePayment Class</h2>";
$molliePath = __DIR__ . '/../../../database/classes/mollie.php';
if (file_exists($molliePath)) {
    require_once $molliePath;
    echo "<p class='success'>✅ MolliePayment class file found</p>";
    
    if (class_exists('MolliePayment')) {
        echo "<p class='success'>✅ MolliePayment class loaded</p>";
        
        try {
            $mollie = new MolliePayment();
            echo "<p class='success'>✅ MolliePayment instantiated successfully</p>";
            
            if ($mollie->isConfigured()) {
                echo "<p class='success'>✅ Mollie is configured!</p>";
                echo "<pre>API Key (masked): " . $mollie->getApiKey() . "</pre>";
            } else {
                echo "<p class='error'>❌ Mollie is not configured</p>";
                echo "<p>Check your .env file has MOLLIE_TEST_API_KEY</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error creating MolliePayment: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>❌ MolliePayment class not found in file</p>";
    }
} else {
    echo "<p class='error'>❌ mollie.php file not found!</p>";
    echo "<pre>Expected at: $molliePath</pre>";
}

// Test 5: Database
echo "<h2>5. Database Connection</h2>";
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=glass_market", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Database connection successful</p>";
    
    // Check if mollie_payments table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'mollie_payments'")->fetchAll();
    if (count($tables) > 0) {
        echo "<p class='success'>✅ mollie_payments table exists</p>";
    } else {
        echo "<p class='error'>❌ mollie_payments table not found</p>";
        echo "<p>Run: <code>database/mollie_payments.sql</code> in phpMyAdmin</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Summary
echo "<h2>Summary</h2>";
if (class_exists('\Mollie\Api\MollieApiClient') && 
    class_exists('MolliePayment') && 
    file_exists($envPath)) {
    echo "<p class='success' style='font-size:20px;'>✅ Everything looks good! You can use the sandbox now.</p>";
    echo "<p><a href='sandbox.php' style='display:inline-block;padding:15px 30px;background:#2563eb;color:white;text-decoration:none;border-radius:8px;font-weight:600;'>Open Sandbox →</a></p>";
} else {
    echo "<p class='error' style='font-size:20px;'>❌ Some issues found. Fix the errors above.</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
