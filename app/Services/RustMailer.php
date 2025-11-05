<?php
/**
 * Rust Email Service Wrapper for Laravel/PHP
 * 
 * This class provides a clean interface to the Rust email microservice
 */

namespace App\Services;

class RustMailer
{
    private string $binaryPath;
    private string $gmailUser;
    private string $gmailPass;

    public function __construct(?string $binaryPath = null, ?string $gmailUser = null, ?string $gmailPass = null)
    {
        // Use provided path or detect automatically
        if ($binaryPath) {
            $this->binaryPath = $binaryPath;
        } else {
            // Try to detect Laravel base path, otherwise use relative path
            try {
                $this->binaryPath = base_path('email-service/target/release/glass-market-mailer.exe');
            } catch (\Throwable $e) {
                $this->binaryPath = __DIR__ . '/../../email-service/target/release/glass-market-mailer.exe';
            }
        }
        
        // Use provided credentials or load from environment/config
        if ($gmailUser) {
            $this->gmailUser = $gmailUser;
        } else {
            try {
                $this->gmailUser = env('GMAIL_FROM_EMAIL', 'musieatsbeha633@gmail.com');
            } catch (\Throwable $e) {
                $this->gmailUser = getenv('GMAIL_FROM_EMAIL') ?: 'musieatsbeha633@gmail.com';
            }
        }
        
        if ($gmailPass) {
            $this->gmailPass = $gmailPass;
        } else {
            try {
                $this->gmailPass = env('GOOGLE_APP_SECRET', 'dfylmduqfpapcsqp');
            } catch (\Throwable $e) {
                $this->gmailPass = getenv('GOOGLE_APP_SECRET') ?: 'dfylmduqfpapcsqp';
            }
        }
        
        if (!file_exists($this->binaryPath)) {
            throw new \Exception(
                "Rust email binary not found at: {$this->binaryPath}\nRun: cd email-service && cargo build --release"
            );
        }
    }

    /**
     * Send a simple text/HTML email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (text or HTML)
     * @param bool $isHtml Whether body contains HTML
     * @param string|null $toName Recipient name
     * @return array ['success' => bool, 'message' => string]
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        bool $isHtml = false,
        ?string $toName = null
    ): array {
        // Create temporary JSON file
        $request = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'is_html' => $isHtml,
        ];
        
        if ($toName) {
            $request['to_name'] = $toName;
        }
        
        $jsonFile = tempnam(sys_get_temp_dir(), 'email_');
        file_put_contents($jsonFile, json_encode($request));
        
        try {
            // Build command - Windows style
            $cmd = sprintf(
                'set GMAIL_USER=%s && set GMAIL_PASS=%s && "%s" "%s" 2>&1',
                $this->gmailUser,
                $this->gmailPass,
                $this->binaryPath,
                $jsonFile
            );
            
            exec($cmd, $output, $returnCode);
            
            $outputStr = implode("\n", $output);
            
            // Parse JSON response
            $lastLine = end($output);
            $response = @json_decode($lastLine, true);
            
            if (!$response) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from mailer: ' . $outputStr,
                ];
            }
            
            return $response;
            
        } finally {
            // Clean up temp file
            @unlink($jsonFile);
        }
    }

    /**
     * Send a quick email (simpler, less overhead)
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param bool $isHtml Whether body is HTML
     * @return bool Success status
     */
    public function sendQuick(
        string $to,
        string $subject,
        string $body,
        bool $isHtml = false
    ): bool {
        $htmlFlag = $isHtml ? 'html' : '';
        
        $cmd = sprintf(
            'set GMAIL_USER=%s && set GMAIL_PASS=%s && %s --quick %s %s %s %s 2>&1',
            escapeshellarg($this->gmailUser),
            escapeshellarg($this->gmailPass),
            escapeshellarg($this->binaryPath),
            escapeshellarg($to),
            escapeshellarg($subject),
            escapeshellarg($body),
            $htmlFlag
        );
        
        exec($cmd, $output, $returnCode);
        
        return $returnCode === 0;
    }

    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(string $email, string $username): array
    {
        $subject = "Welcome to Glass Market!";
        $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Glass Market!</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{$username}</strong>,</p>
            <p>Thank you for joining Glass Market! Your account has been successfully created.</p>
            <p>You can now start browsing and purchasing glass products from our marketplace.</p>
            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Glass Market. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $this->send($email, $subject, $body, true, $username);
    }

    /**
     * Send listing notification email
     */
    public function sendListingNotification(string $email, string $listingTitle, string $listingUrl): array
    {
        $subject = "New Listing: $listingTitle";
        $body = <<<HTML
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>New Listing Available</h2>
    <p>A new listing has been posted: <strong>{$listingTitle}</strong></p>
    <p><a href="{$listingUrl}" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px;">View Listing</a></p>
</body>
</html>
HTML;

        return $this->send($email, $subject, $body, true);
    }

