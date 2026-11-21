<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/middlewares/Cors.php';

Cors::aplicar('http://localhost');

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/InstructorCurso.php';
require_once PROJECT_ROOT . '/controllers/InstructorCursoController.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responderCursos(int $estado, array $contenido): void
{
    http_response_code($estado);

    echo json_encode(
        $contenido,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

function leerJsonCursos(): array
{
    $contenido = file_get_contents('php://input');

    if ($contenido === false || trim($contenido) === '') {
        return [];
    }

    $datos = json_decode(
        $contenido,
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    if (!is_array($datos)) {
        responderCursos(400, [
            'exito' => false,
            'mensaje' => 'El cuerpo debe ser un objeto JSON.'
        ]);
    }

    return $datos;
}

function idCursoSolicitado(): ?int
{
    $id = $_GET['id'] ?? null;

    if ($id === null || $id === '') {
        return null;
    }

    $idValidado = filter_var(
        $id,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($idValidado === false) {
        responderCursos(400, [
            'exito' => false,
            'mensaje' => 'El identificador del curso no es válido.'
        ]);
    }

    return (int) $idValidado;
}

try {
    if (!is_authenticated()) {
        responderCursos(401, [
            'exito' => false,
            'mensaje' => 'Debes iniciar sesión.'
        ]);
    }

    if (user_role() !== 'instructor') {
        responderCursos(403, [
            'exito' => false,
            'mensaje' => 'Esta API está disponible para instructores.'
        ]);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $idCurso = idCursoSolicitado();

    if (!in_array($metodo, ['GET', 'POST', 'PATCH', 'DELETE'], true)) {
        header('Allow: GET, POST, PATCH, DELETE, OPTIONS');

        responderCursos(405, [
            'exito' => false,
            'mensaje' => 'Método no permitido.'
        ]);
    }

    if ($metodo === 'POST' && $idCurso !== null) {
        responderCursos(405, [
            'exito' => false,
            'mensaje' => 'POST solo está disponible sobre la colección de cursos.'
        ]);
    }

    if (in_array($metodo, ['PATCH', 'DELETE'], true) && $idCurso === null) {
        responderCursos(400, [
            'exito' => false,
            'mensaje' => 'Debes indicar el id del curso.'
        ]);
    }

    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($configuracion);
    $modelo = new InstructorCurso($conexion->obtenerConexion());
    $controlador = new InstructorCursoController($modelo);
    $idInstructor = (int) ($_SESSION['usuario_id'] ?? 0);

    if ($metodo === 'GET') {
        if ($idCurso === null) {
            responderCursos(200, [
                'exito' => true,
                'cursos' => $modelo->listarPorInstructor($idInstructor),
                'csrf_token' => csrf_token()
            ]);
        }

        $curso = $modelo->buscarPropio($idCurso, $idInstructor);

        if ($curso === null) {
            responderCursos(404, [
                'exito' => false,
                'mensaje' => 'El curso no existe o no te pertenece.'
            ]);
        }

        $curso['niveles'] = $modelo->listarNiveles(
            $idCurso,
            $idInstructor
        );

        responderCursos(200, [
            'exito' => true,
            'curso' => $curso,
            'csrf_token' => csrf_token()
        ]);
    }

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (!verify_csrf($token)) {
        responderCursos(403, [
            'exito' => false,
            'mensaje' => 'Token CSRF inválido. Consulta primero la API con GET.'
        ]);
    }

    if ($metodo === 'POST') {
        $datos = leerJsonCursos();

        $resultado = $controlador->crear(
            $idInstructor,
            $datos,
            []
        );

        if (!$resultado['exito']) {
            responderCursos(422, $resultado);
        }

        $curso = $modelo->buscarPropio(
            (int) $resultado['id_curso'],
            $idInstructor
        );

        responderCursos(201, [
            'exito' => true,
            'mensaje' => $resultado['mensaje'],
            'curso' => $curso
        ]);
    }

    $cursoActual = $modelo->buscarPropio(
        (int) $idCurso,
        $idInstructor
    );

    if ($cursoActual === null) {
        responderCursos(404, [
            'exito' => false,
            'mensaje' => 'El curso no existe o no te pertenece.'
        ]);
    }

    if ($metodo === 'PATCH') {
        $datos = leerJsonCursos();

        $datosCompletos = [
            'titulo' => $datos['titulo'] ?? $cursoActual['titulo'],
            'descripcion' => $datos['descripcion'] ?? $cursoActual['descripcion'],
            'id_categoria' => $datos['id_categoria'] ?? $cursoActual['id_categoria'],
            'precio' => $datos['precio'] ?? $cursoActual['precio']
        ];

        $resultado = $controlador->actualizar(
            $idInstructor,
            (int) $idCurso,
            $datosCompletos,
            []
        );

        if (!$resultado['exito']) {
            responderCursos(422, $resultado);
        }

        if (array_key_exists('estado', $datos)) {
            $resultadoEstado = $controlador->cambiarEstado(
                $idInstructor,
                (int) $idCurso,
                (string) $datos['estado']
            );

            if (!$resultadoEstado['exito']) {
                responderCursos(422, $resultadoEstado);
            }
        }

        responderCursos(200, [
            'exito' => true,
            'mensaje' => 'Curso actualizado correctamente.',
            'curso' => $modelo->buscarPropio(
                (int) $idCurso,
                $idInstructor
            )
        ]);
    }

    $resultado = $controlador->cambiarEstado(
        $idInstructor,
        (int) $idCurso,
        'desactivado'
    );

    if (!$resultado['exito']) {
        responderCursos(422, $resultado);
    }

    responderCursos(200, [
        'exito' => true,
        'mensaje' => 'Curso dado de baja correctamente.',
        'curso' => $modelo->buscarPropio(
            (int) $idCurso,
            $idInstructor
        )
    ]);
} catch (JsonException $error) {
    responderCursos(400, [
        'exito' => false,
        'mensaje' => 'El JSON enviado no es válido.'
    ]);
} catch (PDOException $error) {
    error_log($error->getMessage());

    responderCursos(500, [
        'exito' => false,
        'mensaje' => 'No fue posible procesar la solicitud.'
    ]);
} catch (Throwable $error) {
    error_log($error->getMessage());

    responderCursos(500, [
        'exito' => false,
        'mensaje' => 'Ocurrió un error al procesar la solicitud.'
    ]);
}
