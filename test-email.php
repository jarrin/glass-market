<?php
/**
 * Test Gmail SMTP Configuration
 * 
 * This script tests the email sending functionality using Gmail SMTP.
 * Run this from the command line or browser to verify your setup.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Services/EmailService.php';

// Create email service instance
$emailService = new EmailService();

// Test email address (change this to your email)
$test_email = 'musieatsbeha633@gmail.com';
$test_name = 'Test User';

echo "=== Glass Market - Gmail SMTP Test ===\n\n";
echo "Testing email configuration...\n";
echo "From: " . getenv('GMAIL_FROM_EMAIL') . "\n";
echo "SMTP Host: " . getenv('MAIL_HOST') . "\n";
echo "SMTP Port: " . getenv('MAIL_PORT') . "\n";
echo "Encryption: " . getenv('MAIL_ENCRYPTION') . "\n\n";

// Test 1: Welcome Email
echo "Test 1: Sending Welcome Email...\n";
$result1 = $emailService->sendWelcomeEmail($test_email, $test_name);
if ($result1) {
    echo "✓ Welcome email sent successfully!\n";
} else {
    echo "✗ Failed to send welcome email.\n";
}

echo "\n";

// Test 2: New Listing Notification
echo "Test 2: Sending New Listing Notification...\n";
$test_listing = [
    'title' => 'Test Glass Product',
    'glass_type' => 'Clear Float Glass',
    'quantity' => '500 kg',
    'location' => 'Amsterdam, Netherlands'
];
$result2 = $emailService->sendNewListingNotification($test_email, $test_name, $test_listing);
if ($result2) {
    echo "✓ Listing notification sent successfully!\n";
} else {
    echo "✗ Failed to send listing notification.\n";
}

echo "\n=== Test Complete ===\n\n";

if ($result1 && $result2) {
    echo "All tests passed! Check your inbox at: $test_email\n";
    echo "Note: Gmail may take a few seconds to deliver. Check spam folder if not received.\n";
} else {
    echo "Some tests failed. Please check:\n";
    echo "1. MAIL_USERNAME and GMAIL_FROM_EMAIL in .env are set to your Gmail address\n";
    echo "2. GOOGLE_APP_SECRET is your correct Google App Password (not regular password)\n";
    echo "3. Your Gmail account has 2-factor authentication enabled\n";
    echo "4. Check the error log for more details\n";
}

echo "\n";
