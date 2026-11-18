<?php

declare(strict_types=1);

//cargamos los archivos antes creados 

require_once dirname(__DIR__) . '/config/app.php'; //funciones y config del portal
require_once PROJECT_ROOT . '/models/conexion.php'; //abre conexion pdo
require_once PROJECT_ROOT . '/models/usuario.php'; //consulta y registro
require_once PROJECT_ROOT . '/models/rol.php'; //id de rol
require_once PROJECT_ROOT . '/controllers/registroController.php'; //valida regitro

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('registro.php'));
    exit;
}



try {

    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $objetoConexion = new Conexion($configuracion);
    $pdo = $objetoConexion->obtenerConexion();

    $modeloUsuario = new Usuario($pdo);
    $modeloRol = new Rol($pdo);

    $controlador = new RegistroController( //creamos controlador y se cargan los modelos
        $modeloUsuario,
        $modeloRol
    );

    $resultado = $controlador->registrar(
        $_POST, //nombre, correo, fecha, género, rol y contra
        $_FILES //imagenes subidas
    );
} catch (Throwable $error) {
    error_log($error->getMessage());

    $resultado = [
        'exito' => false,
        'mensaje' => 'Ocurrió un error al procesar el registro.'
    ];
}

$redirect = trim((string) ($_POST['redirect'] ?? ''));

if (!$resultado['exito']) {
    $parametros = [
        'error' => $resultado['mensaje']
    ];

    if ($redirect !== '') {
        $parametros['redirect'] = $redirect;
    }

    header(
        'Location: '
        . url('registro.php?')
        . http_build_query($parametros)
    );

    exit;
}

$parametros = [
    'registered' => '1'
];

if ($redirect !== '') {
    $parametros['redirect'] = $redirect;
}

header(
    'Location: '
    . url('login.php?')
    . http_build_query($parametros)
);

exit;