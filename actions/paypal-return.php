<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/includes/PayPalClient.php';

require_role('estudiante');

$orderId = trim((string) ($_GET['token'] ?? ''));
$pending = is_array($_SESSION['paypal_pending_orders'][$orderId] ?? null)
    ? $_SESSION['paypal_pending_orders'][$orderId]
    : null;

if ($orderId === '' || !$pending) {
    header('Location: ' . url('estudiante/?payment=invalid#cursos'));
    exit;
}

$slug = (string) ($pending['slug'] ?? '');
$course = course_by_slug($slug);
if (!$course) {
    unset($_SESSION['paypal_pending_orders'][$orderId]);
    header('Location: ' . url('estudiante/?payment=invalid#cursos'));
    exit;
}

try {
    $paypal = new PayPalClient();
    $capture = $paypal->captureOrder($orderId);

    $status = strtoupper((string) ($capture['status'] ?? ''));
    $captureData = $capture['purchase_units'][0]['payments']['captures'][0] ?? [];
    $capturedAmount = (string) ($captureData['amount']['value'] ?? '');
    $capturedCurrency = strtoupper((string) ($captureData['amount']['currency_code'] ?? ''));

    $expectedAmount = number_format((float) ($pending['amount'] ?? 0), 2, '.', '');
    $expectedCurrency = strtoupper((string) ($pending['currency'] ?? ''));
    $normalizedCapturedAmount = $capturedAmount !== ''
        ? number_format((float) $capturedAmount, 2, '.', '')
        : '';

    if (
        $status !== 'COMPLETED'
        || $normalizedCapturedAmount !== $expectedAmount
        || $capturedCurrency !== $expectedCurrency
    ) {
        throw new RuntimeException('La captura de PayPal no coincide con la orden esperada.');
    }

    record_completed_purchase(
        $course,
        'PayPal',
        $orderId,
        $expectedAmount,
        $expectedCurrency,
        'COMPLETED'
    );

    unset($_SESSION['paypal_pending_orders'][$orderId], $_SESSION['paypal_last_error']);

    header('Location: ' . url('estudiante/?payment=success&course=' . rawurlencode($slug) . '#cursos'));
    exit;
} catch (Throwable $error) {
    $_SESSION['paypal_last_error'] = $error->getMessage();
    header('Location: ' . url('estudiante/checkout.php?slug=' . rawurlencode($slug) . '&payment=error'));
    exit;
}
