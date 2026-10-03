<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

require_role('estudiante');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('cursos.php'));
    exit;
}

$slug = trim((string) ($_POST['slug'] ?? ''));
$course = course_by_slug($slug);

if (!$course || !verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    header('Location: ' . url('cursos.php'));
    exit;
}

$price = course_price_info($course);
if (!$price['free']) {
    header('Location: ' . url('estudiante/checkout.php?slug=' . rawurlencode($slug)));
    exit;
}

if (!student_owns_course($slug)) {
    record_completed_purchase(
        $course,
        'AulaGo',
        'FREE-' . strtoupper(substr(hash('sha256', user_email() . '|' . $slug), 0, 12)),
        '0.00',
        null,
        'ENROLLED'
    );
}

header('Location: ' . url('estudiante/?payment=free&course=' . rawurlencode($slug) . '#cursos'));
exit;
