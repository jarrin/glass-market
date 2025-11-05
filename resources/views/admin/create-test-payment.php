<?php
/**
 * Create a test payment with custom redirect URL
 */
session_start();
require_once __DIR__ . '/../../../includes/admin-guard.php';
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../database/classes/mollie.php';

$mollie = new MolliePayment();
$user_id = $_SESSION['user_id'] ?? 1;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect_page = $_POST['redirect_page'] ?? 'mollie-return.php';
    $amount = floatval($_POST['amount'] ?? 9.99);
    $description = $_POST['description'] ?? 'Test Payment';

    $redirectUrl = "http://localhost/glass-market/resources/views/admin/{$redirect_page}?user_id={$user_id}";

    try {
        $payment = $mollie->createPayment($amount, $description, $redirectUrl, null, [
            'user_id' => $user_id,
            'test' => true
        ]);

        if (is_array($payment) && isset($payment['error'])) {
            $error = $payment['error'];
        } elseif ($payment) {
            // Save to database
            $stmt = $pdo->prepare("
                INSERT INTO mollie_payments (user_id, payment_id, amount, status, months, created_at)
                VALUES (:user_id, :payment_id, :amount, :status, 1, NOW())
            ");
            $stmt->execute([
                'user_id' => $user_id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'status' => $payment->status,
            ]);

            // Redirect to Mollie checkout
            header('Location: ' . $payment->getCheckoutUrl());
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Test Payment</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
    <style>
        body {
            background: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h1 {
            margin-bottom: 8px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #5568d3;
        }

        .error {
            background: #ffebee;
            border: 2px solid #f44336;
            padding: 16px;
            border-radius: 8px;
            color: #c62828;
            margin-bottom: 20px;
        }

        .info {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Create Test Payment</h1>
        <p class="subtitle">Create a test Mollie payment to debug redirect behavior</p>

        <?php if (isset($error)): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>📋 Testing Instructions:</strong>
            <ol style="margin: 10px 0 0 20px; line-height: 1.8;">
                <li>Choose where Mollie should redirect after payment</li>
                <li>Click "Create Payment" to go to Mollie checkout</li>
                <li>On Mollie's page, try different actions:
                    <ul style="margin-left: 20px;">
                        <li>✅ Pay successfully (use test card 4111 1111 1111 1111)</li>
                        <li>❌ Click "Cancel" button</li>
                        <li>⏰ Let it sit until it expires</li>
                    </ul>
                </li>
                <li>See where you get redirected and what data is passed</li>
            </ol>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="redirect_page">Redirect To:</label>
                <select name="redirect_page" id="redirect_page">
                    <option value="mollie-return.php">mollie-return.php (Production)</option>
                    <option value="test-redirect.php" selected>test-redirect.php (Debug)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="amount">Amount (EUR):</label>
                <input type="number" name="amount" id="amount" value="9.99" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" name="description" id="description" value="Test Payment - Debug Redirects" required>
            </div>

            <button type="submit">🚀 Create Payment & Go to Mollie</button>
        </form>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
