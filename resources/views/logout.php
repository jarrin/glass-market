<?php
session_start();

// Database connection for any cleanup if needed
require_once __DIR__ . '/../../config.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: ' . PUBLIC_URL . '/index.php');
exit;
