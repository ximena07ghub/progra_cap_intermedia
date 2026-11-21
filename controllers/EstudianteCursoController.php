<?php

declare(strict_types=1);

class EstudianteCursoController
{
    public function __construct(private EstudianteCurso $modelo)
    {
    }

    public function inscribirGratis(int $idEstudiante, array $curso): array
    {
        if ((float) ($curso['amount'] ?? 0) > 0) {
            return ['exito' => false, 'mensaje' => 'Este curso requiere pago.'];
        }

        $id = $this->modelo->inscribir($idEstudiante, (int) $curso['databaseId']);
        return ['exito' => $id > 0, 'mensaje' => $id > 0 ? 'Inscripción completada.' : 'No fue posible completar la inscripción.'];
    }

    public function confirmarPago(int $idEstudiante, array $curso, string $referencia, float $monto, string $moneda): void
    {
        $idCurso = (int) $curso['databaseId'];
        $idInscripcion = $this->modelo->inscribir($idEstudiante, $idCurso);
        $this->modelo->registrarPago($idEstudiante, $idCurso, $idInscripcion, 'PayPal', $referencia, $monto, $moneda);
    }

    public function actualizarProgreso(int $idEstudiante, array $datos): array
    {
        $idCurso = filter_var($datos['id_curso'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $idLeccion = filter_var($datos['id_leccion'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idCurso === false || $idLeccion === false) {
            return ['exito' => false, 'mensaje' => 'Curso o lección inválidos.'];
        }

        $completada = ($datos['completada'] ?? '') === '1';
        $ok = $this->modelo->actualizarLeccion($idEstudiante, (int) $idCurso, (int) $idLeccion, $completada);
        return ['exito' => $ok, 'mensaje' => $ok ? 'Progreso actualizado.' : 'No fue posible actualizar el progreso.'];
    }
}
