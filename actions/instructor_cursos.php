<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

require_role('instructor');

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Solicitud inválida.');
}

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/InstructorCurso.php';
require_once PROJECT_ROOT
    . '/controllers/InstructorCursoController.php';

$idInstructor =
    (int) ($_SESSION['usuario_id'] ?? 0);

$accion = is_string($_POST['accion'] ?? null)
    ? $_POST['accion']
    : '';

try {
    $configuracion = require PROJECT_ROOT
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);

    $modelo = new InstructorCurso(
        $conexion->obtenerConexion()
    );

    $controlador =
        new InstructorCursoController($modelo);

    if ($accion === 'crear') {
        $resultado = $controlador->crear(
            $idInstructor,
            $_POST,
            $_FILES['imagen'] ?? []
        );

        $destino = '#cursos';

    } else {
        $idCurso = filter_var(
            $_POST['id_curso'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        if ($idCurso === false) {
            http_response_code(400);
            exit('Curso inválido.');
        }

        if ($accion === 'actualizar') {
            $resultado = $controlador->actualizar(
                $idInstructor,
                (int) $idCurso,
                $_POST,
                $_FILES['imagen'] ?? []
            );

            $destino =
                '#editar-' . $idCurso;

        } elseif ($accion === 'publicar') {
            $resultado =
                $controlador->cambiarEstado(
                    $idInstructor,
                    (int) $idCurso,
                    'publicado'
                );

            $destino = '#cursos';

        } elseif ($accion === 'desactivar') {
            $resultado =
                $controlador->cambiarEstado(
                    $idInstructor,
                    (int) $idCurso,
                    'desactivado'
                );

            $destino = '#cursos';

        } elseif ($accion === 'borrador') {
            $resultado =
                $controlador->cambiarEstado(
                    $idInstructor,
                    (int) $idCurso,
                    'borrador'
                );

            $destino = '#cursos';

        } elseif ($accion === 'crear_nivel') {
            $resultado =
                $controlador->crearNivel(
                    $idInstructor,
                    (int) $idCurso,
                    $_POST
                );

            $destino =
                '#contenido-' . $idCurso;

        } elseif ($accion === 'crear_leccion') {
            $resultado =
                $controlador->crearLeccion(
                    $idInstructor,
                    (int) $idCurso,
                    $_POST
                );

            $destino =
                '#contenido-' . $idCurso;

        } elseif ($accion === 'eliminar_nivel') {
            $idNivel = filter_var(
                $_POST['id_nivel'] ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1
                    ]
                ]
            );

            if ($idNivel === false) {
                throw new RuntimeException(
                    'Nivel inválido.'
                );
            }

            $eliminado =
                $modelo->eliminarNivel(
                    (int) $idNivel,
                    (int) $idCurso,
                    $idInstructor
                );

            $resultado = [
                'exito' => $eliminado,
                'mensaje' => $eliminado
                    ? 'Nivel eliminado correctamente.'
                    : 'No fue posible eliminar el nivel.'
            ];

            $destino =
                '#contenido-' . $idCurso;

        } elseif ($accion === 'eliminar_leccion') {
            $idLeccion = filter_var(
                $_POST['id_leccion'] ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1
                    ]
                ]
            );

            if ($idLeccion === false) {
                throw new RuntimeException(
                    'Lección inválida.'
                );
            }

            $eliminada =
                $modelo->eliminarLeccion(
                    (int) $idLeccion,
                    (int) $idCurso,
                    $idInstructor
                );

            $resultado = [
                'exito' => $eliminada,
                'mensaje' => $eliminada
                    ? 'Lección eliminada correctamente.'
                    : 'No fue posible eliminar la lección.'
            ];

            $destino =
                '#contenido-' . $idCurso;

        } elseif ($accion === 'eliminar') {
            $resultado = $controlador->eliminar(
                $idInstructor,
                (int) $idCurso
            );

            $destino = '#cursos';

        } else {
            http_response_code(400);
            exit('Acción no permitida.');
        }
    }

    $_SESSION['instructor_cursos_mensaje'] =
        $resultado['mensaje'];

    header(
        'Location: '
        . url('instructor/')
        . $destino
    );

    exit;

} catch (Throwable $error) {
    error_log($error->getMessage());

    $_SESSION['instructor_cursos_mensaje'] =
        'No fue posible completar la operación.';

    header(
        'Location: '
        . url('instructor/')
        . '#cursos'
    );

    exit;
}