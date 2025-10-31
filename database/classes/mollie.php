<?php

/**
 * Mollie Payment Integration
 * Handles payment processing and subscriptions via Mollie API
 */
class MolliePayment
{
    private $mollie;
    private $apiKey;
    private $profileId;
    
    public function __construct()
    {
        // Load environment variables with detailed logging
        $envPath = dirname(dirname(__DIR__)) . '/.env';
        
        // Debug logging
        error_log("===== Mollie Constructor Debug =====");
        error_log("Current file: " . __FILE__);
        error_log("dirname(__DIR__): " . dirname(__DIR__));
        error_log("dirname(dirname(__DIR__)): " . dirname(dirname(__DIR__)));
        error_log("Looking for .env at: " . $envPath);
        error_log("Real path: " . realpath($envPath));
        error_log("File exists: " . (file_exists($envPath) ? 'YES' : 'NO'));
        
        if (file_exists($envPath)) {
            error_log(".env file found! Reading contents...");
            $envContent = file_get_contents($envPath);
            error_log("File size: " . strlen($envContent) . " bytes");
            
            $lines = explode("\n", $envContent);
            error_log("Total lines: " . count($lines));
            
            $foundKeys = [];
            
            foreach ($lines as $lineNum => $line) {
                $line = trim($line);
                
                // Skip comments and empty lines
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                
                // Parse KEY=VALUE or KEY="VALUE"
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Log what we're parsing
                    if ($key === 'MOLLIE_TEST_API_KEY' || $key === 'PROFILE_ID') {
                        error_log("Line $lineNum - Found key: $key");
                        error_log("Raw value: $value");
                    }
                    
                    // Remove quotes if present
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                        if ($key === 'MOLLIE_TEST_API_KEY' || $key === 'PROFILE_ID') {
                            error_log("After removing quotes: $value");
                        }
                    }
                    
                    if ($key === 'MOLLIE_TEST_API_KEY') {
                        $this->apiKey = $value;
                        $foundKeys[] = 'MOLLIE_TEST_API_KEY';
                        error_log("✅ Set apiKey: " . substr($value, 0, 15) . "...");
                    } elseif ($key === 'PROFILE_ID') {
                        $this->profileId = $value;
                        $foundKeys[] = 'PROFILE_ID';
                        error_log("✅ Set profileId: $value");
                    }
                }
            }
            
