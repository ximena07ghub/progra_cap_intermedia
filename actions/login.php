<?php
require_once dirname(__DIR__) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('login.php'));
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$requestedRole = trim((string) ($_POST['role'] ?? ''));
$redirect = trim((string) ($_POST['redirect'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || trim($password) === '') {
    header('Location: ' . url('login.php?error=' . rawurlencode('Revisa el correo y la contraseña antes de continuar.')));
    exit;
}

$allowedRoles = ['estudiante', 'instructor', 'administrador'];
if (!in_array($requestedRole, $allowedRoles, true)) {
    $normalized = text_lower($email);
    $requestedRole = str_starts_with($normalized, 'admin@')
        ? 'administrador'
        : (str_starts_with($normalized, 'instructor@') ? 'instructor' : 'estudiante');
}

$_SESSION['usuario_id'] = 1;
$_SESSION['usuario_rol'] = $requestedRole;
$_SESSION['usuario_correo'] = $email;
$_SESSION['usuario_nombre'] = match ($requestedRole) {
    'instructor' => 'Instructor AulaGo',
    'administrador' => 'Administrador AulaGo',
    default => 'Estudiante AulaGo',
};

$fallback = role_home_url();
header('Location: ' . safe_redirect_target($redirect, $fallback));
exit;
