<?php

declare(strict_types=1);

class EstudiantePortal
{
    public function __construct(private PDO $conexion)
    {
    }

    public function listarKardex(int $idEstudiante): array
    {
        $sql = "SELECT
                    i.id_inscripcion,
                    i.id_curso,
                    i.estado,
                    i.fecha_inscripcion,
                    i.fecha_finalizacion,
                    cu.titulo AS curso,
                    cu.slug,
                    ca.nombre AS categoria,
                    u.nombre AS instructor,
                    ce.codigo AS certificado,
                    ce.fecha_emision
                FROM inscripciones i
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN categorias ca ON ca.id_categoria = cu.id_categoria
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                LEFT JOIN certificados ce ON ce.id_inscripcion = i.id_inscripcion
                WHERE i.id_estudiante = :estudiante
                  AND i.estado IN ('activa', 'completada')
                ORDER BY
                    CASE WHEN i.estado = 'completada' THEN 0 ELSE 1 END,
                    COALESCE(i.fecha_finalizacion, i.fecha_inscripcion) DESC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCertificados(int $idEstudiante): array
    {
        $sql = "SELECT
                    ce.codigo,
                    ce.fecha_emision,
                    i.fecha_finalizacion,
                    cu.id_curso,
                    cu.titulo AS curso,
                    cu.slug,
                    ca.nombre AS categoria,
                    u.nombre AS instructor
                FROM certificados ce
                INNER JOIN inscripciones i ON i.id_inscripcion = ce.id_inscripcion
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN categorias ca ON ca.id_categoria = cu.id_categoria
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                WHERE i.id_estudiante = :estudiante
                  AND i.estado = 'completada'
                ORDER BY ce.fecha_emision DESC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarCertificado(int $idEstudiante, string $codigo): ?array
    {
        $sql = "SELECT
                    ce.codigo,
                    ce.fecha_emision,
                    i.fecha_finalizacion,
                    cu.titulo AS curso,
                    ca.nombre AS categoria,
                    u.nombre AS instructor,
                    e.nombre AS estudiante
                FROM certificados ce
                INNER JOIN inscripciones i ON i.id_inscripcion = ce.id_inscripcion
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN categorias ca ON ca.id_categoria = cu.id_categoria
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                INNER JOIN usuarios e ON e.id_usuario = i.id_estudiante
                WHERE i.id_estudiante = :estudiante
                  AND ce.codigo = :codigo
                  AND i.estado = 'completada'
                LIMIT 1";

        $q = $this->conexion->prepare($sql);
        $q->execute([
            'estudiante' => $idEstudiante,
            'codigo' => $codigo,
        ]);

        $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function listarConversaciones(int $idEstudiante): array
    {
        $sql = "SELECT
                    c.id_conversacion,
                    c.id_curso,
                    cu.titulo AS curso,
                    u.nombre AS instructor
                FROM conversaciones c
                INNER JOIN cursos cu ON cu.id_curso = c.id_curso
                INNER JOIN usuarios u ON u.id_usuario = c.id_instructor
                WHERE c.id_estudiante = :estudiante
                ORDER BY c.fecha_creacion DESC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);
        $conversaciones = $q->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conversaciones as &$conversacion) {
            $conversacion['mensajes'] = $this->listarMensajes(
                (int) $conversacion['id_conversacion'],
                $idEstudiante
            );
        }
        unset($conversacion);

        return $conversaciones;
    }

    private function listarMensajes(int $idConversacion, int $idEstudiante): array
    {
        $sql = "SELECT
                    m.id_mensaje,
                    m.id_remitente,
                    m.contenido,
                    m.fecha_envio,
                    u.nombre AS remitente
                FROM mensajes m
                INNER JOIN usuarios u ON u.id_usuario = m.id_remitente
                WHERE m.id_conversacion = :conversacion
                ORDER BY m.fecha_envio ASC, m.id_mensaje ASC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['conversacion' => $idConversacion]);

        $mensajes = [];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $mensajes[] = [
                'id' => (int) $fila['id_mensaje'],
                'from' => (int) $fila['id_remitente'] === $idEstudiante ? 'me' : 'other',
                'text' => (string) $fila['contenido'],
                'date' => substr((string) $fila['fecha_envio'], 0, 10),
                'time' => substr((string) $fila['fecha_envio'], 11, 5),
            ];
        }

