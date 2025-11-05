<?php
session_start();

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Subscription Debug Test</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }";
echo "h1 { color: #333; }";
echo "pre { background: white; padding: 20px; border-radius: 8px; overflow-x: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo ".success { color: #16a34a; font-weight: bold; }";
echo ".error { color: #dc2626; font-weight: bold; }";
echo ".warning { color: #f59e0b; font-weight: bold; }";
echo ".section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo "a { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 5px; }";
echo "a:hover { background: #2563eb; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 Subscription Debug Test</h1>";

// Check session
echo "<div class='section'>";
echo "<h2>SESSION INFO</h2>";
echo "<pre>";
echo "Logged in: " . (isset($_SESSION['user_logged_in']) ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? '<span class="error">NULL</span>') . "\n";
echo "User Name: " . ($_SESSION['user_name'] ?? '<span class="error">NULL</span>') . "\n";
echo "User Email: " . ($_SESSION['user_email'] ?? '<span class="error">NULL</span>') . "\n";
echo "</pre>";
echo "</div>";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='section'>";
        echo "<h2>DATABASE CONNECTION</h2>";
        echo "<pre><span class='success'>✅ Connected successfully</span></pre>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h2>USER INFO FROM DATABASE</h2>";
        echo "<pre>";
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo "<span class='success'>✅ User found</span>\n";
            print_r($user);
        } else {
            echo "<span class='error'>❌ User not found in database!</span>\n";
        }
        echo "</pre>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h2>SUBSCRIPTION QUERY FOR YOUR USER</h2>";
        echo "<pre>";
        $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "User ID queried: <strong>" . $user_id . "</strong>\n";
        echo "Number of subscriptions found: <strong>" . count($subscriptions) . "</strong>\n";
        echo "Subscriptions empty: " . (empty($subscriptions) ? "<span class='warning'>⚠️ YES</span>" : "<span class='success'>✅ NO</span>") . "\n\n";
        
        if (!empty($subscriptions)) {
            echo "<span class='success'>✅ SUBSCRIPTION DATA FOUND:</span>\n";
            echo "----------------------------------------\n";
            foreach ($subscriptions as $sub) {
                print_r($sub);
                echo "\n";
            }
        } else {
            echo "<span class='error'>❌ No subscriptions found for user_id = " . $user_id . "</span>\n";
        }
        echo "</pre>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h2>ALL SUBSCRIPTIONS IN DATABASE</h2>";
        echo "<pre>";
        $stmt = $pdo->query('SELECT * FROM user_subscriptions ORDER BY created_at DESC');
        $all_subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total subscriptions in database: <strong>" . count($all_subs) . "</strong>\n\n";
        if (count($all_subs) > 0) {
            echo "List of all subscriptions:\n";
            echo "----------------------------------------\n";
            foreach ($all_subs as $sub) {
                $match = ($sub['user_id'] == $user_id) ? " <-- <span class='success'>THIS IS YOU!</span>" : "";
                echo "Subscription ID: {$sub['id']}, User ID: {$sub['user_id']}, Active: {$sub['is_active']}, Start: {$sub['start_date']}, End: {$sub['end_date']}{$match}\n";
            }
        } else {
            echo "<span class='warning'>⚠️ No subscriptions exist in the database at all.</span>\n";
        }
        echo "</pre>";
        echo "</div>";
        
        // DIAGNOSIS
        echo "<div class='section' style='background: #eff6ff; border: 2px solid #3b82f6;'>";
        echo "<h2>🔧 DIAGNOSIS</h2>";
        echo "<pre style='background: transparent;'>";
        if (empty($subscriptions) && count($all_subs) > 0) {
            echo "<span class='warning'>⚠️ ISSUE FOUND:</span>\n";
            echo "Your user ID (<strong>{$user_id}</strong>) doesn't have any subscriptions.\n";
            echo "But there are subscriptions for other users in the database.\n\n";
            echo "<strong>Possible reasons:</strong>\n";
            echo "1. You're logged in as a different user than expected\n";
            echo "2. The subscription was created for a different user ID\n";
            echo "3. You need to create a new subscription for this user\n";
        } elseif (!empty($subscriptions)) {
            echo "<span class='success'>✅ EVERYTHING LOOKS GOOD!</span>\n";
            echo "You have " . count($subscriptions) . " subscription(s).\n";
            echo "They should appear on your profile page.\n\n";
            echo "If they don't appear:\n";
            echo "1. Clear your browser cache\n";
            echo "2. Check the browser console for JavaScript errors\n";
            echo "3. Make sure you're on the 'Subscription' tab in your profile\n";
        } else {
            echo "<span class='warning'>⚠️ NO SUBSCRIPTIONS EXIST:</span>\n";
            echo "There are no subscriptions in the database at all.\n";
            echo "You need to create a subscription first.\n";
        }
        echo "</pre>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='section' style='background: #fee2e2; border: 2px solid #dc2626;'>";
        echo "<h2>❌ DATABASE ERROR</h2>";
        echo "<pre style='background: transparent;'>";
        echo "<span class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
        echo "</pre>";
        echo "</div>";
    }
} else {
    echo "<div class='section' style='background: #fee2e2; border: 2px solid #dc2626;'>";
    echo "<h2>❌ NOT LOGGED IN</h2>";
    echo "<pre style='background: transparent;'>";
    echo "<span class='error'>You are not logged in. Please log in first to check your subscriptions.</span>\n";
    echo "</pre>";
    echo "</div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>";
echo '<a href="resources/views/profile.php">📄 Go to Profile Page</a>';
echo '<a href="resources/views/login.php">🔐 Go to Login</a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '">🔄 Refresh This Page</a>';
echo "</div>";

echo "</body>";
echo "</html>";
?>
