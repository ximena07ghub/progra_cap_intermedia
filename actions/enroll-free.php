<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/EstudianteCurso.php';
require_once PROJECT_ROOT . '/controllers/EstudianteCursoController.php';

require_role('estudiante');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('cursos.php'));
    exit;
}

$slug = trim((string) ($_POST['slug'] ?? ''));
$course = course_by_slug($slug);

if (!$course || !verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    header('Location: ' . url('cursos.php'));
    exit;
}

$price = course_price_info($course);
if (!$price['free']) {
    header('Location: ' . url('estudiante/checkout.php?slug=' . rawurlencode($slug)));
    exit;
}

try {
    $config = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($config);
    $modelo = new EstudianteCurso($conexion->obtenerConexion());
    $controlador = new EstudianteCursoController($modelo);
    $resultado = $controlador->inscribirGratis((int) $_SESSION['usuario_id'], $course);

    $_SESSION['estudiante_curso_mensaje'] = $resultado['mensaje'];
    $estado = $resultado['exito'] ? 'free' : 'error';
} catch (Throwable $error) {
    error_log($error->getMessage());
    $estado = 'error';
}

header('Location: ' . url('estudiante/?payment=' . $estado . '&course=' . rawurlencode($slug) . '#cursos'));
exit;