            error_log("Found keys: " . implode(', ', $foundKeys));
            error_log("Final apiKey: " . ($this->apiKey ? substr($this->apiKey, 0, 15) . "..." : "NOT SET"));
            error_log("Final profileId: " . ($this->profileId ? $this->profileId : "NOT SET"));
        } else {
            error_log("❌ .env file NOT FOUND!");
            error_log("Searched at: $envPath");
            error_log("Current working directory: " . getcwd());
            
            // Try alternative paths
            $alternatives = [
                __DIR__ . '/../../../.env',
                dirname(dirname(dirname(__DIR__))) . '/.env',
                $_SERVER['DOCUMENT_ROOT'] . '/glass-market/.env',
            ];
            
            error_log("Trying alternative paths:");
            foreach ($alternatives as $alt) {
                error_log("  - $alt : " . (file_exists($alt) ? 'EXISTS' : 'NOT FOUND'));
                if (file_exists($alt)) {
                    error_log("    Real path: " . realpath($alt));
                }
            }
        }
        
        error_log("===================================");
        
        // Initialize Mollie API client
        if (class_exists('\Mollie\Api\MollieApiClient')) {
            $this->mollie = new \Mollie\Api\MollieApiClient();
            if ($this->apiKey) {
                $this->mollie->setApiKey($this->apiKey);
            }
        }
    }
    
    /**
     * Create a payment for subscription
     * @param float $amount Amount in EUR
     * @param string $description Payment description
     * @param string $redirectUrl URL to redirect after payment
     * @param string $webhookUrl URL for payment webhooks
     * @param array $metadata Additional metadata
     * @return object|false Payment object or false on failure
     */
    public function createPayment($amount, $description, $redirectUrl, $webhookUrl = null, $metadata = [])
    {
        try {
            if (!$this->mollie) {
                $error = 'Mollie API client not initialized. Check if composer autoload is loaded and library is installed.';
                error_log('Mollie Payment Error: ' . $error);
                return ['error' => $error];
            }
            
            if (!$this->apiKey) {
                $error = 'Mollie API key not set. Check .env file has MOLLIE_TEST_API_KEY.';
                error_log('Mollie Payment Error: ' . $error);
                return ['error' => $error];
            }
            
            $payment = $this->mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($amount, 2, '.', '')
                ],
                'description' => $description,
                'redirectUrl' => $redirectUrl,
                'webhookUrl' => $webhookUrl,
                'metadata' => $metadata,
            ]);
            
            return $payment;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            error_log('Mollie Payment Error: ' . $errorMessage);
            error_log('Stack trace: ' . $e->getTraceAsString());
            return ['error' => $errorMessage];
        }
    }
    
    /**
     * Get payment status
     * @param string $paymentId Mollie payment ID
     * @return object|false Payment object or false on failure
     */
    public function getPayment($paymentId)
    {
        try {
            if (!$this->mollie) {
                return false;
            }
            
            return $this->mollie->payments->get($paymentId);
        } catch (Exception $e) {
            error_log('Mollie Get Payment Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if payment is paid
     * @param string $paymentId Mollie payment ID
     * @return bool
     */
    public function isPaymentPaid($paymentId)
    {
        $payment = $this->getPayment($paymentId);
        return $payment && $payment->isPaid();
    }
    
    /**
     * Create a subscription payment (extends trial)
     * @param int $userId User ID
     * @param int $months Number of months to extend
     * @param PDO $pdo Database connection
     * @return string|false Payment checkout URL or false on failure
     */
    public function createSubscriptionPayment($userId, $months, $pdo)
    {
        try {
            $amount = $months * 9.99; // €9.99 per month
            $description = "Glass Market Subscription - {$months} month(s)";

            $redirectUrl = 'http://localhost/glass-market/resources/views/admin/mollie-return.php?user_id=' . $userId;
            // For localhost testing, don't include webhook URL (Mollie can't reach localhost)
            // In production, set this to a publicly accessible URL
            $webhookUrl = null;

            $metadata = [
                'user_id' => $userId,
                'months' => $months,
                'type' => 'subscription'
            ];

            $payment = $this->createPayment($amount, $description, $redirectUrl, $webhookUrl, $metadata);
            
            // Check if error was returned
            if (is_array($payment) && isset($payment['error'])) {
                return $payment; // Return error array
            }
            
            if ($payment && is_object($payment)) {
                // Save payment reference to database
                $stmt = $pdo->prepare("
                    INSERT INTO mollie_payments (user_id, payment_id, amount, status, months, created_at)
                    VALUES (:user_id, :payment_id, :amount, :status, :months, NOW())
                ");
                $stmt->execute([
                    'user_id' => $userId,
                    'payment_id' => $payment->id,
                    'amount' => $amount,
                    'status' => $payment->status,
                    'months' => $months
                ]);
                
                return $payment->getCheckoutUrl();
            }
            
            return ['error' => 'Unknown error creating payment'];
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            error_log('Create Subscription Payment Error: ' . $errorMessage);
            error_log('Stack trace: ' . $e->getTraceAsString());
            return ['error' => $errorMessage];
        }
    }
    
    /**
     * Get API key (for testing)
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey ? substr($this->apiKey, 0, 10) . '...' : 'Not configured';
    }
    
    /**
     * Check if Mollie is configured
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->apiKey) && $this->mollie !== null;
    }
    
    /**
     * Get debug information for troubleshooting
     * @return array
     */
    public function getDebugInfo()
    {
        $envPath = dirname(dirname(__DIR__)) . '/.env';
        
        $info = [
            'env_path' => $envPath,
            'env_real_path' => realpath($envPath),
            'env_exists' => file_exists($envPath),
            'current_file' => __FILE__,
            'api_key_set' => !empty($this->apiKey),
            'api_key_preview' => $this->apiKey ? substr($this->apiKey, 0, 15) . '...' : 'NOT SET',
            'profile_id' => $this->profileId ?? 'NOT SET',
            'mollie_client_exists' => $this->mollie !== null,
            'mollie_class_exists' => class_exists('\Mollie\Api\MollieApiClient'),
            'working_directory' => getcwd(),
        ];
        
        // Try alternative paths
        $alternatives = [
            __DIR__ . '/../../../.env',
            dirname(dirname(dirname(__DIR__))) . '/.env',
            $_SERVER['DOCUMENT_ROOT'] . '/glass-market/.env',
        ];
        
        $info['alternative_paths'] = [];
        foreach ($alternatives as $alt) {
            $info['alternative_paths'][] = [
                'path' => $alt,
                'exists' => file_exists($alt),
                'real_path' => file_exists($alt) ? realpath($alt) : 'N/A'
            ];
        }
        
        return $info;
    }
}
