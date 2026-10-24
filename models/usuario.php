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

    public function registrarFallo(int $idUsuario): void
    {
        $sql = '
            update usuarios
            set
                bloqueado = case
                    when intentos_fallidos >= 2 then 1
                    else bloqueado
                end,
                intentos_fallidos = least(intentos_fallidos + 1, 3)
            where id_usuario = :id_usuario
        ';
    
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_usuario' => $idUsuario]);
    }
    
    public function reiniciarIntentos(int $idUsuario): void
    {
        $sql = '
            update usuarios
            set intentos_fallidos = 0
            where id_usuario = :id_usuario
        ';
    
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_usuario' => $idUsuario]);
    }


            public function buscarPorId(int $idUsuario): ?array
            {
                $sql = '
                    select
                        usuarios.id_usuario,
                        usuarios.nombre,
                        usuarios.email,
                        usuarios.genero,
                        usuarios.fecha_nacimiento,
                        usuarios.avatar,
                        usuarios.bloqueado,
                        roles.nombre as rol
                    from usuarios
                    inner join roles
                        on usuarios.id_rol = roles.id_rol
                    where usuarios.id_usuario = :id_usuario
                    limit 1
                ';
            
                $consulta = $this->conexion->prepare($sql);
            
                $consulta->execute([
                    'id_usuario' => $idUsuario
                ]);
            
                $usuario = $consulta->fetch();
            
                return $usuario ?: null;
            }

        public function actualizarDatos(
            int $idUsuario,
            string $nombre,
            string $email
        ): void {
            $sql = '
                update usuarios
                set
                    nombre = :nombre,
                    email = :email
                where id_usuario = :id_usuario
            ';

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                'nombre' => $nombre,
                'email' => $email,
                'id_usuario' => $idUsuario
            ]);
        }

}