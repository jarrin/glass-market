<?php
/**
 * TEST ONLY: Instantly expire subscription for testing
 * Sets subscription end_date to yesterday
 */
session_start();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expire_subscription_test'])) {
    try {
        // Set end_date to yesterday to make it expired
        $stmt = $pdo->prepare("
            UPDATE user_subscriptions
            SET end_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            WHERE user_id = :user_id
            ORDER BY end_date DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);

        $_SESSION['success_message'] = "✅ Test: Subscription expired successfully! (End date set to yesterday)";

    } catch (PDOException $e) {
        error_log("Error expiring subscription for test: " . $e->getMessage());
        $_SESSION['error_message'] = "❌ Test failed: " . $e->getMessage();
    }

    header('Location: ' . VIEWS_URL . '/profile.php?tab=subscription');
    exit;
}

// If not POST request, redirect back
header('Location: ' . VIEWS_URL . '/profile.php?tab=subscription');
exit;
