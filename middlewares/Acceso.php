<?php

declare(strict_types=1);

class Acceso
{
    private Usuario $modeloUsuario;

    public function __construct(Usuario $modeloUsuario)
    {
        $this->modeloUsuario = $modeloUsuario;
    }

    public function exigirRol(string $rolPermitido): array //rebibe el rol y devulve
    {
        if (!isset($_SESSION['usuario_id'])) { //comrpueba si hay sesion autentific

            throw new RuntimeException(
                'Debes iniciar sesión.',
                401
            );
        }

        $usuario = $this->modeloUsuario->buscarPorId(
            (int) $_SESSION['usuario_id'] //consulta el id de sesion
        );

        if ($usuario === null || (int) $usuario['bloqueado'] === 1) {
            unset(
                $_SESSION['usuario_id'],
                $_SESSION['usuario_rol'],
                $_SESSION['usuario_nombre'],
                $_SESSION['usuario_correo']
            );

            throw new RuntimeException(
                'La sesión ya no es válida.',
                401
            );
        }

        if ($usuario['rol'] !== $rolPermitido) {
            throw new RuntimeException(
                'No tienes permiso para esta operación.',
                403
            );
        }

        return $usuario;
    }
}