<?php
session_start();
require_once __DIR__ . '/../../../includes/admin-guard.php';
require_once __DIR__ . '/../../../config.php';

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    // Filter by user
    $user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

    // Build query
    $where = '';
    $params = [];

    if ($user_filter) {
        $where = 'WHERE pe.user_id = :user_id';
        $params['user_id'] = $user_filter;
    }

    // Get total count
    $count_query = "SELECT COUNT(*) FROM payment_errors pe $where";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_errors = $stmt->fetchColumn();

    // Get errors with user info
    $query = "
        SELECT
            pe.*,
            u.email as user_email,
            u.name as user_name
        FROM payment_errors pe
        LEFT JOIN users u ON pe.user_id = u.id
        $where
        ORDER BY pe.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $errors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_pages = ceil($total_errors / $per_page);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Errors - Glass Market Admin</title>
    <link rel="stylesheet" href="css/manage-users-header.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f7;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #1d1d1f;
        }

        .page-header p {
            font-size: 16px;
            color: #6e6e73;
            margin: 0;
        }

        /* Stats - Apple-like cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.06);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }

        .stat-card h3 {
            font-size: 13px;
            color: #86868b;
            margin: 0 0 12px 0;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .stat-card .value {
            font-size: 40px;
            font-weight: 700;
            color: #dc2626;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        /* Apple-like table */
        .errors-table {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
        }

        .errors-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .errors-table th {
            background: #f5f5f7;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: #1d1d1f;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            font-size: 13px;
            letter-spacing: -0.01em;
        }

        .errors-table td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            font-size: 14px;
            color: #1d1d1f;
        }

        .errors-table tr:hover {
            background: rgba(0,0,0,0.02);
        }

        .error-row {
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .error-detail {
            display: none;
            background: #fef2f2;
            padding: 20px;
            border-left: 4px solid #dc2626;
        }

        .error-detail.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-detail h4 {
            margin: 0 0 12px 0;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
        }

        .error-detail pre {
            background: white;
            padding: 16px;
            border-radius: 12px;
            overflow-x: auto;
            font-size: 12px;
            margin: 8px 0;
            border: 1px solid rgba(0,0,0,0.08);
        }

        /* Apple-like badges */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .badge-trial { background: #e8f4fd; color: #0071e3; }
        .badge-monthly { background: #d6f5e0; color: #0d6832; }
        .badge-annual { background: #fff4d6; color: #8e6a00; }

        /* Apple-like pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 32px;
            padding: 20px 0;
        }

        .pagination a,
        .pagination span {
            padding: 10px 16px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            color: #1d1d1f;
            font-weight: 500;
            font-size: 14px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: #f5f5f7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .pagination .current {
            background: #0071e3;
            color: white;
        }

        /* Apple-like empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state svg {
            width: 72px;
            height: 72px;
            margin-bottom: 20px;
            color: #06c;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #1d1d1f;
        }

        .empty-state p {
            font-size: 16px;
            color: #86868b;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Header matching manage-users.php -->
    <div class="header">
        <div class="header-content">
            <h1>Payment Errors - Glass Market</h1>
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>Payment Errors</h1>
            <p>Monitor and troubleshoot failed payment attempts</p>
        </div>

        <?php
        // Get statistics
        $stmt = $pdo->query("SELECT COUNT(*) FROM payment_errors WHERE DATE(created_at) = CURDATE()");
        $today_errors = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM payment_errors WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $week_errors = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM payment_errors");
        $affected_users = $stmt->fetchColumn();
        ?>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Errors</h3>
                <div class="value"><?php echo number_format($total_errors); ?></div>
            </div>
            <div class="stat-card">
                <h3>Today</h3>
                <div class="value"><?php echo number_format($today_errors); ?></div>
            </div>
            <div class="stat-card">
                <h3>Last 7 Days</h3>
                <div class="value"><?php echo number_format($week_errors); ?></div>
            </div>
            <div class="stat-card">
                <h3>Affected Users</h3>
                <div class="value"><?php echo number_format($affected_users); ?></div>
            </div>
        </div>

        <?php if (empty($errors)): ?>
            <div class="errors-table">
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3>No Payment Errors</h3>
                    <p>All payment attempts are processing successfully!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="errors-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Error</th>
                            <th>Payment ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($errors as $error): ?>
                            <tr class="error-row" onclick="toggleDetail(<?php echo $error['id']; ?>)">
                                <td><?php echo date('M d, Y H:i', strtotime($error['created_at'])); ?></td>
                                <td>
                                    <?php if ($error['user_name']): ?>
                                        <strong><?php echo htmlspecialchars($error['user_name']); ?></strong><br>
                                        <small style="color: #6b7280;"><?php echo htmlspecialchars($error['user_email']); ?></small>
                                    <?php else: ?>
                                        <em style="color: #9ca3af;">Unknown User</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($error['plan']): ?>
                                        <span class="badge badge-<?php echo strtolower($error['plan']); ?>">
                                            <?php echo ucfirst($error['plan']); ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($error['amount']): ?>
                                        €<?php echo number_format($error['amount'], 2); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars(substr($error['error_message'], 0, 100)); ?>
                                    <?php if (strlen($error['error_message']) > 100): ?>...<?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($error['payment_id']): ?>
                                        <code style="font-size: 11px;"><?php echo htmlspecialchars($error['payment_id']); ?></code>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding: 0;">
                                    <div class="error-detail" id="detail-<?php echo $error['id']; ?>">
                                        <h4>Full Error Message</h4>
                                        <pre><?php echo htmlspecialchars($error['error_message']); ?></pre>

                                        <?php if ($error['error_context']): ?>
                                            <h4>Context</h4>
                                            <pre><?php echo htmlspecialchars(json_encode(json_decode($error['error_context']), JSON_PRETTY_PRINT)); ?></pre>
                                        <?php endif; ?>

                                        <?php if ($error['request_data']): ?>
                                            <h4>Request Data</h4>
                                            <pre><?php echo htmlspecialchars(json_encode(json_decode($error['request_data']), JSON_PRETTY_PRINT)); ?></pre>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1); ?><?php echo $user_filter ? "&user_id=$user_filter" : ''; ?>">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php echo $user_filter ? "&user_id=$user_filter" : ''; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo ($page + 1); ?><?php echo $user_filter ? "&user_id=$user_filter" : ''; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetail(id) {
            const detail = document.getElementById('detail-' + id);
            detail.classList.toggle('show');
        }
    </script>
</body>
</html>
