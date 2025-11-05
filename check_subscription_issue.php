<?php
session_start();

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

echo "<!DOCTYPE html><html><head><title>Subscription Debug</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "h2{color:#333;border-bottom:2px solid #3b82f6;padding-bottom:10px;}";
echo ".success{color:#16a34a;font-weight:bold;}";
echo ".error{color:#dc2626;font-weight:bold;}";
echo "pre{background:#f1f5f9;padding:15px;border-radius:6px;overflow-x:auto;}";
echo "</style></head><body>";

echo "<h1>🔍 Subscription Debug for Current User</h1>";

// Check session
echo "<div class='box'>";
echo "<h2>1. Session Information</h2>";
echo "Logged in: " . (isset($_SESSION['user_logged_in']) ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "<br>";
echo "User ID: " . ($_SESSION['user_id'] ?? '<span class="error">NULL</span>') . "<br>";
echo "User Name: " . ($_SESSION['user_name'] ?? '<span class="error">NULL</span>') . "<br>";
echo "User Email: " . ($_SESSION['user_email'] ?? '<span class="error">NULL</span>') . "<br>";
echo "</div>";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='box'>";
        echo "<h2>2. User Info from Database</h2>";
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<span class='error'>User not found!</span>";
        }
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>3. Checking for Subscriptions</h2>";
        $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Subscriptions found: <strong>" . count($subscriptions) . "</strong><br><br>";
        
        if (!empty($subscriptions)) {
            echo "<span class='success'>✅ Subscriptions exist!</span><br><br>";
            echo "<pre>";
            print_r($subscriptions);
            echo "</pre>";
        } else {
            echo "<span class='error'>❌ NO subscriptions found for user_id = $user_id</span><br><br>";
            
            // Check if table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'user_subscriptions'");
            if ($stmt->rowCount() == 0) {
                echo "<span class='error'>⚠️ user_subscriptions table does NOT exist!</span><br>";
            } else {
                echo "<span class='success'>✓ user_subscriptions table exists</span><br>";
                
                // Check all subscriptions
                $stmt = $pdo->query('SELECT * FROM user_subscriptions ORDER BY created_at DESC');
                $all_subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<br>Total subscriptions in database: <strong>" . count($all_subs) . "</strong><br>";
                
                if (count($all_subs) > 0) {
                    echo "<br>All subscriptions:<br><pre>";
                    foreach ($all_subs as $sub) {
                        echo "ID: {$sub['id']}, User ID: {$sub['user_id']}, Active: {$sub['is_active']}, Created: {$sub['created_at']}\n";
                    }
                    echo "</pre>";
                }
            }
        }
        echo "</div>";
        
        // Check subscription class
        echo "<div class='box'>";
        echo "<h2>4. Testing Subscription Creation</h2>";
        
        $sub_class_path = __DIR__ . '/database/classes/subscriptions.php';
        echo "Subscription class path: <code>$sub_class_path</code><br>";
        echo "File exists: " . (file_exists($sub_class_path) ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "<br>";
        
        if (file_exists($sub_class_path)) {
            require_once $sub_class_path;
            echo "Class loaded: " . (class_exists('Subscription') ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "<br>";
            
            if (class_exists('Subscription')) {
                echo "Method exists: " . (method_exists('Subscription', 'createTrialSubscription') ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "<br>";
            }
        }
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='box' style='background:#fee2e2;'>";
        echo "<h2>❌ Database Error</h2>";
        echo "<span class='error'>" . htmlspecialchars($e->getMessage()) . "</span>";
        echo "</div>";
    }
} else {
    echo "<div class='box' style='background:#fee2e2;'>";
    echo "<h2>❌ Not Logged In</h2>";
    echo "Please log in first.";
    echo "</div>";
}

echo "<br><a href='resources/views/profile.php?tab=subscription' style='display:inline-block;background:#3b82f6;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;'>Go to Profile Subscription Tab</a>";
echo "</body></html>";
?>
