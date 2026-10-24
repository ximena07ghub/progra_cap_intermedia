<?php

declare(strict_types=1);

class RegistroController
{
    private Usuario $modeloUsuario;
    private Rol $modeloRol; //se guardan los modelos que necesita el controlador 

    public function __construct( //se reciben los modelos 
        Usuario $modeloUsuario,
        Rol $modeloRol
    ) {
        $this->modeloUsuario = $modeloUsuario;
        $this->modeloRol = $modeloRol;
    }

    public function registrar(array $datos, array $archivos): array //recibimos los arreglos
    {
        $nombre = trim((string) ($datos['nombre'] ?? ''));
        $email = strtolower(trim((string) ($datos['email'] ?? '')));
        $fechaNacimiento = trim((string) ($datos['fecha_nacimiento'] ?? ''));
        $genero = trim((string) ($datos['genero'] ?? ''));
        $nombreRol = trim((string) ($datos['rol'] ?? ''));
        $password = (string) ($datos['password'] ?? '');
        $confirmacion = (string) ($datos['password_confirmation'] ?? '');

        if (
            $nombre === ''
            || $email === ''
            || $fechaNacimiento === ''
            || $genero === ''
            || $nombreRol === ''
            || $password === ''
            || $confirmacion === ''
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Todos los campos son obligatorios.'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'exito' => false,
                'mensaje' => 'El correo electrónico no es válido.'
            ];
        }

        $generosPermitidos = [
            'mujer',
            'hombre',
            'otro',
            'prefiero_no_decir'
        ];

        if (!in_array($genero, $generosPermitidos, true)) {
            return [
                'exito' => false,
                'mensaje' => 'El género seleccionado no es válido.'
            ];
        }

        $rolesPermitidos = [
            'estudiante',
            'instructor'
        ];

        if (!in_array($nombreRol, $rolesPermitidos, true)) {
            return [
                'exito' => false,
                'mensaje' => 'El rol seleccionado no está permitido.'
            ];
        }

        $fecha = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
        $hoy = new DateTime('today');

        if (
            $fecha === false
            || $fecha->format('Y-m-d') !== $fechaNacimiento
            || $fecha > $hoy
        ) {
            return [
                'exito' => false,
                'mensaje' => 'La fecha de nacimiento no es válida.'
            ];
        }

        $passwordValida =
            strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);

        if (!$passwordValida) {
            return [
                'exito' => false,
                'mensaje' => 'La contraseña no cumple los requisitos.'
            ];
        }

        if ($password !== $confirmacion) {
            return [
                'exito' => false,
                'mensaje' => 'Las contraseñas no coinciden.'
            ];
        }

        if ($this->modeloUsuario->buscarPorEmail($email) !== null) {
            return [
                'exito' => false,
                'mensaje' => 'Ya existe una cuenta con ese correo.'
            ];
        }

        $idRol = $this->modeloRol->buscarIdPorNombre($nombreRol);

        if ($idRol === null) {
            return [
                'exito' => false,
                'mensaje' => 'No fue posible identificar el rol.'
            ];
        }

        $avatar = $archivos['avatar'] ?? null;

        if (
            !is_array($avatar)
            || ($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Debes seleccionar una imagen de perfil.'
            ];
        }


        $detector = new finfo(FILEINFO_MIME_TYPE);
        $tipoImagen = $detector->file($avatar['tmp_name']);

        $extensionesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];

        if (!isset($extensionesPermitidas[$tipoImagen])) {
            return [
                'exito' => false,
                'mensaje' => 'La imagen debe ser JPG o PNG.'
            ];
        }

        $directorio = PROJECT_ROOT . '/public/uploads/avatars';

        if (!is_dir($directorio) && !mkdir($directorio, 0775, true)) {
            return [
                'exito' => false,
                'mensaje' => 'No fue posible preparar la carpeta de imágenes.'
            ];
        }

        $extension = $extensionesPermitidas[$tipoImagen];
        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $extension;
        $destinoCompleto = $directorio . '/' . $nombreArchivo;
        $rutaAvatar = 'public/uploads/avatars/' . $nombreArchivo;

        if (!move_uploaded_file($avatar['tmp_name'], $destinoCompleto)) {
            return [
                'exito' => false,
                'mensaje' => 'No fue posible guardar la imagen.'
            ];
        }

        $contrasenaHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $idUsuario = $this->modeloUsuario->registrar( //el controller entrega datos y usuario ejecuta el insert
                $idRol,
                $nombre,
                $genero,
                $fechaNacimiento,
                $rutaAvatar,
                $email,
                $contrasenaHash
            );

            return [
                'exito' => true,
                'mensaje' => 'La cuenta fue registrada correctamente.',
                'id_usuario' => $idUsuario
            ];
        } catch (PDOException $error) {
            if (is_file($destinoCompleto)) {
                unlink($destinoCompleto);
            }

            error_log($error->getMessage());

            return [
                'exito' => false,
                'mensaje' => 'No fue posible registrar la cuenta.'
            ];
        }
    }
}