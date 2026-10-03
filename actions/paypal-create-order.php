<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/includes/PayPalClient.php';

require_role('estudiante');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('cursos.php'));
    exit;
}

$slug = trim((string) ($_POST['slug'] ?? ''));
$csrf = (string) ($_POST['csrf_token'] ?? '');
$course = course_by_slug($slug);

if (!$course || !verify_csrf($csrf)) {
    header('Location: ' . url('cursos.php'));
    exit;
}

if (student_owns_course($slug)) {
    header('Location: ' . url('estudiante/?course=' . rawurlencode($slug) . '#cursos'));
    exit;
}

$price = course_price_info($course);
if ($price['free']) {
    header('Location: ' . url('curso.php?slug=' . rawurlencode($slug)));
    exit;
}

try {
    $paypal = new PayPalClient();

    $order = $paypal->createOrder(
        $slug,
        (string) $course['title'],
        (string) $price['amount'],
        (string) $price['currency'],
        absolute_url('actions/paypal-return.php'),
        absolute_url('estudiante/checkout.php?slug=' . rawurlencode($slug) . '&payment=cancelled')
    );

    $orderId = trim((string) ($order['id'] ?? ''));
    $approvalUrl = $paypal->approvalUrl($order);

    if ($orderId === '' || !$approvalUrl) {
        throw new RuntimeException('PayPal no devolvió una orden aprobable.');
    }

    $_SESSION['paypal_pending_orders'] ??= [];
    $_SESSION['paypal_pending_orders'][$orderId] = [
        'slug' => $slug,
        'amount' => (string) $price['amount'],
        'currency' => (string) $price['currency'],
        'created_at' => time(),
    ];

    header('Location: ' . $approvalUrl);
    exit;
} catch (Throwable $error) {
    $_SESSION['paypal_last_error'] = $error->getMessage();
    header('Location: ' . url('estudiante/checkout.php?slug=' . rawurlencode($slug) . '&payment=error'));
    exit;
}