        return $mensajes;
    }

    public function cursosParaMensajes(int $idEstudiante): array
    {
        $sql = "SELECT
                    cu.id_curso,
                    cu.titulo,
                    cu.id_instructor,
                    u.nombre AS instructor
                FROM inscripciones i
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                WHERE i.id_estudiante = :estudiante
                  AND i.estado IN ('activa', 'completada')
                ORDER BY cu.titulo ASC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enviarMensaje(int $idEstudiante, int $idCurso, string $contenido): bool
    {
        $sql = "SELECT cu.id_instructor
                FROM inscripciones i
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                WHERE i.id_estudiante = :estudiante
                  AND i.id_curso = :curso
                  AND i.estado IN ('activa', 'completada')
                LIMIT 1";

        $q = $this->conexion->prepare($sql);
        $q->execute([
            'estudiante' => $idEstudiante,
            'curso' => $idCurso,
        ]);

        $idInstructor = $q->fetchColumn();
        if ($idInstructor === false) {
            return false;
        }

        $this->conexion->beginTransaction();

        try {
            $sqlConversacion = "INSERT INTO conversaciones (
                                    id_estudiante,
                                    id_instructor,
                                    id_curso
                                ) VALUES (
                                    :estudiante,
                                    :instructor,
                                    :curso
                                )
                                ON DUPLICATE KEY UPDATE
                                    id_conversacion = LAST_INSERT_ID(id_conversacion)";

            $q = $this->conexion->prepare($sqlConversacion);
            $q->execute([
                'estudiante' => $idEstudiante,
                'instructor' => (int) $idInstructor,
                'curso' => $idCurso,
            ]);

            $idConversacion = (int) $this->conexion->lastInsertId();

            $sqlMensaje = "INSERT INTO mensajes (
                                id_conversacion,
                                id_remitente,
                                contenido
                            ) VALUES (
                                :conversacion,
                                :remitente,
                                :contenido
                            )";

            $q = $this->conexion->prepare($sqlMensaje);
            $q->execute([
                'conversacion' => $idConversacion,
                'remitente' => $idEstudiante,
                'contenido' => $contenido,
            ]);

            $this->conexion->commit();
            return true;
        } catch (Throwable $error) {
            $this->conexion->rollBack();
            throw $error;
        }
    }

    public function cursosParaResena(int $idEstudiante): array
    {
        $sql = "SELECT
                    cu.id_curso,
                    cu.titulo AS curso,
                    cu.slug,
                    u.nombre AS instructor,
                    i.fecha_finalizacion,
                    r.calificacion,
                    r.comentario,
                    r.fecha_creacion
                FROM inscripciones i
                INNER JOIN cursos cu ON cu.id_curso = i.id_curso
                INNER JOIN usuarios u ON u.id_usuario = cu.id_instructor
                LEFT JOIN resenas r
                    ON r.id_estudiante = i.id_estudiante
                    AND r.id_curso = i.id_curso
                WHERE i.id_estudiante = :estudiante
                  AND i.estado = 'completada'
                ORDER BY i.fecha_finalizacion DESC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['estudiante' => $idEstudiante]);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarResena(
        int $idEstudiante,
        int $idCurso,
        int $calificacion,
        string $comentario
    ): bool {
        $sql = "SELECT id_inscripcion
                FROM inscripciones
                WHERE id_estudiante = :estudiante
                  AND id_curso = :curso
                  AND estado = 'completada'
                LIMIT 1";

        $q = $this->conexion->prepare($sql);
        $q->execute([
            'estudiante' => $idEstudiante,
            'curso' => $idCurso,
        ]);

        if ($q->fetchColumn() === false) {
            return false;
        }

        $sql = "INSERT INTO resenas (
                    id_estudiante,
                    id_curso,
                    calificacion,
                    comentario,
                    estado
                ) VALUES (
                    :estudiante,
                    :curso,
                    :calificacion,
                    :comentario,
                    'visible'
                )
                ON DUPLICATE KEY UPDATE
                    calificacion = VALUES(calificacion),
                    comentario = VALUES(comentario),
                    estado = 'visible',
                    fecha_creacion = CURRENT_TIMESTAMP";

        $q = $this->conexion->prepare($sql);

        return $q->execute([
            'estudiante' => $idEstudiante,
            'curso' => $idCurso,
            'calificacion' => $calificacion,
            'comentario' => $comentario !== '' ? $comentario : null,
        ]);
    }
}
