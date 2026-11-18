<?php
declare(strict_types=1);

class AdminUsuarioController
{
    private AdminUsuario $modelo;

    public function __construct(AdminUsuario $modelo)
    {
        $this->modelo = $modelo;
    }

    public function cambiarBloqueo(
        int $idUsuario,
        int $idAdministrador,
        string $accion
    ): array {
        if ($idUsuario <= 0 || $idAdministrador <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'Identificador de usuario inválido.'
            ];
        }

        if (!in_array($accion, ['bloquear', 'desbloquear'], true)) {
            return [
                'exito' => false,
                'mensaje' => 'Acción no permitida.'
            ];
        }

        if ($idUsuario === $idAdministrador) {
            return [
                'exito' => false,
                'mensaje' => 'No puedes modificar el bloqueo de tu propia cuenta.'
            ];
        }

        $usuario = $this->modelo->buscarPorId($idUsuario);

        if ($usuario === null) {
            return [
                'exito' => false,
                'mensaje' => 'El usuario no existe.'
            ];
        }

        if (!in_array($usuario['rol'], ['estudiante', 'instructor'], true)) {
            return [
                'exito' => false,
                'mensaje' => 'No se permite bloquear ni desbloquear administradores.'
            ];
        }

        if ($accion === 'bloquear') {
            $realizado = $this->modelo->bloquear($idUsuario);

            return [
                'exito' => $realizado,
                'mensaje' => $realizado
                    ? 'Usuario bloqueado correctamente.'
                    : 'El usuario ya estaba bloqueado administrativamente.'
            ];
        }

        $realizado = $this->modelo->desbloquear($idUsuario);

        return [
            'exito' => $realizado,
            'mensaje' => $realizado
                ? 'Usuario desbloqueado correctamente.'
                : 'El usuario no tiene bloqueos pendientes.'
        ];
    }
}
