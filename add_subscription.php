<?php
session_start();

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

echo "<!DOCTYPE html><html><head><title>Add Subscription</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;max-width:800px;margin:0 auto;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "h1,h2{color:#333;}";
echo ".success{color:#16a34a;font-weight:bold;}";
echo ".error{color:#dc2626;font-weight:bold;}";
echo ".btn{display:inline-block;background:#3b82f6;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;margin:10px 5px 10px 0;}";
echo ".btn:hover{background:#2563eb;}";
echo "</style></head><body>";

echo "<h1>🎯 Add Subscription to Current User</h1>";

if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['user_id'])) {
    echo "<div class='box' style='background:#fee2e2;'>";
    echo "<p class='error'>❌ You must be logged in to use this tool.</p>";
    echo "<a href='resources/views/login.php' class='btn'>Login</a>";
    echo "</div></body></html>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Unknown';
$user_email = $_SESSION['user_email'] ?? 'Unknown';

echo "<div class='box'>";
echo "<h2>Current User</h2>";
echo "User ID: <strong>$user_id</strong><br>";
echo "Name: <strong>$user_name</strong><br>";
echo "Email: <strong>$user_email</strong><br>";
echo "</div>";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check existing subscriptions
    echo "<div class='box'>";
    echo "<h2>Existing Subscriptions</h2>";
    $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute(['user_id' => $user_id]);
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($existing) > 0) {
        echo "<p class='success'>✅ You already have " . count($existing) . " subscription(s):</p>";
        echo "<pre style='background:#f1f5f9;padding:15px;border-radius:6px;overflow-x:auto;'>";
        foreach ($existing as $sub) {
            echo "ID: {$sub['id']}, Start: {$sub['start_date']}, End: {$sub['end_date']}, Active: {$sub['is_active']}, Trial: {$sub['is_trial']}\n";
        }
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ No subscriptions found</p>";
    }
    echo "</div>";
    
    // Add subscription if POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subscription'])) {
        echo "<div class='box' style='background:#eff6ff;border:2px solid #3b82f6;'>";
        echo "<h2>🔄 Creating Subscription...</h2>";
        
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+3 months'));
        
        $stmt = $pdo->prepare("
            INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active, created_at)
            VALUES (:user_id, :start_date, :end_date, 1, 1, NOW())
        ");
        
        $result = $stmt->execute([
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
        
        if ($result) {
            echo "<p class='success'>✅ SUCCESS! Trial subscription created!</p>";
            echo "<p>Start Date: <strong>$start_date</strong></p>";
            echo "<p>End Date: <strong>$end_date</strong></p>";
            echo "<p>Duration: <strong>3 months</strong></p>";
            echo "<br><a href='resources/views/profile.php?tab=subscription' class='btn'>View in Profile</a>";
        } else {
            echo "<p class='error'>❌ Failed to create subscription</p>";
        }
        echo "</div>";
    } else {
        // Show form to add subscription
        echo "<div class='box' style='background:#f0fdf4;border:2px solid #16a34a;'>";
        echo "<h2>➕ Add 3-Month Trial Subscription</h2>";
        echo "<p>Click the button below to add a free 3-month trial subscription to your account.</p>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='add_subscription' class='btn' style='background:#16a34a;'>Add Trial Subscription</button>";
        echo "</form>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='box' style='background:#fee2e2;'>";
    echo "<h2>❌ Database Error</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<br><div class='box' style='text-align:center;background:#fafafa;'>";
echo "<a href='check_subscription_issue.php' class='btn' style='background:#64748b;'>🔍 Debug Tool</a>";
echo "<a href='resources/views/profile.php?tab=subscription' class='btn'>👤 View Profile</a>";
echo "<a href='resources/views/logout.php' class='btn' style='background:#dc2626;'>🚪 Logout</a>";
echo "</div>";

echo "</body></html>";
?>
