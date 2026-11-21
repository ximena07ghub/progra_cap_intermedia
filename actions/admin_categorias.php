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
    exit(
        'Solicitud inválida. Actualiza la página e inténtalo nuevamente.'
    );
}

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/AdminCategoria.php';
require_once PROJECT_ROOT . '/controllers/AdminCategoriaController.php';

$accion = $_POST['accion'] ?? '';

if (!is_string($accion)) {
    http_response_code(400);
    exit('Acción inválida.');
}

try {
    $configuracion = require PROJECT_ROOT
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);

    $modelo = new AdminCategoria(
        $conexion->obtenerConexion()
    );

    $controlador = new AdminCategoriaController(
        $modelo
    );

    if ($accion === 'crear') {
        $resultado = $controlador->crear($_POST);

    } elseif (
        $accion === 'activar'
        || $accion === 'desactivar'
        || $accion === 'eliminar'
    ) {
        $idCategoria = filter_var(
            $_POST['id_categoria'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        if ($idCategoria === false) {
            http_response_code(400);
            exit('Categoría inválida.');
        }

        if ($accion === 'eliminar') {
            $resultado = $controlador->eliminar(
                $idCategoria
            );
        } else {
            $resultado = $controlador->cambiarEstado(
                $idCategoria,
                $accion
            );
        }

    } else {
        http_response_code(400);
        exit('Acción no permitida.');
    }

    $_SESSION['admin_categorias_mensaje'] =
        $resultado['mensaje'];

    header(
        'Location: '
        . url('admin/')
        . '#categorias'
    );

    exit;

} catch (Throwable $error) {
    error_log($error->getMessage());

    $_SESSION['admin_categorias_mensaje'] =
        'No fue posible completar la operación.';

    header(
        'Location: '
        . url('admin/')
        . '#categorias'
    );

    exit;
}