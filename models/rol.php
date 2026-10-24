<?php

declare(strict_types=1);

class Rol
{ 
    private PDO $conexion; //conexion que utiliza el modelo

    public function __construct(PDO $conexion) //recibe la conexion pdo cuando creamos el objeto
    {
        $this->conexion = $conexion;
    }

    public function buscarIdPorNombre(string $nombre): ?int //recibe el nombre del rol y devuelve el id si existe y null si no
    {
        $sql = '
            select id_rol
            from roles
            where nombre = :nombre
            limit 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'nombre' => $nombre
        ]);

        $rol = $consulta->fetch();

        return $rol ? (int) $rol['id_rol'] : null; // si mysql encuentra el rol, devuelve su conetenido si no devuelve null
    }
}