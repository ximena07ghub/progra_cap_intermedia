<?php

declare(strict_types=1);

class Usuario
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = '
            select
                usuarios.id_usuario,
                usuarios.id_rol,
                roles.nombre as rol,
                usuarios.nombre,
                usuarios.genero,
                usuarios.fecha_nacimiento,
                usuarios.avatar,
                usuarios.email,
                usuarios.contrasena_hash,
                usuarios.intentos_fallidos,
                usuarios.bloqueado
            from usuarios
            inner join roles
                on usuarios.id_rol = roles.id_rol
            where usuarios.email = :email
            limit 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'email' => $email
        ]);

        $usuario = $consulta->fetch();

        return $usuario ?: null;
    }

    public function registrar(
        int $idRol,
        string $nombre,
        string $genero,
        string $fechaNacimiento,
        string $avatar,
        string $email,
        string $contrasenaHash
    ): int {
        $sql = '
            insert into usuarios (
                id_rol,
                nombre,
                genero,
                fecha_nacimiento,
                avatar,
                email,
                contrasena_hash
            )
            values (
                :id_rol,
                :nombre,
                :genero,
                :fecha_nacimiento,
                :avatar,
                :email,
                :contrasena_hash
            )
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_rol' => $idRol,
            'nombre' => $nombre,
            'genero' => $genero,
            'fecha_nacimiento' => $fechaNacimiento,
            'avatar' => $avatar,
            'email' => $email,
            'contrasena_hash' => $contrasenaHash
        ]);

        return (int) $this->conexion->lastInsertId();
    }
}