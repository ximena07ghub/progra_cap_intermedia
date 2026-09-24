<?php
require_once dirname(__DIR__) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('registro.php'));
    exit;
}

$name = trim((string) ($_POST['nombre'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$birthdate = trim((string) ($_POST['fecha_nacimiento'] ?? ''));
$gender = trim((string) ($_POST['genero'] ?? ''));
$role = trim((string) ($_POST['rol'] ?? 'estudiante'));
$password = (string) ($_POST['password'] ?? '');
$confirmation = (string) ($_POST['password_confirmation'] ?? '');

$validRole = in_array($role, ['estudiante', 'instructor'], true);
$passwordOk = strlen($password) >= 8 && preg_match('/[A-Z]/', $password) && preg_match('/\d/', $password) && preg_match('/[^A-Za-z0-9]/', $password);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $birthdate === '' || $gender === '' || !$validRole || !$passwordOk || $password !== $confirmation) {
    header('Location: ' . url('registro.php?error=' . rawurlencode('Hay información pendiente o inválida. Revisa los campos antes de continuar.')));
    exit;
}

header('Location: ' . url('login.php?registered=1&role=' . rawurlencode($role)));
exit;
