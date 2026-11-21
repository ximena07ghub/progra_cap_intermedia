<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Acceso no permitido.');
}

require_once dirname(__DIR__) . '/models/conexion.php';
require_once dirname(__DIR__) . '/models/usuario.php';
require_once dirname(__DIR__) . '/models/rol.php';

function preguntar(string $mensaje): string
{
    echo $mensaje;

    $respuesta = fgets(STDIN);

    if ($respuesta === false) {
        throw new RuntimeException(
            'No se pudo leer el dato.'
        );
    }

    return trim($respuesta);
}

function preguntarContrasena(string $mensaje): string
{
    echo $mensaje;

    if (PHP_OS_FAMILY === 'Windows') {
        $script = '$s = Read-Host -AsSecureString; '
            . '$p = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($s); '
            . 'try { '
            . '[Console]::WriteLine('
            . '[Runtime.InteropServices.Marshal]::PtrToStringBSTR($p)) '
            . '} finally { '
            . '[Runtime.InteropServices.Marshal]::ZeroFreeBSTR($p) '
            . '}';

        $comando = 'powershell.exe -NoProfile -Command '
            . escapeshellarg($script);

        $proceso = popen($comando, 'r');

        if ($proceso === false) {
            throw new RuntimeException(
                'No se pudo iniciar la lectura segura.'
            );
        }

        $respuesta = stream_get_contents($proceso);
        $estado = pclose($proceso);

        echo PHP_EOL;

        if ($estado !== 0 || $respuesta === false) {
            throw new RuntimeException(
                'No se pudo leer la contraseña de forma oculta.'
            );
        }

        return rtrim($respuesta, "\r\n");
    }

    system('stty -echo');

    try {
        $respuesta = fgets(STDIN);
    } finally {
        system('stty echo');
        echo PHP_EOL;
    }

    if ($respuesta === false) {
        throw new RuntimeException(
            'No se pudo leer la contraseña.'
        );
    }

    return rtrim($respuesta, "\r\n");
}

try {
    echo PHP_EOL;
    echo '=== Crear administrador ===' . PHP_EOL;
    echo PHP_EOL;

    $nombre = preguntar('Nombre completo: ');
    $correo = strtolower(
        preguntar('Correo electrónico: ')
    );

    if ($nombre === '' || strlen($nombre) > 150) {
        throw new RuntimeException(
            'El nombre no es válido.'
        );
    }

    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || strlen($correo) > 255
    ) {
        throw new RuntimeException(
            'El correo no es válido.'
        );
    }

    $configuracion = require dirname(__DIR__)
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);
    $pdo = $conexion->obtenerConexion();

    $modeloUsuario = new Usuario($pdo);
    $modeloRol = new Rol($pdo);

    if ($modeloUsuario->buscarPorEmail($correo) !== null) {
        throw new RuntimeException(
            'Ya existe una cuenta con ese correo.'
        );
    }

    $idRol = $modeloRol->buscarIdPorNombre(
        'administrador'
    );

    if ($idRol === null) {
        throw new RuntimeException(
            'No existe el rol administrador en MySQL.'
        );
    }

    $contrasena = preguntarContrasena(
        'Contraseña: '
    );

    if (strlen($contrasena) < 8) {
        throw new RuntimeException(
            'La contraseña debe tener al menos 8 caracteres.'
        );
    }

    $confirmacion = preguntarContrasena(
        'Confirmar contraseña: '
    );

    if ($contrasena !== $confirmacion) {
        throw new RuntimeException(
            'Las contraseñas no coinciden.'
        );
    }

    $hash = password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );

    unset($contrasena, $confirmacion);

    $idAdministrador = $modeloUsuario->crearAdministrador(
        $idRol,
        $nombre,
        $correo,
        $hash
    );

    echo PHP_EOL;
    echo 'Administrador creado correctamente.' . PHP_EOL;
    echo 'ID: ' . $idAdministrador . PHP_EOL;
    echo 'Correo: ' . $correo . PHP_EOL;
} catch (Throwable $error) {
    fwrite(
        STDERR,
        PHP_EOL
        . 'Error: '
        . $error->getMessage()
        . PHP_EOL
    );

    exit(1);
}