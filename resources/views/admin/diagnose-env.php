<?php
/**
 * .env File Diagnostic Tool
 * Shows exact content and helps identify parsing issues
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>.env Diagnostic</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;}h2{color:#333;border-bottom:2px solid #333;padding-bottom:10px;}pre{background:#fff;padding:15px;border:1px solid #ddd;border-radius:5px;overflow-x:auto;}table{background:#fff;border-collapse:collapse;width:100%;margin:20px 0;}td,th{padding:10px;border:1px solid #ddd;text-align:left;}th{background:#333;color:#fff;}.success{color:green;}.error{color:red;}.warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>🔍 .env File Diagnostic</h1>";

$envPath = dirname(dirname(dirname(__DIR__))) . '/.env';
echo "<h2>File Location</h2>";
echo "<p><strong>Looking at:</strong> <code>" . htmlspecialchars($envPath) . "</code></p>";
echo "<p><strong>Exists:</strong> " . (file_exists($envPath) ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "</p>";

if (file_exists($envPath)) {
    echo "<p><strong>Real Path:</strong> <code>" . htmlspecialchars(realpath($envPath)) . "</code></p>";
    
    $content = file_get_contents($envPath);
    $lines = explode("\n", $content);
    
    echo "<h2>File Statistics</h2>";
    echo "<ul>";
    echo "<li><strong>File Size:</strong> " . strlen($content) . " bytes</li>";
    echo "<li><strong>Total Lines:</strong> " . count($lines) . "</li>";
    echo "</ul>";
    
    echo "<h2>Mollie-Related Lines</h2>";
    echo "<table>";
    echo "<tr><th>#</th><th>Line Content</th><th>Status</th></tr>";
    
    $foundMollieKey = false;
    $foundProfileId = false;
    
    foreach ($lines as $num => $line) {
        $lineNum = $num + 1;
        $trimmed = trim($line);
        
        // Check if line contains MOLLIE or PROFILE
        if (stripos($line, 'MOLLIE') !== false || stripos($line, 'PROFILE') !== false) {
            echo "<tr>";
            echo "<td>$lineNum</td>";
            echo "<td><pre style='margin:0;'>" . htmlspecialchars($line) . "</pre></td>";
            
            // Check if it's a valid key=value line
            if (strpos($trimmed, '=') !== false && strpos($trimmed, '#') !== 0) {
                list($key, $value) = explode('=', $trimmed, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                if ($key === 'MOLLIE_TEST_API_KEY') {
                    $foundMollieKey = true;
                    echo "<td class='success'>✅ Found! Value: " . htmlspecialchars(substr($value, 0, 15)) . "...</td>";
                } elseif ($key === 'PROFILE_ID') {
                    $foundProfileId = true;
                    echo "<td class='success'>✅ Found! Value: " . htmlspecialchars($value) . "</td>";
                } else {
                    echo "<td class='warning'>⚠️ Key name: '$key' (not exact match)</td>";
                }
            } else {
                echo "<td class='error'>❌ Not a valid KEY=VALUE line</td>";
            }
            echo "</tr>";
        }
    }
    
    echo "</table>";
    
    echo "<h2>Search Results</h2>";
    echo "<ul>";
    echo "<li><strong>MOLLIE_TEST_API_KEY:</strong> " . ($foundMollieKey ? "<span class='success'>✅ Found</span>" : "<span class='error'>❌ Not Found</span>") . "</li>";
    echo "<li><strong>PROFILE_ID:</strong> " . ($foundProfileId ? "<span class='success'>✅ Found</span>" : "<span class='error'>❌ Not Found</span>") . "</li>";
    echo "</ul>";
    
    if (!$foundMollieKey || !$foundProfileId) {
        echo "<div style='background:#fff3cd;padding:20px;border:2px solid #ffc107;border-radius:8px;margin:20px 0;'>";
        echo "<h3 style='margin-top:0;color:#856404;'>⚠️ Keys Not Found - Possible Issues:</h3>";
        echo "<ol style='color:#856404;'>";
        echo "<li><strong>Typo in key name:</strong> Make sure it's exactly <code>MOLLIE_TEST_API_KEY</code> (not MOLLIE_TEST_API_KE)</li>";
        echo "<li><strong>Wrong key name:</strong> Should be <code>MOLLIE_TEST_API_KEY</code> not <code>MOLLIE_API_KEY</code></li>";
        echo "<li><strong>Commented out:</strong> Make sure line doesn't start with #</li>";
        echo "<li><strong>Extra spaces:</strong> No spaces around the = sign</li>";
        echo "</ol>";
        echo "<h4>Expected Format:</h4>";
        echo "<pre>MOLLIE_TEST_API_KEY=\"test_DPnkq9mH3BgmWfJQJVwBpF9MjySf5F\"\nPROFILE_ID=\"pfl_2u72EdoaP6\"</pre>";
        echo "</div>";
    }
    
    echo "<h2>Full File Content (first 50 lines)</h2>";
    echo "<pre>";
    for ($i = 0; $i < min(50, count($lines)); $i++) {
        $lineNum = str_pad($i + 1, 3, ' ', STR_PAD_LEFT);
        echo "$lineNum | " . htmlspecialchars($lines[$i]) . "\n";
    }
    if (count($lines) > 50) {
        echo "\n... (" . (count($lines) - 50) . " more lines)\n";
    }
    echo "</pre>";
    
    // Show what the parser would extract
    echo "<h2>What the Parser Found</h2>";
    echo "<table>";
    echo "<tr><th>Key</th><th>Value (first 50 chars)</th></tr>";
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        if (empty($trimmed) || strpos($trimmed, '#') === 0) {
            continue;
        }
        
        if (strpos($trimmed, '=') !== false) {
            list($key, $value) = explode('=', $trimmed, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Only show Mollie and Profile related
            if (stripos($key, 'MOLLIE') !== false || stripos($key, 'PROFILE') !== false) {
                echo "<tr>";
                echo "<td><code>" . htmlspecialchars($key) . "</code></td>";
                echo "<td><code>" . htmlspecialchars(substr($value, 0, 50)) . (strlen($value) > 50 ? '...' : '') . "</code></td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
    
} else {
    echo "<p class='error'>❌ File not found! Create .env file in project root.</p>";
}

echo "<hr><p><a href='sandbox.php'>← Back to Sandbox</a> | <a href='test-mollie.php'>Test Mollie Integration</a></p>";
echo "</body></html>";
