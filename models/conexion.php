<?php

declare(strict_types=1);

class Conexion
{
    private PDO $conexion;

    public function __construct(array $configuracion)
    {
        $dsn = 'mysql:host=' . $configuracion['host']
            . ';port=' . $configuracion['puerto']
            . ';dbname=' . $configuracion['base_datos']
            . ';charset=' . $configuracion['charset'];

        $this->conexion = new PDO(
            $dsn,
            $configuracion['usuario'],
            $configuracion['contrasena'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public function obtenerConexion(): PDO
    {
        return $this->conexion;
    }
}