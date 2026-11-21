<?php

declare(strict_types=1);

class PerfilController
{
    private Usuario $modeloUsuario;

    public function __construct(Usuario $modeloUsuario)
    {
        $this->modeloUsuario = $modeloUsuario;
    }

    public function actualizar(int $idUsuario, array $datos, ?string $avatar = null): array
    {
        $nombre = trim((string) ($datos['nombre'] ?? ''));
        $email = strtolower(trim((string) ($datos['email'] ?? '')));
        $genero = trim((string) ($datos['genero'] ?? ''));
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 150) {
            return ['exito' => false, 'mensaje' => 'El nombre debe tener entre 1 y 150 caracteres.'];
        }

        if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['exito' => false, 'mensaje' => 'El correo electrónico no es válido.'];
        }

        $generosPermitidos = ['mujer', 'hombre', 'otro', 'prefiero_no_decir'];
        if ($genero !== '' && !in_array($genero, $generosPermitidos, true)) {
            return ['exito' => false, 'mensaje' => 'El género seleccionado no es válido.'];
        }

        $usuarioActual = $this->modeloUsuario->buscarPorId($idUsuario);
        if ($usuarioActual === null) {
            return ['exito' => false, 'mensaje' => 'La cuenta no existe.'];
        }

        $usuarioConCorreo = $this->modeloUsuario->buscarPorEmail($email);
        if ($usuarioConCorreo !== null && (int) $usuarioConCorreo['id_usuario'] !== $idUsuario) {
            return ['exito' => false, 'mensaje' => 'Ese correo pertenece a otra cuenta.'];
        }

        $fechaNacimiento = (string) ($usuarioActual['fecha_nacimiento'] ?? '');
        $avatarFinal = $avatar ?? ($usuarioActual['avatar'] ?: null);

        $this->modeloUsuario->actualizarDatos(
            $idUsuario,
            $nombre,
            $email,
            $genero !== '' ? $genero : null,
            $fechaNacimiento !== '' ? $fechaNacimiento : null,
            $avatarFinal
        );

        return [
            'exito' => true,
            'mensaje' => 'Perfil actualizado correctamente.',
            'nombre' => $nombre,
            'email' => $email,
            'genero' => $genero,
            'fecha_nacimiento' => $fechaNacimiento,
            'avatar' => $avatarFinal
        ];
    }

}