<?php

declare(strict_types=1);

class PerfilController
{
    private Usuario $modeloUsuario;

    public function __construct(Usuario $modeloUsuario)
    {
        $this->modeloUsuario = $modeloUsuario;
    }

    public function actualizar(int $idUsuario, array $datos): array
    {
        if (
            !isset($datos['nombre'], $datos['email'])
            || !is_string($datos['nombre'])
            || !is_string($datos['email'])
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Completa el nombre y el correo.'
            ];
        }

        $nombre = trim($datos['nombre']);
        $email = strtolower(trim($datos['email']));

        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 150) {
            return [
                'exito' => false,
                'mensaje' => 'El nombre debe tener entre 1 y 150 caracteres.'
            ];
        }

        if (
            strlen($email) > 255
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return [
                'exito' => false,
                'mensaje' => 'El correo electrónico no es válido.'
            ];
        }

        $usuarioActual = $this->modeloUsuario->buscarPorId($idUsuario);

        if ($usuarioActual === null) {
            return [
                'exito' => false,
                'mensaje' => 'La cuenta no existe.'
            ];
        }

        $usuarioConCorreo = $this->modeloUsuario->buscarPorEmail($email);

        if (
            $usuarioConCorreo !== null
            && (int) $usuarioConCorreo['id_usuario'] !== $idUsuario
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Ese correo pertenece a otra cuenta.'
            ];
        }

        $this->modeloUsuario->actualizarDatos(
            $idUsuario,
            $nombre,
            $email
        );

        return [
            'exito' => true,
            'mensaje' => 'Perfil actualizado correctamente.',
            'nombre' => $nombre,
            'email' => $email
        ];
    }
}