<?php
/**
 * Test the Rust Email Service from PHP
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\RustMailer;

echo "=== Rust Email Service Test ===\n\n";

try {
    $mailer = new RustMailer();
    
    echo "1. Testing simple text email...\n";
    $result = $mailer->send(
        'musieatsbeha633@gmail.com',
        'Test from Rust Mailer',
        'This email was sent from PHP calling a Rust microservice!',
        false
    );
    
    echo "   Result: " . ($result['success'] ? '✓ SUCCESS' : '✗ FAILED') . "\n";
    echo "   Message: {$result['message']}\n\n";
    
    if ($result['success']) {
        echo "2. Testing HTML email...\n";
        $htmlBody = '<h1>Hello from Rust!</h1><p>This is a <strong>HTML</strong> email sent via Rust.</p>';
        $result2 = $mailer->send(
            'musieatsbeha633@gmail.com',
            'HTML Test from Rust Mailer',
            $htmlBody,
            true
        );
        
        echo "   Result: " . ($result2['success'] ? '✓ SUCCESS' : '✗ FAILED') . "\n";
        echo "   Message: {$result2['message']}\n\n";
        
        echo "3. Testing welcome email template...\n";
        $result3 = $mailer->sendWelcomeEmail('musieatsbeha633@gmail.com', 'Test User');
        
        echo "   Result: " . ($result3['success'] ? '✓ SUCCESS' : '✗ FAILED') . "\n";
        echo "   Message: {$result3['message']}\n\n";
    }
    
    echo "✓ All tests completed!\n";
    echo "\nCheck your inbox at: musieatsbeha633@gmail.com\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
