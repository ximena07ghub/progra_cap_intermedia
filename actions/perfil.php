<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/usuario.php';
require_once PROJECT_ROOT . '/controllers/PerfilController.php';

if (!is_authenticated() || !in_array(user_role(), ['estudiante', 'instructor'], true)) {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

$destino = user_role() === 'instructor'
    ? url('instructor/#cuenta')
    : url('estudiante/#cuenta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $destino);
    exit;
}

if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['perfil_mensaje'] = 'La solicitud no es válida.';
    $_SESSION['perfil_mensaje_tipo'] = 'error';
    header('Location: ' . $destino);
    exit;
}

$nuevoAvatar = null;
$archivoNuevo = null;

try {
    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($configuracion);
    $modelo = new Usuario($conexion->obtenerConexion());
    $usuarioActual = $modelo->buscarPorId((int) ($_SESSION['usuario_id'] ?? 0));

    if ($usuarioActual === null) {
        throw new RuntimeException('La cuenta no existe.');
    }

    $avatar = $_FILES['avatar'] ?? null;
    if (is_array($avatar) && ($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No fue posible recibir la imagen.');
        }

        if (($avatar['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new RuntimeException('La imagen no debe superar 3 MB.');
        }

        $detector = new finfo(FILEINFO_MIME_TYPE);
        $tipo = $detector->file((string) $avatar['tmp_name']);
        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!isset($permitidos[$tipo])) {
            throw new RuntimeException('La imagen debe ser JPG o PNG.');
        }

        $directorio = PROJECT_ROOT . '/public/uploads/avatars';
        if (!is_dir($directorio) && !mkdir($directorio, 0775, true)) {
            throw new RuntimeException('No fue posible preparar la carpeta de imágenes.');
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $permitidos[$tipo];
        $archivoNuevo = $directorio . '/' . $nombreArchivo;
        $nuevoAvatar = 'public/uploads/avatars/' . $nombreArchivo;

        if (!move_uploaded_file((string) $avatar['tmp_name'], $archivoNuevo)) {
            throw new RuntimeException('No fue posible guardar la imagen.');
        }
    }

    $controlador = new PerfilController($modelo);
    $resultado = $controlador->actualizar(
        (int) $_SESSION['usuario_id'],
        $_POST,
        $nuevoAvatar
    );

    if (!$resultado['exito']) {
        if ($archivoNuevo !== null && is_file($archivoNuevo)) {
            unlink($archivoNuevo);
        }
        $_SESSION['perfil_mensaje'] = $resultado['mensaje'];
        $_SESSION['perfil_mensaje_tipo'] = 'error';
        header('Location: ' . $destino);
        exit;
    }

    if ($nuevoAvatar !== null) {
        $avatarAnterior = (string) ($usuarioActual['avatar'] ?? '');
        if (
            str_starts_with($avatarAnterior, 'public/uploads/avatars/')
            && $avatarAnterior !== $nuevoAvatar
        ) {
            $rutaAnterior = PROJECT_ROOT . '/' . $avatarAnterior;
            if (is_file($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
    }

    $_SESSION['usuario_nombre'] = $resultado['nombre'];
    $_SESSION['usuario_correo'] = $resultado['email'];
    $_SESSION['usuario_avatar'] = $resultado['avatar'];

    $_SESSION['perfil_mensaje'] = $resultado['mensaje'];
    $_SESSION['perfil_mensaje_tipo'] = 'exito';
} catch (Throwable $error) {
    if ($archivoNuevo !== null && is_file($archivoNuevo)) {
        unlink($archivoNuevo);
    }

    error_log($error->getMessage());
    $_SESSION['perfil_mensaje'] = $error instanceof RuntimeException
        ? $error->getMessage()
        : 'No fue posible actualizar el perfil.';
    $_SESSION['perfil_mensaje_tipo'] = 'error';
}

header('Location: ' . $destino);
exit;
