<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/EstudiantePortal.php';
require_once PROJECT_ROOT . '/controllers/EstudiantePortalController.php';

require_role('estudiante');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('estudiante/#resenas'));
    exit;
}

if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['estudiante_portal_mensaje'] = 'La solicitud no es válida.';
    header('Location: ' . url('estudiante/#resenas'));
    exit;
}

try {
    $config = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($config);
    $modelo = new EstudiantePortal($conexion->obtenerConexion());
    $controlador = new EstudiantePortalController($modelo);

    $resultado = $controlador->guardarResena(
        (int) ($_SESSION['usuario_id'] ?? 0),
        $_POST
    );

    $_SESSION['estudiante_portal_mensaje'] = $resultado['mensaje'];
} catch (Throwable $error) {
    error_log($error->getMessage());
    $_SESSION['estudiante_portal_mensaje'] = 'No fue posible guardar la reseña.';
}

header('Location: ' . url('estudiante/#resenas'));
exit;
