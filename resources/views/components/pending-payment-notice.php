<?php
/**
 * Pending Payment Notice Component
 * Shows notification if user has pending/failed/canceled payments
 */

if (!isset($pdo) || !isset($_SESSION['user_id'])) {
    return;
}

// Check for recent open/failed/canceled payments
$stmt = $pdo->prepare("
    SELECT payment_id, amount, status, months, created_at
    FROM mollie_payments
    WHERE user_id = :user_id
    AND status IN ('open', 'failed', 'canceled', 'expired')
    AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$pending_payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pending_payment) {
    return;
}

// Check actual status from Mollie
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../database/classes/mollie.php';

$mollie = new MolliePayment();
if (!$mollie->isConfigured()) {
    return;
}

try {
    $payment = $mollie->getPayment($pending_payment['payment_id']);

    if (!$payment) {
        return;
    }

    // Update database with current status
    $current_status = $payment->status;
    if ($current_status !== $pending_payment['status']) {
        $stmt = $pdo->prepare("UPDATE mollie_payments SET status = :status, updated_at = NOW() WHERE payment_id = :payment_id");
        $stmt->execute(['status' => $current_status, 'payment_id' => $pending_payment['payment_id']]);
        $pending_payment['status'] = $current_status;
    }

    // Only show notice for open, failed, canceled, expired
    if (!in_array($current_status, ['open', 'failed', 'canceled', 'expired'])) {
        return;
    }

    // Determine notice type and message
    $notice_config = [
        'open' => [
            'bg' => '#e3f2fd',
            'border' => '#2196f3',
            'icon_bg' => '#2196f3',
            'title' => 'Payment Pending',
            'message' => 'You have an incomplete payment of €' . number_format($pending_payment['amount'], 2) . '. Complete your payment to activate your subscription.',
            'button_text' => 'Complete Payment',
            'button_url' => $payment->getCheckoutUrl(),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        ],
        'failed' => [
            'bg' => '#ffebee',
            'border' => '#f44336',
            'icon_bg' => '#f44336',
            'title' => 'Payment Failed',
            'message' => 'Your payment of €' . number_format($pending_payment['amount'], 2) . ' could not be processed. Please try again with a different payment method.',
            'button_text' => 'Try Again',
            'button_url' => VIEWS_URL . '/pricing.php',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
        ],
        'canceled' => [
            'bg' => '#fff3cd',
            'border' => '#ff9800',
            'icon_bg' => '#ff9800',
            'title' => 'Payment Canceled',
            'message' => 'You canceled the payment of €' . number_format($pending_payment['amount'], 2) . '. You can try again whenever you\'re ready.',
            'button_text' => 'Try Again',
            'button_url' => VIEWS_URL . '/pricing.php',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        ],
        'expired' => [
            'bg' => '#fce4ec',
            'border' => '#e91e63',
            'icon_bg' => '#e91e63',
            'title' => 'Payment Expired',
            'message' => 'Your payment session expired after 15 minutes of inactivity. Please create a new payment to continue.',
            'button_text' => 'Start New Payment',
            'button_url' => VIEWS_URL . '/pricing.php',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        ]
    ];

    $config = $notice_config[$current_status] ?? null;
    if (!$config) {
        return;
    }

    ?>
    <div style="
        background: <?php echo $config['bg']; ?>;
        border: 2px solid <?php echo $config['border']; ?>;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    ">
        <div style="display: flex; gap: 16px; align-items: start;">
            <div style="
                width: 48px;
                height: 48px;
                background: <?php echo $config['icon_bg']; ?>;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            ">
                <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                    <?php echo $config['icon']; ?>
                </svg>
            </div>

            <div style="flex: 1;">
                <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 700; color: #1a1a1a;">
                    <?php echo $config['title']; ?>
                </h3>
                <p style="margin: 0 0 16px; color: #666; line-height: 1.6;">
                    <?php echo $config['message']; ?>
                </p>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="<?php echo $config['button_url']; ?>" style="
                        display: inline-block;
                        padding: 10px 20px;
                        background: <?php echo $config['icon_bg']; ?>;
                        color: white;
                        text-decoration: none;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 14px;
                        transition: opacity 0.2s;
                    " onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <?php echo $config['button_text']; ?>
                    </a>
                    <button onclick="dismissPaymentNotice('<?php echo $pending_payment['payment_id']; ?>')" style="
                        padding: 10px 20px;
                        background: white;
                        color: #666;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.2s;
                    " onmouseover="this.style.borderColor='#666'" onmouseout="this.style.borderColor='#e0e0e0'">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function dismissPaymentNotice(paymentId) {
        // Store dismissed payment in sessionStorage
        sessionStorage.setItem('dismissed_payment_' + paymentId, 'true');
        // Hide the notice
        event.target.closest('div[style*="background: <?php echo $config['bg']; ?>"]').style.display = 'none';
    }

    // Check if already dismissed
    (function() {
        const paymentId = '<?php echo $pending_payment['payment_id']; ?>';
        if (sessionStorage.getItem('dismissed_payment_' + paymentId) === 'true') {
            const notice = document.querySelector('div[style*="background: <?php echo $config['bg']; ?>"]');
            if (notice) {
                notice.style.display = 'none';
            }
        }
    })();
    </script>
    <?php

} catch (Exception $e) {
    error_log('Pending payment notice error: ' . $e->getMessage());
}
