<?php
require_once dirname(__DIR__) . '/config/app.php';

$email = trim((string) ($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . url('recuperar-contrasena.php?error=' . rawurlencode('Ingresa un correo electrónico válido.')));
    exit;
}

header('Location: ' . url('recuperar-contrasena.php?sent=1'));
exit;
