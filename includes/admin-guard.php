<?php
/**
 * Admin Guard
 * Ensures only logged-in admin users can access admin pages
 * Include this at the top of all admin pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Not logged in - redirect to admin login
    header('Location: /glass-market/resources/views/admin/login.php');
    exit;
}

// Check if user is admin (handle both 1 and true)
if (!isset($_SESSION['is_admin']) || ($_SESSION['is_admin'] !== true && $_SESSION['is_admin'] != 1)) {
    // Logged in but not admin - redirect to public home page
    $_SESSION['access_denied'] = 'You do not have permission to access the admin area.';
    header('Location: /glass-market/public/index.php');
    exit;
}

// Admin access granted - continue
