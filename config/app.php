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

function courses(): array
{
    return catalog()['courses'] ?? [];
}

function categories(): array
{
    return catalog()['categories'] ?? [];
}

function enrolled_courses(): array
{
    return catalog()['enrolledCourses'] ?? [];
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
