<?php

declare(strict_types=1);

class EstudianteCurso
{
    public function __construct(private PDO $conexion)
    {
    }

    public function buscarInscripcion(int $idEstudiante, int $idCurso): ?array
    {
        $sql = "SELECT id_inscripcion, id_estudiante, id_curso, estado, fecha_inscripcion, fecha_finalizacion
                FROM inscripciones
                WHERE id_estudiante = :id_estudiante AND id_curso = :id_curso
                LIMIT 1";
        $q = $this->conexion->prepare($sql);
        $q->execute(['id_estudiante' => $idEstudiante, 'id_curso' => $idCurso]);
        $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function inscribir(int $idEstudiante, int $idCurso): int
    {
        $existente = $this->buscarInscripcion($idEstudiante, $idCurso);
        if ($existente !== null) {
            if ($existente['estado'] === 'cancelada') {
                $q = $this->conexion->prepare("UPDATE inscripciones SET estado = 'activa', fecha_finalizacion = NULL WHERE id_inscripcion = :id");
                $q->execute(['id' => $existente['id_inscripcion']]);
            }
            return (int) $existente['id_inscripcion'];
        }

        $q = $this->conexion->prepare("INSERT INTO inscripciones (id_estudiante, id_curso, estado) VALUES (:estudiante, :curso, 'activa')");
        $q->execute(['estudiante' => $idEstudiante, 'curso' => $idCurso]);
        return (int) $this->conexion->lastInsertId();
    }

    public function registrarPago(int $idEstudiante, int $idCurso, int $idInscripcion, string $metodo, string $referencia, float $monto, string $moneda): void
    {
        $sql = "INSERT INTO pagos (id_estudiante, id_curso, id_inscripcion, metodo, referencia_externa, monto, moneda, estado)
                VALUES (:estudiante, :curso, :inscripcion, :metodo, :referencia, :monto, :moneda, 'completado')
                ON DUPLICATE KEY UPDATE id_inscripcion = VALUES(id_inscripcion), estado = 'completado'";
        $q = $this->conexion->prepare($sql);
        $q->execute([
            'estudiante' => $idEstudiante, 'curso' => $idCurso, 'inscripcion' => $idInscripcion,
            'metodo' => $metodo, 'referencia' => $referencia, 'monto' => $monto, 'moneda' => $moneda
        ]);
    }

    public function listarCursos(int $idEstudiante): array
    {
        $sql = "SELECT cu.id_curso, cu.slug, cu.titulo, cu.descripcion, cu.imagen, cu.precio, cu.moneda,
                       ca.nombre AS categoria, u.nombre AS instructor, i.id_inscripcion, i.estado,
                       i.fecha_inscripcion, i.fecha_finalizacion,
                       COUNT(DISTINCT l.id_leccion) AS total_lecciones,
                       COUNT(DISTINCT CASE WHEN p.completada = 1 THEN l.id_leccion END) AS completadas
                FROM inscripciones i
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN categorias ca ON ca.id_categoria = cu.id_categoria
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                LEFT JOIN niveles n ON n.id_curso = cu.id_curso AND n.activo = 1
                LEFT JOIN lecciones l ON l.id_nivel = n.id_nivel AND l.activo = 1
                LEFT JOIN progreso_lecciones p ON p.id_inscripcion = i.id_inscripcion AND p.id_leccion = l.id_leccion
                WHERE i.id_estudiante = :estudiante AND i.estado IN ('activa','completada')
                GROUP BY cu.id_curso, cu.slug, cu.titulo, cu.descripcion, cu.imagen, cu.precio, cu.moneda,
                         ca.nombre, u.nombre, i.id_inscripcion, i.estado, i.fecha_inscripcion, i.fecha_finalizacion
                ORDER BY i.fecha_inscripcion DESC";
        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarLecciones(int $idEstudiante, int $idCurso): array
    {
        $inscripcion = $this->buscarInscripcion($idEstudiante, $idCurso);
        if ($inscripcion === null || $inscripcion['estado'] === 'cancelada') {
            return [];
        }

        $sql = "SELECT n.id_nivel, n.titulo AS nivel, n.descripcion AS descripcion_nivel, n.orden AS orden_nivel,
                       l.id_leccion, l.titulo, l.contenido, l.url_video, l.orden,
                       COALESCE(p.completada, 0) AS completada, p.fecha_completada
                FROM niveles n
                INNER JOIN lecciones l ON l.id_nivel = n.id_nivel AND l.activo = 1
                LEFT JOIN progreso_lecciones p ON p.id_inscripcion = :inscripcion AND p.id_leccion = l.id_leccion
                WHERE n.id_curso = :curso AND n.activo = 1
                ORDER BY n.orden, l.orden";
        $q = $this->conexion->prepare($sql);
        $q->execute(['inscripcion' => $inscripcion['id_inscripcion'], 'curso' => $idCurso]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarLeccion(int $idEstudiante, int $idCurso, int $idLeccion, bool $completada): bool
    {
        $inscripcion = $this->buscarInscripcion($idEstudiante, $idCurso);
        if ($inscripcion === null || $inscripcion['estado'] === 'cancelada') {
            return false;
        }

        $q = $this->conexion->prepare("SELECT l.id_leccion FROM lecciones l INNER JOIN niveles n ON n.id_nivel = l.id_nivel WHERE l.id_leccion = :leccion AND n.id_curso = :curso AND l.activo = 1 AND n.activo = 1 LIMIT 1");
        $q->execute(['leccion' => $idLeccion, 'curso' => $idCurso]);
        if (!$q->fetchColumn()) {
            return false;
        }

        $sql = "INSERT INTO progreso_lecciones (id_inscripcion, id_leccion, completada, fecha_completada)
                VALUES (:inscripcion, :leccion, :completada, :fecha)
                ON DUPLICATE KEY UPDATE completada = VALUES(completada), fecha_completada = VALUES(fecha_completada)";
        $q = $this->conexion->prepare($sql);
        $q->execute([
            'inscripcion' => $inscripcion['id_inscripcion'], 'leccion' => $idLeccion,
            'completada' => $completada ? 1 : 0, 'fecha' => $completada ? date('Y-m-d H:i:s') : null
        ]);

        $this->actualizarEstadoCurso((int) $inscripcion['id_inscripcion'], $idCurso);
        return true;
    }

    private function actualizarEstadoCurso(int $idInscripcion, int $idCurso): void
    {
        $q = $this->conexion->prepare("SELECT COUNT(*) FROM lecciones l INNER JOIN niveles n ON n.id_nivel = l.id_nivel WHERE n.id_curso = :curso AND n.activo = 1 AND l.activo = 1");
        $q->execute(['curso' => $idCurso]);
        $total = (int) $q->fetchColumn();

        $q = $this->conexion->prepare("SELECT COUNT(*) FROM progreso_lecciones p INNER JOIN lecciones l ON l.id_leccion = p.id_leccion INNER JOIN niveles n ON n.id_nivel = l.id_nivel WHERE p.id_inscripcion = :inscripcion AND n.id_curso = :curso AND n.activo = 1 AND l.activo = 1 AND p.completada = 1");
        $q->execute(['inscripcion' => $idInscripcion, 'curso' => $idCurso]);
        $hechas = (int) $q->fetchColumn();

        $completo = $total > 0 && $hechas >= $total;
        $q = $this->conexion->prepare("UPDATE inscripciones SET estado = :estado, fecha_finalizacion = :fecha WHERE id_inscripcion = :id");
        $q->execute(['estado' => $completo ? 'completada' : 'activa', 'fecha' => $completo ? date('Y-m-d H:i:s') : null, 'id' => $idInscripcion]);

        if ($completo) {
            $codigo = 'AULAGO-' . strtoupper(substr(hash('sha256', $idInscripcion . '|' . $idCurso), 0, 16));
            $q = $this->conexion->prepare("INSERT IGNORE INTO certificados (id_inscripcion, codigo) VALUES (:inscripcion, :codigo)");
            $q->execute(['inscripcion' => $idInscripcion, 'codigo' => $codigo]);
        }
    }
}
