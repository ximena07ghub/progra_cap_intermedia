<?php

declare(strict_types=1);

class LoginController
{
    private Usuario $modeloUsuario;

    public function __construct(Usuario $modeloUsuario)
    {
        $this->modeloUsuario = $modeloUsuario;
    }

    public function ingresar(array $datos): array
    {
        if (
            !isset($datos['email'], $datos['password'])
            || !is_string($datos['email'])
            || !is_string($datos['password'])
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Completa el correo y la contraseña.'
            ];
        }

        $email = strtolower(trim($datos['email']));
        $password = $datos['password'];

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $password === ''
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Revisa el correo y la contraseña.'
            ];
        }

        $usuario = $this->modeloUsuario->buscarPorEmail($email);

        if ($usuario === null) {
            return [
                'exito' => false,
                'mensaje' => 'Correo o contraseña incorrectos.'
            ];
        }

        if ((int) $usuario['bloqueado'] === 1) {
            return [
                'exito' => false,
                'mensaje' => 'La cuenta está bloqueada. Contacta al administrador.'
            ];
        }

        if (!password_verify($password, $usuario['contrasena_hash'])) {
            $this->modeloUsuario->registrarFallo(
                (int) $usuario['id_usuario']
            );

            return [
                'exito' => false,
                'mensaje' => 'Correo o contraseña incorrectos. Al tercer fallo se bloquea la cuenta.'
            ];
        }

        if (!in_array($usuario['rol'], ['estudiante', 'instructor'], true)) {
            return [
                'exito' => false,
                'mensaje' => 'Esta cuenta no utiliza el acceso público.'
            ];
        }

        $this->modeloUsuario->reiniciarIntentos(
            (int) $usuario['id_usuario']
        );

        return [
            'exito' => true,
            'usuario' => $usuario
        ];
    }
}