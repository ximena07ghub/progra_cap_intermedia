<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/InstructorMensaje.php';
require_once PROJECT_ROOT . '/controllers/InstructorMensajeController.php';

require_role('instructor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('instructor/#mensajes'));
    exit;
}

if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['instructor_mensaje'] = 'La solicitud no es válida.';
    header('Location: ' . url('instructor/#mensajes'));
    exit;
}

try {
    $config = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($config);
    $modelo = new InstructorMensaje($conexion->obtenerConexion());
    $controlador = new InstructorMensajeController($modelo);

    $resultado = $controlador->responder(
        (int) ($_SESSION['usuario_id'] ?? 0),
        $_POST
    );

    $_SESSION['instructor_mensaje'] = $resultado['mensaje'];
} catch (Throwable $error) {
    error_log($error->getMessage());
    $_SESSION['instructor_mensaje'] = 'No fue posible enviar la respuesta.';
}

header('Location: ' . url('instructor/#mensajes'));
exit;
