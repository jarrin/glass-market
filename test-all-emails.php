<?php
/**
 * Complete Email System Test
 * Tests all email types: Welcome, Subscription, Cancellation, Payment Receipt
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\RustMailer;

echo "=== Complete Email System Test ===\n\n";

$mailer = new RustMailer();
$testEmail = 'musieatsbeha633@gmail.com';
$testName = 'Test User';

$allSuccess = true;

// Test 1: Welcome Email
echo "1. Testing Welcome Email...\n";
$result1 = $mailer->sendWelcomeEmail($testEmail, $testName);
echo "   " . ($result1['success'] ? '✓' : '✗') . " " . $result1['message'] . "\n\n";
$allSuccess = $allSuccess && $result1['success'];

// Test 2: Trial Subscription Email
echo "2. Testing Trial Subscription Email...\n";
$result2 = $mailer->sendSubscriptionEmail($testEmail, $testName, [
    'plan_name' => '3-Month Free Trial',
    'amount' => '0.00',
    'currency' => 'EUR',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+3 months')),
    'is_trial' => true,
]);
echo "   " . ($result2['success'] ? '✓' : '✗') . " " . $result2['message'] . "\n\n";
$allSuccess = $allSuccess && $result2['success'];

// Test 3: Paid Subscription Email
echo "3. Testing Paid Subscription Email...\n";
$result3 = $mailer->sendSubscriptionEmail($testEmail, $testName, [
    'plan_name' => '12-Month Premium Plan',
    'amount' => '99.99',
    'currency' => 'EUR',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+12 months')),
    'is_trial' => false,
]);
echo "   " . ($result3['success'] ? '✓' : '✗') . " " . $result3['message'] . "\n\n";
$allSuccess = $allSuccess && $result3['success'];

// Test 4: Subscription Cancelled Email
echo "4. Testing Subscription Cancellation Email...\n";
$result4 = $mailer->sendSubscriptionCancelledEmail($testEmail, $testName, [
    'plan_name' => 'Premium Plan',
    'end_date' => date('Y-m-d', strtotime('+1 month')),
]);
echo "   " . ($result4['success'] ? '✓' : '✗') . " " . $result4['message'] . "\n\n";
$allSuccess = $allSuccess && $result4['success'];

// Test 5: Payment Receipt Email
echo "5. Testing Payment Receipt Email...\n";
$result5 = $mailer->sendPaymentReceipt($testEmail, $testName, [
    'payment_id' => 'tr_test_' . time(),
    'amount' => '99.99',
    'currency' => 'EUR',
    'description' => '12-Month Subscription Renewal',
    'date' => date('Y-m-d H:i:s'),
    'method' => 'Credit Card',
    'status' => 'paid',
]);
echo "   " . ($result5['success'] ? '✓' : '✗') . " " . $result5['message'] . "\n\n";
$allSuccess = $allSuccess && $result5['success'];

// Test 6: Listing Notification Email
echo "6. Testing Listing Notification Email...\n";
$result6 = $mailer->sendListingNotification(
    $testEmail,
    'Premium Float Glass - 1000kg Available',
    'http://localhost/glass-market/resources/views/listings.php?id=123'
);
echo "   " . ($result6['success'] ? '✓' : '✗') . " " . $result6['message'] . "\n\n";
$allSuccess = $allSuccess && $result6['success'];

// Summary
echo str_repeat("=", 50) . "\n";
if ($allSuccess) {
    echo "✓ ALL TESTS PASSED!\n";
    echo "\nCheck your inbox at: $testEmail\n";
    echo "You should have received 6 emails:\n";
    echo "  1. Welcome Email\n";
    echo "  2. Trial Subscription Confirmation\n";
    echo "  3. Paid Subscription Confirmation\n";
    echo "  4. Subscription Cancellation Notice\n";
    echo "  5. Payment Receipt\n";
    echo "  6. New Listing Notification\n";
} else {
    echo "✗ SOME TESTS FAILED\n";
    echo "Check the error messages above.\n";
}
echo str_repeat("=", 50) . "\n";

echo "\nEmail system is " . ($allSuccess ? "READY FOR PRODUCTION! 🚀" : "needs attention ⚠️") . "\n";
