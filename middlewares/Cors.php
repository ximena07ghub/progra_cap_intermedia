<?php

declare(strict_types=1);

class Cors
{
    public static function aplicar(string $origenPermitido): void //statis nos permite llamar sin crear un objeto
    {
        $origen = $_SERVER['HTTP_ORIGIN'] ?? '';

        header('Vary: Origin');

        if ($origen !== '' && $origen !== $origenPermitido) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'exito' => false,
                'mensaje' => 'Origen no permitido.'
            ]);

            exit;
        }

        if ($origen === $origenPermitido) {
            header('Access-Control-Allow-Origin: ' . $origenPermitido);
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: GET, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}