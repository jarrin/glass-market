<?php
// User Data Loader
// Loads user data, company data, listings count, subscriptions, and cards
// Sets: $user, $company, $user_listings_count, $user_subscriptions, $user_cards

// Initialize variables
$user = [
    'id' => $_SESSION['user_id'] ?? null,
    'name' => $_SESSION['user_name'] ?? 'User',
    'email' => $_SESSION['user_email'] ?? '',
    'avatar' => $_SESSION['user_avatar'] ?? '',
    'company_name' => '',
    'created_at' => null,
];

$user_listings_count = 0;
$company = null;
$user_subscriptions = [];
$user_cards = [];
$subscription_error = null;

if (!$user['id']) {
    return;
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load user data
    $stmt = $pdo->prepare('SELECT id, name, company_name, email, avatar, created_at, company_id FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $user['id']]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dbUser) {
        $user = array_merge($user, $dbUser);
        
        // Get company data if user has a company
        if (!empty($dbUser['company_id'])) {
            $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = :company_id LIMIT 1');
            $stmt->execute(['company_id' => $dbUser['company_id']]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Get user's listings count
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as count
        FROM listings l
        WHERE l.user_id = :user_id OR l.company_id = :company_id
    ');
    $stmt->execute([
        'user_id' => $user['id'],
        'company_id' => $dbUser['company_id'] ?? 0
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_listings_count = $result['count'] ?? 0;
    
    // Load user's subscriptions
    try {
        $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user['id']]);
        $user_subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $subscription_error = $e->getMessage();
        error_log("Subscription load error: " . $e->getMessage());
    }
    
    // Load user's credit cards
    try {
        $stmt = $pdo->prepare('SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC');
        $stmt->execute(['user_id' => $user['id']]);
        $user_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table might not exist yet
    }
    
} catch (PDOException $e) {
    // Silently continue with session data if DB fails
    error_log("User data load error: " . $e->getMessage());
}
