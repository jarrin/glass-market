<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug - Glass Market</title>
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
        h2 { color: #374151; font-size: 18px; margin-top: 20px; }
        pre {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
        }
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }
        .status-yes { background: #dcfce7; color: #166534; }
        .status-no { background: #fee2e2; color: #991b1b; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-right: 10px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #2563eb;
        }
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
    </style>
</head>
<body>
    <div class="card">
        <h1>🔍 Session Debug Information</h1>

        <h2>Session Status</h2>
        <p>
            Session Active:
            <span class="status <?php echo session_status() === PHP_SESSION_ACTIVE ? 'status-yes' : 'status-no'; ?>">
                <?php echo session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO'; ?>
            </span>
        </p>
        <p>Session ID: <code><?php echo session_id(); ?></code></p>

        <h2>Login Status Check</h2>
        <?php
        $is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
        $user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
        $is_admin = $is_logged_in ? ($_SESSION['is_admin'] ?? 0) : 0;
        ?>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><code>$is_logged_in</code></td>
                <td><?php echo $is_logged_in ? 'true' : 'false'; ?></td>
                <td>
                    <span class="status <?php echo $is_logged_in ? 'status-yes' : 'status-no'; ?>">
                        <?php echo $is_logged_in ? 'LOGGED IN' : 'NOT LOGGED IN'; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><code>$user_name</code></td>
                <td><?php echo htmlspecialchars($user_name); ?></td>
                <td><?php echo !empty($user_name) ? '✅' : '❌'; ?></td>
            </tr>
            <tr>
                <td><code>$is_admin</code></td>
                <td><?php echo $is_admin; ?></td>
                <td><?php echo $is_admin == 1 ? '👑 Admin' : '👤 User'; ?></td>
            </tr>
        </table>

        <h2>What Navbar Should Show:</h2>
        <?php if ($is_logged_in): ?>
            <p>✅ <strong>User name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
            <p>✅ <strong>Logout button</strong> (NOT Login/Register)</p>
            <?php if ($is_admin == 1): ?>
                <p>✅ <strong>Admin button</strong></p>
            <?php else: ?>
                <p>❌ No Admin button (not an admin)</p>
            <?php endif; ?>
        <?php else: ?>
            <p>❌ <strong>Login and Register buttons</strong></p>
            <p>❌ No user name shown</p>
        <?php endif; ?>

        <h2>All Session Variables</h2>
        <pre><?php print_r($_SESSION); ?></pre>

        <h2>Session Data (Raw)</h2>
        <pre><?php var_export($_SESSION); ?></pre>

        <a href="/glass-market/public/index.php" class="btn">Go to Homepage</a>
        <a href="/glass-market/resources/views/browse.php" class="btn">Test Browse Page</a>
        <a href="/glass-market/resources/views/login.php" class="btn">Login Page</a>
    </div>

    <div class="card">
        <h2>💡 Troubleshooting</h2>
        <p><strong>If you're logged in but navbar shows Login/Register:</strong></p>
        <ol>
            <li><strong>Clear browser cache:</strong> Press Ctrl + Shift + R (Windows) or Cmd + Shift + R (Mac)</li>
            <li><strong>Try incognito mode:</strong> Open an incognito/private window</li>
            <li><strong>Check session values:</strong> Make sure "user_logged_in" shows "true" above</li>
            <li><strong>Re-login:</strong> Logout and login again to refresh session</li>
        </ol>
    </div>
</body>
</html>
