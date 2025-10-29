<?php
/**
 * Session Activity Updater
 * Keeps the pricing flow session alive on user activity
 */

session_start();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Get the request data
$input = json_decode(file_get_contents('php://input'), true);
$flow = $input['flow'] ?? '';

// Update the session timestamp for the pricing flow
if ($flow === 'pricing' && isset($_SESSION['pricing_flow_started'])) {
    $_SESSION['pricing_flow_started'] = time();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'timestamp' => time(),
        'expires_in' => 600 - (time() - $_SESSION['pricing_flow_started'])
    ]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid session']);
}
