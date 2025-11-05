<?php
/**
 * Email Service Class
 * Handles all email sending functionality for Glass Market
 */

class EmailService {
    private $from_email;
    private $from_name = 'Glass Market';
    private $use_smtp = true; // Using Gmail SMTP
    
    // Gmail SMTP settings
    private $smtp_host = 'smtp.gmail.com';
    private $smtp_port = 587;
    private $smtp_username;
    private $smtp_password;
    private $smtp_encryption = 'tls';
    
    public function __construct() {
        // Load config if not already loaded
        if (!defined('BASE_URL')) {
            $config_path = __DIR__ . '/../../config.php';
            if (file_exists($config_path)) {
                require_once $config_path;
            } else {
                // Fallback if config not found
                define('BASE_URL', '');
            }
        }
        
        // Load from environment file
        $this->loadEnvVariables();
        
        // Set email credentials
        $this->from_email = getenv('GMAIL_FROM_EMAIL') ?: 'noreply@glassmarket.com';
        $this->smtp_username = getenv('GMAIL_FROM_EMAIL') ?: getenv('MAIL_USERNAME');
        $this->smtp_password = getenv('GOOGLE_APP_SECRET') ?: getenv('MAIL_PASSWORD');
        
        // Override from env if available
        if (getenv('MAIL_HOST')) {
            $this->smtp_host = getenv('MAIL_HOST');
        }
        if (getenv('MAIL_PORT')) {
            $this->smtp_port = getenv('MAIL_PORT');
        }
        if (getenv('MAIL_ENCRYPTION')) {
            $this->smtp_encryption = getenv('MAIL_ENCRYPTION');
        }
    }
    
    /**
     * Load environment variables from .env file
     */
    private function loadEnvVariables() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    $value = trim($value, '"\'');
                    
                    // Handle variable references like ${VAR_NAME}
                    if (preg_match('/\$\{([^}]+)\}/', $value, $matches)) {
                        $refValue = getenv($matches[1]);
                        $value = str_replace($matches[0], $refValue, $value);
                    }
                    
