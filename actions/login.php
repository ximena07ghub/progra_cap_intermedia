<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/Conexion.php';
require_once PROJECT_ROOT . '/models/Usuario.php';
require_once PROJECT_ROOT . '/controllers/LoginController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('login.php'));
    exit;
}

try {
    $configuracion = require PROJECT_ROOT . '/config/database.local.php';

    $objetoConexion = new Conexion($configuracion);

    $modeloUsuario = new Usuario(
        $objetoConexion->obtenerConexion()
    );

    $controlador = new LoginController($modeloUsuario);
    $resultado = $controlador->ingresar($_POST);

    if ($resultado['exito']) {
        $usuario = $resultado['usuario'];

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int) $usuario['id_usuario'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_correo'] = $usuario['email'];

        header('Location: ' . role_home_url());
        exit;
    }
} catch (Throwable $error) {
    error_log($error->getMessage());

    $resultado = [
        'exito' => false,
        'mensaje' => 'No fue posible iniciar sesión. Intenta nuevamente.'
    ];
}

header(
    'Location: '
    . url('login.php?')
    . http_build_query(['error' => $resultado['mensaje']])
);

exit;