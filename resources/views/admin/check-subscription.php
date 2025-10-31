<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    die('Please login first');
}

$user_id = $_SESSION['user_id'];

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get user subscription
    $stmt = $pdo->prepare("
        SELECT *,
            DATEDIFF(end_date, CURDATE()) as days_remaining,
            CASE
                WHEN end_date >= CURDATE() AND is_active = 1 THEN 'active'
                WHEN end_date < CURDATE() THEN 'expired'
                ELSE 'none'
            END as status
        FROM user_subscriptions
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute(['user_id' => $user_id]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get recent payments
    $stmt = $pdo->prepare("
        SELECT *
        FROM mollie_payments
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $user_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Subscription Status</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f3f4f6;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 { color: #111827; margin-top: 0; }
        h2 { color: #374151; font-size: 18px; margin-top: 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-expired { background: #fee2e2; color: #991b1b; }
        .badge-trial { background: #dbeafe; color: #1e40af; }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-right: 10px;
        }
        .btn:hover {
            background: #2563eb;
        }
        pre {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>📊 Subscription Status</h1>
        <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['user_email'] ?? 'N/A'; ?></p>
    </div>

    <div class="card">
        <h2>Current Subscription</h2>
        <?php if ($subscription): ?>
            <table>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-<?php echo $subscription['status']; ?>">
                            <?php echo strtoupper($subscription['status']); ?>
                        </span>
                        <?php if ($subscription['is_trial']): ?>
                            <span class="badge badge-trial">TRIAL</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td><?php echo $subscription['start_date']; ?></td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td><?php echo $subscription['end_date']; ?></td>
                </tr>
                <tr>
                    <th>Days Remaining</th>
                    <td><?php echo max(0, $subscription['days_remaining']); ?> days</td>
                </tr>
                <tr>
                    <th>Is Active</th>
                    <td><?php echo $subscription['is_active'] ? '✅ Yes' : '❌ No'; ?></td>
                </tr>
            </table>

            <h3 style="margin-top: 20px;">Raw Data:</h3>
            <pre><?php echo htmlspecialchars(json_encode($subscription, JSON_PRETTY_PRINT)); ?></pre>
        <?php else: ?>
            <div class="no-data">
                <p>❌ No subscription found</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Recent Payments</h2>
        <?php if (!empty($payments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Months</th>
                        <th>Created</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($payment['payment_id']); ?></code></td>
                            <td>€<?php echo number_format($payment['amount'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $payment['status']; ?>">
                                    <?php echo strtoupper($payment['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $payment['months']; ?> month(s)</td>
                            <td><?php echo date('Y-m-d H:i', strtotime($payment['created_at'])); ?></td>
                            <td><?php echo $payment['paid_at'] ? date('Y-m-d H:i', strtotime($payment['paid_at'])) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No payments found</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <a href="/glass-market/resources/views/pricing.php" class="btn">Go to Pricing</a>
        <a href="/glass-market/public/index.php" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
