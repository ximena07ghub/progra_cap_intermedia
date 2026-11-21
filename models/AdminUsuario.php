<?php

declare(strict_types=1);

class AdminUsuario
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $sql = '
            SELECT
                u.id_usuario,
                u.nombre AS name,
                u.email,
                r.nombre AS role,
                CASE
                    WHEN u.bloqueo_admin = 1
                        THEN \'Bloqueado por administrador\'
                    WHEN u.bloqueado = 1
                        THEN \'Bloqueado por intentos\'
                    ELSE \'Activo\'
                END AS status
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON u.id_rol = r.id_rol
            ORDER BY
                u.fecha_registro DESC,
                u.id_usuario DESC
        ';

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(
        int $idUsuario
    ): ?array {
        $sql = '
            SELECT
                u.id_usuario,
                u.nombre,
                u.email,
                r.nombre AS rol,
                u.bloqueado,
                u.bloqueo_admin,
                u.intentos_fallidos
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON u.id_rol = r.id_rol
            WHERE u.id_usuario = :id_usuario
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function bloquear(
        int $idUsuario
    ): bool {
        $sql = '
            UPDATE usuarios
            SET bloqueo_admin = 1
            WHERE id_usuario = :id_usuario
              AND id_rol IN (
                  SELECT id_rol
                  FROM roles
                  WHERE nombre IN (
                      \'estudiante\',
                      \'instructor\'
                  )
              )
              AND bloqueo_admin = 0
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        return $consulta->rowCount() === 1;
    }

    public function desbloquear(
        int $idUsuario
    ): bool {
        $sql = '
            UPDATE usuarios
            SET
                bloqueo_admin = 0,
                bloqueado = 0,
                intentos_fallidos = 0
            WHERE id_usuario = :id_usuario
              AND id_rol IN (
                  SELECT id_rol
                  FROM roles
                  WHERE nombre IN (
                      \'estudiante\',
                      \'instructor\'
                  )
              )
              AND (
                  bloqueo_admin = 1
                  OR bloqueado = 1
                  OR intentos_fallidos > 0
              )
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        return $consulta->rowCount() === 1;
    }
}