                    if (!getenv($name)) {
                        putenv("$name=$value");
                    }
                }
            }
        }
    }
    
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail($user_email, $user_name) {
        $subject = "Welcome to Glass Market! 🎉";
        
        $message = $this->getEmailTemplate([
            'title' => 'Welcome to Glass Market',
            'preview' => 'Your account has been created successfully',
            'content' => "
                <h2 style='color: #2f6df5; margin: 0 0 16px;'>Welcome, {$user_name}! 🎉</h2>
                <p style='font-size: 16px; line-height: 1.6; color: #1f2937;'>
                    Thank you for joining Glass Market, the premier marketplace for buying and selling glass materials.
                </p>
                <p style='font-size: 16px; line-height: 1.6; color: #1f2937;'>
                    Your account has been created successfully and you can now:
                </p>
                <ul style='font-size: 15px; line-height: 1.8; color: #1f2937;'>
                    <li>✓ Browse thousands of glass listings</li>
                    <li>✓ Create your own listings</li>
                    <li>✓ Activate your 3-month free trial</li>
                    <li>✓ Connect with sellers worldwide</li>
                </ul>
                <div style='text-align: center; margin: 32px 0;'>
                    <a href='" . BASE_URL . "/resources/views/subscription.php' style='
                        display: inline-block;
                        padding: 14px 32px;
                        background: #2f6df5;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                        font-weight: 600;
                        font-size: 16px;
                    '>Start Your Free Trial</a>
                </div>
                <p style='font-size: 14px; color: #6b7280; margin-top: 24px;'>
                    Need help getting started? Visit our <a href='" . BASE_URL . "/help.php' style='color: #2f6df5;'>Help Center</a>
                    or <a href='" . BASE_URL . "/contact.php' style='color: #2f6df5;'>Contact Support</a>.
                </p>
            "
        ]);
        
        return $this->sendEmail($user_email, $user_name, $subject, $message);
    }
    
    /**
     * Send new listing notification to subscribers
     */
    public function sendNewListingNotification($user_email, $user_name, $listing_details) {
        $subject = "🔔 New Glass Listing: {$listing_details['title']}";
        
        $message = $this->getEmailTemplate([
            'title' => 'New Listing Available',
            'preview' => 'A new glass listing matches your interests',
            'content' => "
                <h2 style='color: #2f6df5; margin: 0 0 16px;'>New Listing Alert! 🔔</h2>
                <p style='font-size: 16px; line-height: 1.6; color: #1f2937;'>
                    Hi {$user_name}, a new glass listing has been posted that might interest you:
                </p>
                <div style='background: #f9fafb; padding: 24px; border-radius: 12px; border: 2px solid #e5e7eb; margin: 24px 0;'>
                    <h3 style='margin: 0 0 12px; font-size: 20px; color: #1f2937;'>{$listing_details['title']}</h3>
                    <div style='display: grid; gap: 8px; font-size: 14px; color: #6b7280;'>
                        " . (!empty($listing_details['glass_type']) ? "<div>🔹 Type: <strong style='color: #1f2937;'>{$listing_details['glass_type']}</strong></div>" : "") . "
                        " . (!empty($listing_details['tons']) ? "<div>⚖️ Quantity: <strong style='color: #1f2937;'>{$listing_details['tons']} tons</strong></div>" : "") . "
                        " . (!empty($listing_details['storage_location']) ? "<div>📍 Location: <strong style='color: #1f2937;'>{$listing_details['storage_location']}</strong></div>" : "") . "
                        " . (!empty($listing_details['side']) ? "<div>📊 Type: <strong style='color: #1f2937;'>" . ($listing_details['side'] === 'WTS' ? 'For Sale' : 'Wanted') . "</strong></div>" : "") . "
                    </div>
                </div>
                <div style='text-align: center; margin: 32px 0;'>
                    <a href='" . BASE_URL . "/listing.php?id={$listing_details['id']}' style='
                        display: inline-block;
                        padding: 14px 32px;
                        background: #2f6df5;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                        font-weight: 600;
                        font-size: 16px;
                    '>View Full Listing</a>
                </div>
                <p style='font-size: 12px; color: #9ca3af; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;'>
                    You're receiving this because you enabled notifications for new listings. 
                    <a href='" . BASE_URL . "/resources/views/profile.php?tab=notifications' style='color: #2f6df5;'>Manage preferences</a>
                </p>
            "
        ]);
        
        return $this->sendEmail($user_email, $user_name, $subject, $message);
    }
    
    /**
     * Send email verification
     */
    public function sendVerificationEmail($user_email, $user_name, $verification_token) {
        $subject = "Verify Your Email - Glass Market";
        
        $verification_link = BASE_URL . "/verify-email.php?token=" . $verification_token;
        
        $message = $this->getEmailTemplate([
            'title' => 'Verify Your Email',
            'preview' => 'Please verify your email address',
            'content' => "
                <h2 style='color: #2f6df5; margin: 0 0 16px;'>Verify Your Email Address</h2>
                <p style='font-size: 16px; line-height: 1.6; color: #1f2937;'>
                    Hi {$user_name},
                </p>
                <p style='font-size: 16px; line-height: 1.6; color: #1f2937;'>
                    Please click the button below to verify your email address and activate your Glass Market account.
                </p>
                <div style='text-align: center; margin: 32px 0;'>
                    <a href='{$verification_link}' style='
                        display: inline-block;
                        padding: 14px 32px;
                        background: #22c55e;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                        font-weight: 600;
                        font-size: 16px;
                    '>Verify Email Address</a>
                </div>
                <p style='font-size: 14px; color: #6b7280;'>
                    Or copy and paste this link into your browser:<br>
                    <a href='{$verification_link}' style='color: #2f6df5; word-break: break-all;'>{$verification_link}</a>
                </p>
                <p style='font-size: 12px; color: #9ca3af; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;'>
                    If you didn't create an account on Glass Market, you can safely ignore this email.
                </p>
            "
        ]);
        
        return $this->sendEmail($user_email, $user_name, $subject, $message);
    }
    
    /**
     * Get styled email template
     */
    private function getEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$data['title']}</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; background: #f5f5f7;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 40px 20px;'>
                <!-- Header -->
                <div style='text-align: center; margin-bottom: 32px;'>
                    <h1 style='margin: 0; font-size: 28px; font-weight: 700; color: #1d1d1f;'>
                        <span style='color: #2f6df5;'>GLASS</span> MARKET
                    </h1>
                </div>
                
                <!-- Main Content -->
                <div style='background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);'>
                    {$data['content']}
                </div>
                
                <!-- Footer -->
                <div style='text-align: center; margin-top: 32px; padding: 24px; color: #6e6e73; font-size: 13px;'>
                    <p style='margin: 0 0 8px;'>© " . date('Y') . " Glass Market. All rights reserved.</p>
                    <p style='margin: 0;'>
                        <a href='" . BASE_URL . "' style='color: #2f6df5; text-decoration: none;'>Visit Website</a> • 
                        <a href='" . BASE_URL . "/contact.php' style='color: #2f6df5; text-decoration: none;'>Contact Us</a> • 
                        <a href='" . BASE_URL . "/privacy.php' style='color: #2f6df5; text-decoration: none;'>Privacy Policy</a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Send email using PHP mail() or SMTP
     */
    private function sendEmail($to, $to_name, $subject, $html_message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        try {
            if ($this->use_smtp) {
                // TODO: Implement SMTP sending (using PHPMailer or similar)
                return $this->sendViaSMTP($to, $to_name, $subject, $html_message);
            } else {
                // Use PHP's built-in mail function
                $result = mail($to, $subject, $html_message, $headers);
                
                // Log email attempt
                $this->logEmail($to, $subject, $result ? 'sent' : 'failed');
                
                return $result;
            }
        } catch (Exception $e) {
            error_log("Email send error: " . $e->getMessage());
            $this->logEmail($to, $subject, 'error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log email activity
     */
    private function logEmail($to, $subject, $status) {
        $log_file = __DIR__ . '/../../../storage/logs/emails.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " | TO: {$to} | SUBJECT: {$subject} | STATUS: {$status}\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
    
    /**
     * Send via SMTP using Gmail
     */
    private function sendViaSMTP($to, $to_name, $subject, $html_message) {
        try {
            // Create email headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
            $headers .= "Reply-To: {$this->from_email}\r\n";
            $headers .= "X-Mailer: Glass Market PHP/" . phpversion();
            
            // Use PHPMailer if available, otherwise use basic SMTP
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                return $this->sendWithPHPMailer($to, $to_name, $subject, $html_message);
            }
            
            // Fallback to basic mail with proper headers
            $result = $this->sendWithSocketSMTP($to, $subject, $html_message);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("SMTP send error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email using socket connection to Gmail SMTP
     */
    private function sendWithSocketSMTP($to, $subject, $message) {
        try {
            // Connect to Gmail SMTP server
            $smtp = fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 30);
            if (!$smtp) {
                throw new Exception("Failed to connect to SMTP server: $errstr ($errno)");
            }
            
            // Read server response
            $response = fgets($smtp);
            
            // Send EHLO
            fputs($smtp, "EHLO {$this->smtp_host}\r\n");
            $response = fgets($smtp);
            
            // Start TLS
            fputs($smtp, "STARTTLS\r\n");
            $response = fgets($smtp);
            
            // Enable crypto
            stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            // Send EHLO again after TLS
            fputs($smtp, "EHLO {$this->smtp_host}\r\n");
            $response = fgets($smtp);
            
            // Authenticate
            fputs($smtp, "AUTH LOGIN\r\n");
            $response = fgets($smtp);
            
            fputs($smtp, base64_encode($this->smtp_username) . "\r\n");
            $response = fgets($smtp);
            
            fputs($smtp, base64_encode($this->smtp_password) . "\r\n");
            $response = fgets($smtp);
            
            if (strpos($response, '235') === false) {
                throw new Exception("SMTP authentication failed: $response");
            }
            
            // Send email
            fputs($smtp, "MAIL FROM: <{$this->from_email}>\r\n");
            $response = fgets($smtp);
            
            fputs($smtp, "RCPT TO: <{$to}>\r\n");
            $response = fgets($smtp);
            
            fputs($smtp, "DATA\r\n");
            $response = fgets($smtp);
            
            // Email headers and body
            $email_data = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $email_data .= "To: <{$to}>\r\n";
            $email_data .= "Subject: {$subject}\r\n";
            $email_data .= "MIME-Version: 1.0\r\n";
            $email_data .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_data .= "\r\n";
            $email_data .= $message;
            $email_data .= "\r\n.\r\n";
            
            fputs($smtp, $email_data);
            $response = fgets($smtp);
            
            // Quit
            fputs($smtp, "QUIT\r\n");
            fclose($smtp);
            
            // Log success
            $this->logEmail($to, $subject, 'sent via Gmail SMTP');
            
            return true;
            
        } catch (Exception $e) {
            error_log("Socket SMTP error: " . $e->getMessage());
            $this->logEmail($to, $subject, 'failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send with PHPMailer if available
     */
    private function sendWithPHPMailer($to, $to_name, $subject, $html_message) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            $mail->SMTPSecure = $this->smtp_encryption;
            $mail->Port = $this->smtp_port;
            
            // Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to, $to_name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_message;
            $mail->AltBody = strip_tags($html_message);
            
            $mail->send();
            $this->logEmail($to, $subject, 'sent via PHPMailer');
            return true;
            
        } catch (Exception $e) {
            error_log("PHPMailer error: " . $mail->ErrorInfo);
            $this->logEmail($to, $subject, 'failed: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
