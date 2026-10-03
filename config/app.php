<?php

declare(strict_types=1);

define('PROJECT_ROOT', dirname(__DIR__));
define('APP_NAME', 'AulaGo');

function detect_base_url(): string
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = realpath(PROJECT_ROOT);

    if ($documentRoot && $projectRoot && str_starts_with($projectRoot, $documentRoot)) {
        $relative = str_replace('\\', '/', substr($projectRoot, strlen($documentRoot)));
        return '/' . trim($relative, '/');
    }

    return '';
}

define('BASE_URL', detect_base_url());

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base !== '' ? $base . '/' : '/';
    }

    return ($base !== '' ? $base : '') . '/' . $path;
}

function absolute_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    return $scheme . '://' . $host . url($path);
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function catalog(): array
{
    static $catalog = null;
    if ($catalog === null) {
        $catalog = require PROJECT_ROOT . '/data/catalog.php';
    }
    return $catalog;
}

function portal_data(): array
{
    static $portal = null;
    if ($portal === null) {
        $portal = require PROJECT_ROOT . '/data/portal.php';
    }
    return $portal;
}

function runtime_store_path(): string
{
    return PROJECT_ROOT . '/data/runtime/payment-state.json';
}

function runtime_store(): array
{
    $path = runtime_store_path();
    if (!is_file($path)) {
        return ['purchases' => [], 'transactions' => []];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded)) {
        return ['purchases' => [], 'transactions' => []];
    }

    return [
        'purchases' => is_array($decoded['purchases'] ?? null) ? $decoded['purchases'] : [],
        'transactions' => is_array($decoded['transactions'] ?? null) ? $decoded['transactions'] : [],
    ];
}

function save_runtime_store(array $store): void
{
    $path = runtime_store_path();
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $handle = fopen($path, 'c+');
    if ($handle === false) {
        throw new RuntimeException('No fue posible abrir el almacenamiento temporal de pagos.');
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('No fue posible bloquear el almacenamiento temporal de pagos.');
        }
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
}

function courses(): array
{
    return catalog()['courses'] ?? [];
}

function categories(): array
{
    return catalog()['categories'] ?? [];
}

function base_enrolled_courses(): array
{
    return catalog()['enrolledCourses'] ?? [];
}

function completed_purchases(): array
{
    $sessionPurchases = is_array($_SESSION['completed_purchases'] ?? null)
        ? $_SESSION['completed_purchases']
        : [];

    $email = text_lower(trim(user_email()));
    if ($email === '') {
        return $sessionPurchases;
    }

    $store = runtime_store();
    $persisted = is_array($store['purchases'][$email] ?? null)
        ? $store['purchases'][$email]
        : [];

    return array_replace($persisted, $sessionPurchases);
}

function student_owns_course(string $slug): bool
{
    foreach (base_enrolled_courses() as $course) {
        if (($course['slug'] ?? '') === $slug) {
            return true;
        }
    }

    return isset(completed_purchases()[$slug]);
}

function enrolled_courses(): array
{
    $enrolled = base_enrolled_courses();
    $ownedSlugs = array_column($enrolled, 'slug');

    foreach (completed_purchases() as $slug => $purchase) {
        if (in_array($slug, $ownedSlugs, true)) {
            continue;
        }

        $course = course_by_slug($slug);
        if (!$course) {
            continue;
        }

        $course['progress'] = 0;
        $course['nextLesson'] = 'Introducción y mapa del curso';
        $course['purchasedAt'] = $purchase['date'] ?? date('Y-m-d');
        $enrolled[] = $course;
        $ownedSlugs[] = $slug;
    }

    return $enrolled;
}

function course_by_slug(string $slug): ?array
{
    foreach (courses() as $course) {
        if (($course['slug'] ?? '') === $slug) {
            return $course;
        }
    }
    return null;
}

function category_by_id(string $categoryId): ?array
{
    foreach (categories() as $category) {
        if (($category['id'] ?? '') === $categoryId) {
            return $category;
        }
    }
    return null;
}

function course_price_info(array $course): array
{
    $raw = trim((string) ($course['price'] ?? ''));
    if ($raw === '' || text_lower($raw) === 'gratis') {
        return [
            'free' => true,
            'amount' => '0.00',
            'currency' => null,
            'label' => $raw !== '' ? $raw : 'Gratis',
        ];
    }

    $amount = null;
    if (preg_match('/([0-9]+(?:[.,][0-9]{1,2})?)/', $raw, $match)) {
        $amount = (float) str_replace(',', '.', $match[1]);
    }

    $currency = 'USD';
    if (preg_match('/\b([A-Z]{3})\b/', strtoupper($raw), $match)) {
        $currency = $match[1];
    }

    return [
        'free' => $amount === null || $amount <= 0,
        'amount' => number_format(max(0, (float) $amount), 2, '.', ''),
        'currency' => $currency,
        'label' => $raw,
    ];
}

function text_lower(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function text_first_upper(string $value): string
{
    if ($value === '') {
        return '';
    }
    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return strtoupper(substr($value, 0, 1));
}

function text_contains_ci(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }
    return function_exists('mb_stripos')
        ? mb_stripos($haystack, $needle, 0, 'UTF-8') !== false
        : stripos($haystack, $needle) !== false;
}

function is_authenticated(): bool
{
    return isset($_SESSION['usuario_id']);
}

function user_role(): ?string
{
    return $_SESSION['usuario_rol'] ?? null;
}

