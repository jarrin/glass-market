<?php
/**
 * Admin Payment History
 * View all Mollie payments with detailed status information
 */
session_start();
require_once __DIR__ . '/../../../includes/admin-guard.php';
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../database/classes/mollie.php';

$mollie = new MolliePayment();

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$user_filter = $_GET['user_id'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query
$where_clauses = [];
$params = [];

if ($status_filter !== 'all') {
    $where_clauses[] = "mp.status = :status";
    $params['status'] = $status_filter;
}

if (!empty($user_filter)) {
    $where_clauses[] = "mp.user_id = :user_id";
    $params['user_id'] = $user_filter;
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM mollie_payments mp $where_sql";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_count / $per_page);

// Get payments
$sql = "
    SELECT
        mp.*,
        u.email,
        u.name as user_name
    FROM mollie_payments mp
    LEFT JOIN users u ON mp.user_id = u.id
    $where_sql
    ORDER BY mp.created_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_sql = "
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_count,
        SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_count,
        SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_revenue
    FROM mollie_payments
";
$stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);

// Sync status with Mollie for open/pending payments if requested
if (isset($_GET['sync']) && $_GET['sync'] === '1') {
    $sync_sql = "SELECT payment_id FROM mollie_payments WHERE status IN ('open', 'pending') LIMIT 10";
    $sync_payments = $pdo->query($sync_sql)->fetchAll(PDO::FETCH_COLUMN);

    foreach ($sync_payments as $payment_id) {
        try {
            $payment = $mollie->getPayment($payment_id);
            if ($payment) {
                $new_status = $payment->status;
                $update_sql = "UPDATE mollie_payments SET status = :status, updated_at = NOW() WHERE payment_id = :payment_id";
                $stmt = $pdo->prepare($update_sql);
                $stmt->execute(['status' => $new_status, 'payment_id' => $payment_id]);
            }
        } catch (Exception $e) {
            error_log("Failed to sync payment $payment_id: " . $e->getMessage());
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query(['status' => $status_filter, 'user_id' => $user_filter]));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History - Admin</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            background: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .stat-card.revenue .stat-value {
            color: #4caf50;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        select, input[type="text"] {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            font-weight: 600;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid { background: #d4edda; color: #155724; }
        .status-open { background: #cce5ff; color: #004085; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-failed { background: #f8d7da; color: #721c24; }
        .status-canceled { background: #e2e3e5; color: #383d41; }
        .status-expired { background: #f5c6cb; color: #721c24; }

        .payment-id {
            font-family: monospace;
            font-size: 13px;
            color: #667eea;
            cursor: pointer;
        }

        .payment-id:hover {
            text-decoration: underline;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .user-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .user-email {
            font-size: 13px;
            color: #666;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 24px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 600;
        }

        .pagination a:hover {
            background: #f8f9fa;
        }

        .pagination .active {
            background: #667eea;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            padding: 32px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .close-modal {
            font-size: 28px;
            color: #999;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close-modal:hover {
            color: #666;
        }

        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            flex: 0 0 180px;
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            flex: 1;
            color: #1a1a1a;
            word-break: break-all;
        }

        .detail-value code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
        }

        .status-checks {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #e0e0e0;
        }

        .status-checks h4 {
            font-size: 16px;
            margin-bottom: 12px;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../../includes/navbar.php'; ?>

    <div class="admin-container">
        <div class="page-header">
            <h1 class="page-title">💳 Payment History</h1>
            <a href="<?php echo VIEWS_URL; ?>/admin/dashboard.php" class="back-link">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Successful</div>
                <div class="stat-value" style="color: #4caf50;"><?php echo number_format($stats['paid_count']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Open/Pending</div>
                <div class="stat-value" style="color: #2196f3;"><?php echo number_format($stats['open_count']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Failed</div>
                <div class="stat-value" style="color: #f44336;"><?php echo number_format($stats['failed_count']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Canceled</div>
                <div class="stat-value" style="color: #9e9e9e;"><?php echo number_format($stats['canceled_count']); ?></div>
            </div>
            <div class="stat-card revenue">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">€<?php echo number_format($stats['total_revenue'], 2); ?></div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">All Payments (<?php echo number_format($total_count); ?>)</h2>

                <div class="filters">
                    <form method="GET" style="display: contents;">
                        <div class="filter-group">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                                <option value="paid" <?php echo $status_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Open</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                <option value="canceled" <?php echo $status_filter === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                <option value="expired" <?php echo $status_filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                            </select>
                        </div>

                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_filter); ?>">
                    </form>

                    <a href="?sync=1&status=<?php echo urlencode($status_filter); ?>&user_id=<?php echo urlencode($user_filter); ?>" class="btn btn-secondary">
                        🔄 Sync Open Payments
                    </a>
                </div>
            </div>

            <?php if (!empty($payments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td>
                            <span class="payment-id" onclick="showPaymentDetails('<?php echo htmlspecialchars($payment['payment_id']); ?>')">
                                <?php echo htmlspecialchars($payment['payment_id']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name"><?php echo htmlspecialchars($payment['user_name'] ?? 'Unknown'); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($payment['email'] ?? 'N/A'); ?></div>
                            </div>
                        </td>
                        <td>€<?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo $payment['months']; ?> month<?php echo $payment['months'] > 1 ? 's' : ''; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $payment['status']; ?>">
                                <?php echo strtoupper($payment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                        <td><?php echo $payment['paid_at'] ? date('M d, Y H:i', strtotime($payment['paid_at'])) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?status=<?php echo urlencode($status_filter); ?>&user_id=<?php echo urlencode($user_filter); ?>&page=<?php echo $page - 1; ?>">← Previous</a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?status=<?php echo urlencode($status_filter); ?>&user_id=<?php echo urlencode($user_filter); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?status=<?php echo urlencode($status_filter); ?>&user_id=<?php echo urlencode($user_filter); ?>&page=<?php echo $page + 1; ?>">Next →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3>No payments found</h3>
                <p>Try adjusting your filters or check back later.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Payment Details</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody">
                <p style="text-align: center; color: #999;">Loading...</p>
            </div>
        </div>
    </div>

    <script>
        function showPaymentDetails(paymentId) {
            const modal = document.getElementById('paymentModal');
            const modalBody = document.getElementById('modalBody');

            modal.classList.add('active');
            modalBody.innerHTML = '<p style="text-align: center; color: #999;">Loading payment details...</p>';

            // Fetch payment details via AJAX
            fetch('<?php echo VIEWS_URL; ?>/admin/get-payment-details.php?payment_id=' + encodeURIComponent(paymentId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalBody.innerHTML = formatPaymentDetails(data.payment);
                    } else {
                        modalBody.innerHTML = '<p style="color: #f44336;">Error: ' + data.error + '</p>';
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = '<p style="color: #f44336;">Failed to load payment details</p>';
                });
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function formatPaymentDetails(payment) {
            let html = '<div>';

            // Basic details
            html += '<div class="detail-row"><span class="detail-label">Payment ID</span><span class="detail-value"><code>' + payment.payment_id + '</code></span></div>';
            html += '<div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="status-badge status-' + payment.status + '">' + payment.status.toUpperCase() + '</span></span></div>';
            html += '<div class="detail-row"><span class="detail-label">Amount</span><span class="detail-value">€' + payment.amount + ' ' + payment.currency + '</span></div>';
            html += '<div class="detail-row"><span class="detail-label">Description</span><span class="detail-value">' + payment.description + '</span></div>';
            html += '<div class="detail-row"><span class="detail-label">Created At</span><span class="detail-value">' + payment.created_at + '</span></div>';

            if (payment.paid_at) {
                html += '<div class="detail-row"><span class="detail-label">Paid At</span><span class="detail-value">' + payment.paid_at + '</span></div>';
            }

            if (payment.expires_at) {
                html += '<div class="detail-row"><span class="detail-label">Expires At</span><span class="detail-value">' + payment.expires_at + '</span></div>';
            }

            // Status checks
            if (payment.status_checks) {
                html += '<div class="status-checks"><h4>Status Check Methods</h4>';
                for (let method in payment.status_checks) {
                    const result = payment.status_checks[method];
                    html += '<div class="detail-row"><span class="detail-label">' + method + '()</span><span class="detail-value">' + (result ? '✅ true' : '❌ false') + '</span></div>';
                }
                html += '</div>';
            }

            html += '</div>';
            return html;
        }

        // Close modal when clicking outside
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
