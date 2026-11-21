<?php

declare(strict_types=1);

class InstructorMensaje
{
    public function __construct(private PDO $conexion)
    {
    }

    public function listarConversaciones(int $idInstructor): array
    {
        $sql = "SELECT
                    c.id_conversacion,
                    c.id_curso,
                    cu.titulo AS curso,
                    u.nombre AS estudiante,
                    u.avatar AS estudiante_avatar
                FROM conversaciones c
                INNER JOIN cursos cu ON cu.id_curso = c.id_curso
                INNER JOIN usuarios u ON u.id_usuario = c.id_estudiante
                WHERE c.id_instructor = :instructor
                ORDER BY c.fecha_creacion DESC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['instructor' => $idInstructor]);
        $conversaciones = $q->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conversaciones as &$conversacion) {
            $conversacion['mensajes'] = $this->listarMensajes(
                (int) $conversacion['id_conversacion'],
                $idInstructor
            );
        }
        unset($conversacion);

        return $conversaciones;
    }

    private function listarMensajes(int $idConversacion, int $idInstructor): array
    {
        $sql = "SELECT id_mensaje, id_remitente, contenido, fecha_envio
                FROM mensajes
                WHERE id_conversacion = :conversacion
                ORDER BY fecha_envio ASC, id_mensaje ASC";

        $q = $this->conexion->prepare($sql);
        $q->execute(['conversacion' => $idConversacion]);

        $mensajes = [];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $mensajes[] = [
                'id' => (int) $fila['id_mensaje'],
                'from' => (int) $fila['id_remitente'] === $idInstructor ? 'me' : 'other',
                'text' => (string) $fila['contenido'],
                'date' => substr((string) $fila['fecha_envio'], 0, 10),
                'time' => substr((string) $fila['fecha_envio'], 11, 5),
            ];
        }

        return $mensajes;
    }

    public function responder(int $idInstructor, int $idConversacion, string $contenido): bool
    {
        $sql = "SELECT id_conversacion
                FROM conversaciones
                WHERE id_conversacion = :conversacion
                  AND id_instructor = :instructor
                LIMIT 1";

        $q = $this->conexion->prepare($sql);
        $q->execute([
            'conversacion' => $idConversacion,
            'instructor' => $idInstructor,
        ]);

        if ($q->fetchColumn() === false) {
            return false;
        }

        $sql = "INSERT INTO mensajes (id_conversacion, id_remitente, contenido)
                VALUES (:conversacion, :remitente, :contenido)";

        $q = $this->conexion->prepare($sql);

        return $q->execute([
            'conversacion' => $idConversacion,
            'remitente' => $idInstructor,
            'contenido' => $contenido,
        ]);
    }
}