function user_name(): string
{
    return $_SESSION['usuario_nombre'] ?? 'Usuario AulaGo';
}

function user_email(): string
{
    return $_SESSION['usuario_correo'] ?? '';
}

function user_initials(): string
{
    $parts = preg_split('/\s+/u', trim(user_name())) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        if ($part !== '') {
            $initials .= text_first_upper($part);
        }
    }
    return $initials !== '' ? $initials : 'AG';
}

function role_home_url(): string
{
    return match (user_role()) {
        'instructor' => url('instructor/'),
        'administrador' => url('admin/'),
        default => url('estudiante/'),
    };
}

function role_account_url(): string
{
    return role_home_url() . '#cuenta';
}

function require_role(string $role): void
{
    if (!is_authenticated()) {
        $redirect = $_SERVER['REQUEST_URI'] ?? role_home_url();
        header('Location: ' . url('login.php?redirect=' . rawurlencode($redirect)));
        exit;
    }

    if (user_role() !== $role) {
        header('Location: ' . role_home_url());
        exit;
    }
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    return is_string($sessionToken) && is_string($token) && $token !== '' && hash_equals($sessionToken, $token);
}

function record_completed_purchase(
    array $course,
    string $provider,
    string $orderId,
    string $amount,
    ?string $currency,
    string $status = 'COMPLETED'
): void {
    $slug = (string) ($course['slug'] ?? '');
    if ($slug === '') {
        return;
    }

    $purchase = [
        'slug' => $slug,
        'course' => $course['title'] ?? $slug,
        'provider' => $provider,
        'order_id' => $orderId,
        'amount' => $amount,
        'currency' => $currency,
        'status' => $status,
        'date' => date('Y-m-d'),
        'student' => user_name(),
        'student_email' => user_email(),
    ];

    $_SESSION['completed_purchases'] ??= [];
    $_SESSION['completed_purchases'][$slug] = $purchase;

    $email = text_lower(trim(user_email()));
    if ($email !== '') {
        $store = runtime_store();
        $store['purchases'][$email] ??= [];
        $store['purchases'][$email][$slug] = $purchase;

        if ((float) $amount > 0) {
            $alreadyStored = false;
            foreach ($store['transactions'] as $transaction) {
                if (($transaction['order_id'] ?? '') === $orderId) {
                    $alreadyStored = true;
                    break;
                }
            }

            if (!$alreadyStored) {
                array_unshift($store['transactions'], [
                    'id' => 'PAY-' . substr($orderId, -8),
                    'order_id' => $orderId,
                    'courseSlug' => $slug,
                    'course' => $course['title'] ?? $slug,
                    'student' => user_name(),
                    'student_email' => user_email(),
                    'date' => date('Y-m-d'),
                    'amount' => (float) $amount,
                    'currency' => $currency ?? 'USD',
                    'provider' => $provider,
                    'status' => $status,
                ]);
            }
        }

        save_runtime_store($store);
    }
}

function sales_transactions(): array
{
    $base = portal_data()['salesTransactions'] ?? [];
    $normalized = array_map(static function (array $sale): array {
        $sale['currency'] = $sale['currency'] ?? 'MXN';
        $sale['provider'] = $sale['provider'] ?? 'AulaGo demo';
        $sale['status'] = $sale['status'] ?? 'COMPLETED';
        $sale['order_id'] = $sale['order_id'] ?? ($sale['id'] ?? '');
        return $sale;
    }, $base);

    $store = runtime_store();
    $runtimeTransactions = $store['transactions'] ?? [];

    return array_values(array_merge($runtimeTransactions, $normalized));
}

function format_transaction_amount(array $transaction): string
{
    $amount = (float) ($transaction['amount'] ?? 0);
    $currency = strtoupper((string) ($transaction['currency'] ?? 'MXN'));
    return '$' . number_format($amount, 2, '.', ',') . ' ' . $currency;
}

function transaction_totals_by_currency(array $transactions): array
{
    $totals = [];
    foreach ($transactions as $transaction) {
        $currency = strtoupper((string) ($transaction['currency'] ?? 'MXN'));
        $totals[$currency] = ($totals[$currency] ?? 0.0) + (float) ($transaction['amount'] ?? 0);
    }
    return $totals;
}

function format_transaction_totals(array $transactions): string
{
    $totals = transaction_totals_by_currency($transactions);
    if (!$totals) {
        return '$0.00';
    }

    $parts = [];
    foreach ($totals as $currency => $amount) {
        $parts[] = '$' . number_format($amount, 2, '.', ',') . ' ' . $currency;
    }
    return implode(' · ', $parts);
}

function format_number(int|float|string $number): string
{
    return number_format((float) $number, 0, '.', ',');
}

function format_money(int|float|string $amount): string
{
    return '$' . number_format((float) $amount, 0, '.', ',') . ' MXN';
}

function format_date_es(string $date): string
{
    $timestamp = strtotime($date);
    if (!$timestamp) {
        return $date;
    }

    $months = [1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    $month = $months[(int) date('n', $timestamp)] ?? '';
    return (int) date('j', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
}

function safe_redirect_target(?string $target, string $fallback): string
{
    $target = trim((string) $target);
    if ($target === '' || str_contains($target, '://') || str_starts_with($target, '//')) {
        return $fallback;
    }

    if (str_starts_with($target, BASE_URL)) {
        return $target;
    }

    if (str_starts_with($target, '/')) {
        return $target;
    }

    return $fallback;
}
