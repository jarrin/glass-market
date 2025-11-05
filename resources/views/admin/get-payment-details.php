<?php
/**
 * AJAX Endpoint: Get Payment Details from Mollie
 */
session_start();
require_once __DIR__ . '/../../../includes/admin-guard.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../database/classes/mollie.php';

header('Content-Type: application/json');

$payment_id = $_GET['payment_id'] ?? null;

if (!$payment_id) {
    echo json_encode(['success' => false, 'error' => 'No payment ID provided']);
    exit;
}

try {
    $mollie = new MolliePayment();

    if (!$mollie->isConfigured()) {
        echo json_encode(['success' => false, 'error' => 'Mollie API not configured']);
        exit;
    }

    $payment = $mollie->getPayment($payment_id);

    if (!$payment) {
        echo json_encode(['success' => false, 'error' => 'Payment not found']);
        exit;
    }

    $response = [
        'success' => true,
        'payment' => [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount->value ?? 'N/A',
            'currency' => $payment->amount->currency ?? 'EUR',
            'description' => $payment->description ?? 'N/A',
            'created_at' => $payment->createdAt ?? 'N/A',
            'paid_at' => $payment->paidAt ?? null,
            'expires_at' => $payment->expiresAt ?? null,
            'status_checks' => [
                'isPaid' => $payment->isPaid(),
                'isFailed' => $payment->isFailed(),
                'isCanceled' => $payment->isCanceled(),
                'isExpired' => $payment->isExpired(),
                'isOpen' => $payment->isOpen(),
                'isPending' => $payment->isPending(),
            ]
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
