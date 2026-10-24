<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/middlewares/Cors.php';

Cors::aplicar('http://localhost');

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/Conexion.php';
require_once PROJECT_ROOT . '/models/Usuario.php';
require_once PROJECT_ROOT . '/controllers/PerfilController.php';
require_once PROJECT_ROOT . '/middlewares/Acceso.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder(int $estado, array $contenido): void
{
    http_response_code($estado);

    echo json_encode(
        $contenido,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

try {
    $metodo = $_SERVER['REQUEST_METHOD'];

    if (!in_array($metodo, ['GET', 'PATCH'], true)) {
        header('Allow: GET, PATCH, OPTIONS');

        responder(405, [
            'exito' => false,
            'mensaje' => 'Método no permitido.'
        ]);
    }

    $configuracion = require PROJECT_ROOT . '/config/database.local.php';

    $objetoConexion = new Conexion($configuracion);
    $modeloUsuario = new Usuario(
        $objetoConexion->obtenerConexion()
    );

    $acceso = new Acceso($modeloUsuario);
    $usuario = $acceso->exigirRol('estudiante');

    if ($metodo === 'GET') {
        responder(200, [
            'exito' => true,
            'perfil' => [
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email']
            ],
            'csrf_token' => csrf_token()
        ]);
    }

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (!verify_csrf($token)) {
        responder(403, [
            'exito' => false,
            'mensaje' => 'La solicitud no es válida. Recarga la página.'
        ]);
    }

    $datos = json_decode(
        file_get_contents('php://input'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    if (!is_array($datos)) {
        responder(400, [
            'exito' => false,
            'mensaje' => 'El cuerpo debe ser un objeto JSON.'
        ]);
    }

    $controlador = new PerfilController($modeloUsuario);

    $resultado = $controlador->actualizar(
        (int) $usuario['id_usuario'],
        $datos
    );

    if (!$resultado['exito']) {
        responder(400, $resultado);
    }

    $_SESSION['usuario_nombre'] = $resultado['nombre'];
    $_SESSION['usuario_correo'] = $resultado['email'];

    responder(200, $resultado);
} catch (JsonException $error) {
    responder(400, [
        'exito' => false,
        'mensaje' => 'El JSON enviado no es válido.'
    ]);
} catch (PDOException $error) {
    error_log($error->getMessage());

    $duplicado = ($error->errorInfo[1] ?? null) === 1062;

    responder($duplicado ? 409 : 500, [
        'exito' => false,
        'mensaje' => $duplicado
            ? 'Ese correo ya está registrado.'
            : 'No fue posible procesar la solicitud.'
    ]);
} catch (Throwable $error) {
    $estado = (int) $error->getCode();

    if (!in_array($estado, [401, 403], true)) {
        error_log($error->getMessage());

        responder(500, [
            'exito' => false,
            'mensaje' => 'Ocurrió un error al procesar la solicitud.'
        ]);
    }

    responder($estado, [
        'exito' => false,
        'mensaje' => $error->getMessage()
    ]);
}