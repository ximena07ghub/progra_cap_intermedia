<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/EstudianteCurso.php';
require_once PROJECT_ROOT . '/controllers/EstudianteCursoController.php';

require_role('estudiante');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    http_response_code(400);
    header('Location: ' . url('estudiante/'));
    exit;
}

$idEstudiante = (int) ($_SESSION['usuario_id'] ?? 0);
$idCurso = (int) ($_POST['id_curso'] ?? 0);

try {
    $config = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($config);
    $modelo = new EstudianteCurso($conexion->obtenerConexion());
    $controlador = new EstudianteCursoController($modelo);
    $resultado = $controlador->actualizarProgreso($idEstudiante, $_POST);
    $_SESSION['estudiante_curso_mensaje'] = $resultado['mensaje'];
} catch (Throwable $error) {
    error_log($error->getMessage());
    $_SESSION['estudiante_curso_mensaje'] = 'No fue posible actualizar el progreso.';
}

$slug = trim((string) ($_POST['slug'] ?? ''));
header('Location: ' . url('curso.php?slug=' . rawurlencode($slug) . '&lesson=' . $idCurso));
exit;
