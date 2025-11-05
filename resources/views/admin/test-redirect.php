<?php
/**
 * TEST PAGE: Debug Mollie Redirects
 * This page logs all parameters when Mollie redirects back
 */
session_start();

// Log everything
$log_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'GET' => $_GET,
    'POST' => $_POST,
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'N/A',
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'N/A',
];

error_log("=== MOLLIE REDIRECT TEST ===");
error_log(print_r($log_data, true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mollie Redirect Test</title>
    <style>
        body {
            font-family: monospace;
            padding: 40px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
        }
        pre {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .success {
            color: #4caf50;
            font-weight: bold;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Mollie Redirect Test Page</h1>

        <div class="info">
            <strong>Success!</strong> Mollie has redirected you to this page.
        </div>

        <h2>Received Parameters:</h2>
        <pre><?php echo htmlspecialchars(print_r($log_data, true)); ?></pre>

        <h2>Instructions:</h2>
        <ol>
            <li>Create a test payment using this redirect URL</li>
            <li>Complete, cancel, or let it expire on Mollie's checkout page</li>
            <li>Check if you get redirected back to this page</li>
            <li>Check the parameters received above</li>
        </ol>

        <h2>Next Steps:</h2>
        <p>If you see this page, Mollie IS redirecting correctly. The issue is likely in mollie-return.php logic.</p>
        <p>If you DON'T see this page after canceling/failing, then Mollie is not redirecting at all for those statuses.</p>

        <a href="dashboard.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 4px;">Back to Dashboard</a>
    </div>
</body>
</html>
