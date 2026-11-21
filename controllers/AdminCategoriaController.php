<?php

declare(strict_types=1);

class AdminCategoriaController
{
    private AdminCategoria $modelo;

    public function __construct(AdminCategoria $modelo)
    {
        $this->modelo = $modelo;
    }

    public function crear(array $datos): array
    {
        $nombre = trim(
            (string) ($datos['nombre'] ?? '')
        );

        $descripcion = trim(
            (string) ($datos['descripcion'] ?? '')
        );

        if ($nombre === '') {
            return [
                'exito' => false,
                'mensaje' => 'El nombre de la categoría es obligatorio.'
            ];
        }

        if (strlen($nombre) > 100) {
            return [
                'exito' => false,
                'mensaje' => 'El nombre no puede superar 100 caracteres.'
            ];
        }

        $slug = $this->crearSlug($nombre);

        if ($slug === '') {
            return [
                'exito' => false,
                'mensaje' => 'No fue posible generar el identificador de la categoría.'
            ];
        }

        if ($this->modelo->existeSlug($slug)) {
            return [
                'exito' => false,
                'mensaje' => 'Ya existe una categoría con ese nombre.'
            ];
        }

        $this->modelo->crear(
            $nombre,
            $slug,
            $descripcion
        );

        return [
            'exito' => true,
            'mensaje' => 'Categoría creada correctamente.'
        ];
    }

    public function cambiarEstado(
        int $idCategoria,
        string $accion
    ): array {
        if ($idCategoria <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'Categoría inválida.'
            ];
        }

        $categoria = $this->modelo->buscarPorId(
            $idCategoria
        );

        if ($categoria === null) {
            return [
                'exito' => false,
                'mensaje' => 'La categoría no existe.'
            ];
        }

        if ($accion === 'activar') {
            $activo = 1;
        } elseif ($accion === 'desactivar') {
            $activo = 0;
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Acción no permitida.'
            ];
        }

        $realizado = $this->modelo->cambiarEstado(
            $idCategoria,
            $activo
        );

        return [
            'exito' => $realizado,
            'mensaje' => $realizado
                ? 'Estado de la categoría actualizado.'
                : 'La categoría ya tenía ese estado.'
        ];
    }

    public function eliminar(
        int $idCategoria
    ): array {
        if ($idCategoria <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'Categoría inválida.'
            ];
        }

        $categoria = $this->modelo->buscarPorId(
            $idCategoria
        );

        if ($categoria === null) {
            return [
                'exito' => false,
                'mensaje' => 'La categoría no existe.'
            ];
        }

        $this->modelo->eliminarConRelacionados(
            $idCategoria
        );

        return [
            'exito' => true,
            'mensaje' => 'Categoría y su información relacionada eliminadas correctamente.'
        ];
    }

    private function crearSlug(string $texto): string
    {
        $texto = strtolower(trim($texto));

        if (function_exists('iconv')) {
            $convertido = iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $texto
            );

            if ($convertido !== false) {
                $texto = $convertido;
            }
        }

        $texto = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $texto
        );

        return trim((string) $texto, '-');
    }
}