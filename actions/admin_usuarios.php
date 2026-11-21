<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

require_role('administrador');

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Solicitud inválida. Actualiza la página e inténtalo nuevamente.');
}

$idUsuario = filter_var(
    $_POST['id_usuario'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$accion = $_POST['accion'] ?? '';

if ($idUsuario === false || !is_string($accion)) {
    http_response_code(400);
    exit('Datos inválidos.');
}

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/AdminUsuario.php';
require_once PROJECT_ROOT . '/controllers/AdminUsuarioController.php';

try {
    $configuracion = require PROJECT_ROOT . '/config/database.local.php';

    $conexion = new Conexion($configuracion);

    $modelo = new AdminUsuario(
        $conexion->obtenerConexion()
    );

    $controlador = new AdminUsuarioController($modelo);

    $resultado = $controlador->cambiarBloqueo(
        $idUsuario,
        (int) $_SESSION['usuario_id'],
        $accion
    );

    $_SESSION['admin_usuarios_mensaje'] = $resultado['mensaje'];

    header('Location: ' . url('admin/') . '#usuarios');
    exit;

} catch (Throwable $error) {
    error_log($error->getMessage());

    $_SESSION['admin_usuarios_mensaje'] =
        'No fue posible actualizar el usuario.';

    header('Location: ' . url('admin/') . '#usuarios');
    exit;
}
