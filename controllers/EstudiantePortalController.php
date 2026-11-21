<?php

declare(strict_types=1);

class EstudiantePortalController
{
    public function __construct(private EstudiantePortal $modelo)
    {
    }

    public function enviarMensaje(int $idEstudiante, array $datos): array
    {
        $idCurso = filter_var(
            $datos['id_curso'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $contenido = trim((string) ($datos['contenido'] ?? ''));

        if ($idCurso === false) {
            return ['exito' => false, 'mensaje' => 'Selecciona un curso válido.'];
        }

        if ($contenido === '' || strlen($contenido) > 2000) {
            return ['exito' => false, 'mensaje' => 'Escribe un mensaje de hasta 2000 caracteres.'];
        }

        $ok = $this->modelo->enviarMensaje(
            $idEstudiante,
            (int) $idCurso,
            $contenido
        );

        return [
            'exito' => $ok,
            'mensaje' => $ok
                ? 'Mensaje enviado correctamente.'
                : 'No fue posible enviar el mensaje.',
        ];
    }

    public function guardarResena(int $idEstudiante, array $datos): array
    {
        $idCurso = filter_var(
            $datos['id_curso'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $calificacion = filter_var(
            $datos['calificacion'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 5]]
        );

        $comentario = trim((string) ($datos['comentario'] ?? ''));

        if ($idCurso === false || $calificacion === false) {
            return ['exito' => false, 'mensaje' => 'Curso o calificación inválidos.'];
        }

        if (strlen($comentario) > 3000) {
            return ['exito' => false, 'mensaje' => 'La reseña no puede superar 3000 caracteres.'];
        }

        $ok = $this->modelo->guardarResena(
            $idEstudiante,
            (int) $idCurso,
            (int) $calificacion,
            $comentario
        );

        return [
            'exito' => $ok,
            'mensaje' => $ok
                ? 'Reseña guardada correctamente.'
                : 'Solo puedes reseñar cursos completados.',
        ];
    }
}