    /**
     * Send subscription created/renewed email
     */
    public function sendSubscriptionEmail(string $email, string $username, array $subscription): array
    {
        $planName = $subscription['plan_name'] ?? 'Subscription';
        $amount = $subscription['amount'] ?? '0.00';
        $currency = $subscription['currency'] ?? 'EUR';
        $startDate = $subscription['start_date'] ?? date('Y-m-d');
        $endDate = $subscription['end_date'] ?? 'Ongoing';
        $isTrial = $subscription['is_trial'] ?? false;

        $subject = $isTrial ? "Your Free Trial Has Started!" : "Subscription Confirmation - {$planName}";
        
        $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px 20px; background: #ffffff; border: 1px solid #e0e0e0; }
        .subscription-details { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-weight: bold; color: #666; }
        .detail-value { color: #333; }
        .amount { font-size: 24px; color: #4CAF50; font-weight: bold; }
        .trial-badge { background: #FFeb3B; color: #000; padding: 5px 15px; border-radius: 20px; font-weight: bold; display: inline-block; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { background: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; display: inline-block; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$subject}</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{$username}</strong>,</p>
HTML;

        if ($isTrial) {
            $body .= <<<HTML
            <p><span class="trial-badge">FREE TRIAL</span></p>
            <p>Great news! Your <strong>3-month free trial</strong> has been activated. You now have full access to all Glass Market features.</p>
HTML;
        } else {
            $body .= <<<HTML
            <p>Thank you for subscribing to <strong>{$planName}</strong>! Your subscription has been successfully activated.</p>
HTML;
        }

        $body .= <<<HTML
            <div class="subscription-details">
                <h3>Subscription Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Plan:</span>
                    <span class="detail-value">{$planName}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value amount">{$currency} {$amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">{$startDate}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">End Date:</span>
                    <span class="detail-value">{$endDate}</span>
                </div>
            </div>
            
            <p>You can manage your subscription anytime from your profile page.</p>
            
            <p style="text-align: center;">
                <a href="http://localhost/glass-market/resources/views/profile.php?tab=subscription" class="btn">Manage Subscription</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Glass Market. All rights reserved.</p>
            <p>Questions? Contact us at support@glassmarket.com</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $this->send($email, $subject, $body, true, $username);
    }

    /**
     * Send subscription cancelled email
     */
    public function sendSubscriptionCancelledEmail(string $email, string $username, array $subscription): array
    {
        $planName = $subscription['plan_name'] ?? 'Subscription';
        $endDate = $subscription['end_date'] ?? date('Y-m-d');

        $subject = "Subscription Cancellation Confirmation";
        
        $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ff9800; color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px 20px; background: #ffffff; border: 1px solid #e0e0e0; }
        .info-box { background: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { background: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; display: inline-block; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Cancelled</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{$username}</strong>,</p>
            <p>Your subscription to <strong>{$planName}</strong> has been cancelled as requested.</p>
            
            <div class="info-box">
                <strong>Important:</strong> You will retain access to all features until <strong>{$endDate}</strong>.
            </div>
            
            <p>We're sorry to see you go! If you change your mind, you can reactivate your subscription anytime from your profile.</p>
            
            <p style="text-align: center;">
                <a href="http://localhost/glass-market/resources/views/profile.php?tab=subscription" class="btn">View Subscription</a>
            </p>
            
            <p>If you cancelled by mistake or have any questions, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Glass Market. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $this->send($email, $subject, $body, true, $username);
    }

    /**
     * Send payment receipt email
     */
    public function sendPaymentReceipt(string $email, string $username, array $payment): array
    {
        $amount = $payment['amount'] ?? '0.00';
        $currency = $payment['currency'] ?? 'EUR';
        $paymentId = $payment['payment_id'] ?? 'N/A';
        $description = $payment['description'] ?? 'Payment';
        $date = $payment['date'] ?? date('Y-m-d H:i:s');
        $method = $payment['method'] ?? 'Credit Card';
        $status = $payment['status'] ?? 'paid';

        $subject = "Payment Receipt - {$currency} {$amount}";
        
        $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2196F3; color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px 20px; background: #ffffff; border: 1px solid #e0e0e0; }
        .receipt { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .receipt-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .receipt-label { font-weight: bold; color: #666; }
        .receipt-value { color: #333; }
        .total { font-size: 24px; color: #2196F3; font-weight: bold; background: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 8px; text-align: center; }
        .success-badge { background: #4CAF50; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; display: inline-block; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Payment Successful</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{$username}</strong>,</p>
            <p><span class="success-badge">{$status}</span></p>
            <p>Thank you for your payment! Here is your receipt:</p>
            
            <div class="receipt">
                <h3>Payment Details</h3>
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID:</span>
                    <span class="receipt-value">{$paymentId}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Description:</span>
                    <span class="receipt-value">{$description}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Method:</span>
                    <span class="receipt-value">{$method}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Date:</span>
                    <span class="receipt-value">{$date}</span>
                </div>
            </div>
            
            <div class="total">
                Total: {$currency} {$amount}
            </div>
            
            <p>This receipt has been sent to your email for your records. You can also view all your transactions in your profile.</p>
            
            <p style="font-size: 12px; color: #666; margin-top: 30px;">
                <strong>Need help?</strong> Contact our support team at support@glassmarket.com
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Glass Market. All rights reserved.</p>
            <p>Payment ID: {$paymentId}</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $this->send($email, $subject, $body, true, $username);
    }
}
