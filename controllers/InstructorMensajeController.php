<?php

declare(strict_types=1);

class InstructorMensajeController
{
    public function __construct(private InstructorMensaje $modelo)
    {
    }

    public function responder(int $idInstructor, array $datos): array
    {
        $idConversacion = filter_var(
            $datos['id_conversacion'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $contenido = trim((string) ($datos['contenido'] ?? ''));

        if ($idConversacion === false) {
            return ['exito' => false, 'mensaje' => 'Conversación inválida.'];
        }

        if ($contenido === '' || strlen($contenido) > 2000) {
            return ['exito' => false, 'mensaje' => 'Escribe un mensaje de hasta 2000 caracteres.'];
        }

        $ok = $this->modelo->responder(
            $idInstructor,
            (int) $idConversacion,
            $contenido
        );

        return [
            'exito' => $ok,
            'mensaje' => $ok
                ? 'Respuesta enviada correctamente.'
                : 'No fue posible enviar la respuesta.',
        ];
    }
}
