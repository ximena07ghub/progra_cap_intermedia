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

function public_catalog_model(): Catalogo
{
    static $modelo = null;

    if ($modelo instanceof Catalogo) {
        return $modelo;
    }

    require_once PROJECT_ROOT . '/models/conexion.php';
    require_once PROJECT_ROOT . '/models/Catalogo.php';

    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($configuracion);

    $modelo = new Catalogo(
        $conexion->obtenerConexion()
    );

    return $modelo;
}

function courses(): array
{
    return public_catalog_model()->listarCursosPublicados();
}

function categories(): array
{
    return public_catalog_model()->listarCategoriasActivas();
}

function course_image_url(array $course): string
{
    $image = ltrim((string) ($course['image'] ?? ''), '/');

    if ($image === '') {
        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="750" viewBox="0 0 1200 750"><rect width="1200" height="750" fill="#1b171c"/><text x="600" y="375" text-anchor="middle" dominant-baseline="middle" fill="#9ca3af" font-family="Arial" font-size="42">AulaGo</text></svg>'
        );
    }

    if (str_starts_with($image, 'uploads/')) {
        return asset($image);
    }

    return asset('images/' . $image);
}

function public_course_lessons(int $idCurso): array
{
    return public_catalog_model()->listarLecciones($idCurso);
}

function public_course_reviews(int $idCurso): array
{
    return public_catalog_model()->listarResenas($idCurso);
}

function student_course_model(): EstudianteCurso
{
    static $modelo = null;

    if ($modelo instanceof EstudianteCurso) {
        return $modelo;
    }

    require_once PROJECT_ROOT . '/models/conexion.php';
    require_once PROJECT_ROOT . '/models/EstudianteCurso.php';

    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($configuracion);
    $modelo = new EstudianteCurso($conexion->obtenerConexion());

    return $modelo;
}

function student_owns_course(string $slug): bool
{
    if (!is_authenticated() || user_role() !== 'estudiante') {
        return false;
    }

    foreach (enrolled_courses() as $curso) {
        if (($curso['slug'] ?? '') === $slug) {
            return true;
        }
    }

    return false;
}

function enrolled_courses(): array
{
    if (!is_authenticated() || user_role() !== 'estudiante') {
        return [];
    }

    $filas = student_course_model()->listarCursos((int) ($_SESSION['usuario_id'] ?? 0));
    $cursos = [];

    foreach ($filas as $fila) {
        $total = (int) $fila['total_lecciones'];
        $completadas = (int) $fila['completadas'];
        $progreso = $total > 0 ? (int) round(($completadas / $total) * 100) : 0;
        $lecciones = student_course_model()->listarLecciones((int) $_SESSION['usuario_id'], (int) $fila['id_curso']);
        $siguiente = 'Curso sin lecciones';

        foreach ($lecciones as $leccion) {
            if ((int) $leccion['completada'] !== 1) {
                $siguiente = (string) $leccion['titulo'];
                break;
            }
        }

        if ($progreso === 100) {
            $siguiente = 'Curso completado';
        }

        $cursos[] = [
            'id' => (int) $fila['id_curso'],
            'databaseId' => (int) $fila['id_curso'],
            'slug' => (string) $fila['slug'],
            'title' => (string) $fila['titulo'],
            'description' => (string) $fila['descripcion'],
            'image' => (string) ($fila['imagen'] ?? ''),
            'price' => (float) $fila['precio'] <= 0 ? 'Gratis' : '$' . number_format((float) $fila['precio'], 2, '.', ',') . ' ' . $fila['moneda'],
            'amount' => (float) $fila['precio'],
            'currency' => (string) $fila['moneda'],
            'category' => (string) $fila['categoria'],
            'instructor' => (string) $fila['instructor'],
            'level' => 'Curso inscrito',
            'duration' => $total . ($total === 1 ? ' lección' : ' lecciones'),
            'lessons' => $total,
            'students' => 0,
            'reviews' => 0,
            'rating' => 0,
            'accent' => (string) $fila['descripcion'],
            'progress' => $progreso,
            'completedLessons' => $completadas,
            'nextLesson' => $siguiente,
            'enrollmentStatus' => (string) $fila['estado'],
            'purchasedAt' => (string) $fila['fecha_inscripcion'],
        ];
    }

    return $cursos;
}

function student_course_lessons(int $idCurso): array
{
    if (!is_authenticated() || user_role() !== 'estudiante') {
        return [];
    }

    return student_course_model()->listarLecciones(
        (int) ($_SESSION['usuario_id'] ?? 0),
        $idCurso
    );
}

function course_by_slug(string $slug): ?array
{
    $slug = trim($slug);

    if ($slug === '') {
        return null;
    }

    return public_catalog_model()->buscarCursoPorSlug($slug);
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
    if (isset($course['amount'], $course['currency'])) {
        $amount = max(0, (float) $course['amount']);
        $currency = strtoupper((string) $course['currency']);

        return [
            'free' => $amount <= 0,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => $currency,
            'label' => $amount <= 0
                ? 'Gratis'
                : '$' . number_format($amount, 2, '.', ',') . ' ' . $currency,
        ];
    }

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

    if (preg_match('/\\b([A-Z]{3})\\b/', strtoupper($raw), $match)) {
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
    require_once PROJECT_ROOT . '/models/conexion.php';
    require_once PROJECT_ROOT . '/models/usuario.php';
    require_once PROJECT_ROOT . '/middlewares/Acceso.php';

    try {
        $configuracion = require PROJECT_ROOT . '/config/database.local.php';

        $objetoConexion = new Conexion($configuracion);
        $modeloUsuario = new Usuario(
            $objetoConexion->obtenerConexion()
        );

        $acceso = new Acceso($modeloUsuario);
        $usuario = $acceso->exigirRol($role);

        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_correo'] = $usuario['email'];
    } 
    
    catch (Throwable $error)
    
    {
        if ($error->getCode() === 401) {
            header('Location: ' . url('login.php'));
            exit;
        }
        if ($error->getCode() === 403) {
            http_response_code(403);
            exit('No tienes permiso para acceder a esta página.');
        }

        error_log($error->getMessage());

        http_response_code(500);
        exit('No fue posible verificar el acceso.');
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
