<?php
/**
 * Dismiss Notification Handler
 * Stores dismissal in session to prevent showing again for 24 hours
 */

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store dismissal timestamp
    $_SESSION['notification_dismissed_at'] = time();
    
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
