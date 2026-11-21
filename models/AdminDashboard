<?php

declare(strict_types=1);

class AdminDashboard
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerMetricas(): array
    {
        return [
            'usuarios' => $this->contar('usuarios'),
            'categorias' => $this->contar('categorias'),
            'categorias_activas' => $this->contarActivos(
                'categorias',
                'activo'
            ),
            'cursos' => $this->contar('cursos'),
            'inscripciones' => $this->contar('inscripciones'),
            'pagos' => $this->contar('pagos'),
            'resenas' => $this->contar('resenas')
        ];
    }

    private function contar(string $tabla): int
    {
        $tablasPermitidas = [
            'usuarios',
            'categorias',
            'cursos',
            'inscripciones',
            'pagos',
            'resenas'
        ];

        if (!in_array($tabla, $tablasPermitidas, true)) {
            throw new InvalidArgumentException(
                'Tabla no permitida.'
            );
        }

        $consulta = $this->conexion->query(
            "SELECT COUNT(*) FROM {$tabla}"
        );

        return (int) $consulta->fetchColumn();
    }

    private function contarActivos(
        string $tabla,
        string $campo
    ): int {
        if (
            $tabla !== 'categorias'
            || $campo !== 'activo'
        ) {
            throw new InvalidArgumentException(
                'Consulta no permitida.'
            );
        }

        $consulta = $this->conexion->query(
            'SELECT COUNT(*)
             FROM categorias
             WHERE activo = 1'
        );

        return (int) $consulta->fetchColumn();
    }

    public function listarPagos(): array
    {
        $sql = '
            SELECT
                p.id_pago,
                p.metodo,
                p.referencia_externa,
                p.monto,
                p.moneda,
                p.estado,
                p.fecha_pago,
                u.nombre AS estudiante,
                cu.titulo AS curso
            FROM pagos AS p
            INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_estudiante
            INNER JOIN cursos AS cu
                ON cu.id_curso = p.id_curso
            ORDER BY p.fecha_pago DESC, p.id_pago DESC
        ';

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarResenas(): array
    {
        $sql = '
            SELECT
                r.id_resena,
                r.calificacion,
                r.comentario,
                r.estado,
                r.fecha_creacion,
                u.nombre AS estudiante,
                cu.titulo AS curso
            FROM resenas AS r
            INNER JOIN usuarios AS u
                ON u.id_usuario = r.id_estudiante
            INNER JOIN cursos AS cu
                ON cu.id_curso = r.id_curso
            ORDER BY r.fecha_creacion DESC, r.id_resena DESC
        ';

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}