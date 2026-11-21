<?php

declare(strict_types=1);

class InstructorCursoController
{
    private InstructorCurso $modelo;

    public function __construct(
        InstructorCurso $modelo
    ) {
        $this->modelo = $modelo;
    }

    public function crear(
        int $idInstructor,
        array $datos,
        array $archivo
    ): array {
        $validacion =
            $this->validarCurso($datos);

        if (!$validacion['exito']) {
            return $validacion;
        }

        $titulo = $validacion['titulo'];
        $slug = $this->crearSlug($titulo);

        if ($slug === '') {
            return [
                'exito' => false,
                'mensaje' => 'No fue posible generar el identificador del curso.'
            ];
        }

        $slugBase = $slug;
        $contador = 2;

        while ($this->modelo->existeSlug($slug)) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        $imagen = $this->guardarImagen($archivo);

        $estado =
            ($datos['estado'] ?? '') === 'publicado'
                ? 'publicado'
                : 'borrador';

        $idCurso = $this->modelo->crear(
            $idInstructor,
            $validacion['id_categoria'],
            $titulo,
            $slug,
            $validacion['descripcion'],
            $imagen,
            $validacion['precio'],
            $estado
        );

        return [
            'exito' => true,
            'id_curso' => $idCurso,
            'mensaje' => $estado === 'publicado'
                ? 'Curso creado y publicado correctamente.'
                : 'Curso guardado como borrador.'
        ];
    }

    public function actualizar(
        int $idInstructor,
        int $idCurso,
        array $datos,
        array $archivo
    ): array {
        $curso = $this->modelo->buscarPropio(
            $idCurso,
            $idInstructor
        );

        if ($curso === null) {
            return [
                'exito' => false,
                'mensaje' => 'El curso no existe o no te pertenece.'
            ];
        }

        $validacion =
            $this->validarCurso($datos);

        if (!$validacion['exito']) {
            return $validacion;
        }

        $imagen = $curso['imagen'];

        if (
            isset($archivo['error'])
            && $archivo['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $nuevaImagen =
                $this->guardarImagen($archivo);

            if ($nuevaImagen !== null) {
                $this->eliminarImagenLocal(
                    is_string($imagen)
                        ? $imagen
                        : null
                );

                $imagen = $nuevaImagen;
            }
        }

        $this->modelo->actualizar(
            $idCurso,
            $idInstructor,
            $validacion['id_categoria'],
            $validacion['titulo'],
            $validacion['descripcion'],
            is_string($imagen) ? $imagen : null,
            $validacion['precio']
        );

        return [
            'exito' => true,
            'mensaje' => 'Curso actualizado correctamente.'
        ];
    }

    public function cambiarEstado(
        int $idInstructor,
        int $idCurso,
        string $estado
    ): array {
        if (!in_array(
            $estado,
            ['borrador', 'publicado', 'desactivado'],
            true
        )) {
            return [
                'exito' => false,
                'mensaje' => 'Estado inválido.'
            ];
        }

        if ($this->modelo->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return [
                'exito' => false,
                'mensaje' => 'El curso no existe o no te pertenece.'
            ];
        }

        $this->modelo->cambiarEstado(
            $idCurso,
            $idInstructor,
            $estado
        );

        return [
            'exito' => true,
            'mensaje' => 'Estado del curso actualizado.'
        ];
    }

    public function crearNivel(
        int $idInstructor,
        int $idCurso,
        array $datos
    ): array {
        $titulo = trim(
            (string) ($datos['titulo'] ?? '')
        );

        $descripcion = trim(
            (string) ($datos['descripcion'] ?? '')
        );

        if ($titulo === '' || strlen($titulo) > 150) {
            return [
                'exito' => false,
                'mensaje' => 'Escribe un título válido para el nivel.'
            ];
        }

        $creado = $this->modelo->crearNivel(
            $idCurso,
            $idInstructor,
            $titulo,
            $descripcion
        );

        return [
            'exito' => $creado,
            'mensaje' => $creado
                ? 'Nivel creado correctamente.'
                : 'No fue posible crear el nivel.'
        ];
    }

    public function crearLeccion(
        int $idInstructor,
        int $idCurso,
        array $datos
    ): array {
        $idNivel = filter_var(
            $datos['id_nivel'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        $titulo = trim(
            (string) ($datos['titulo'] ?? '')
        );

        $contenido = trim(
            (string) ($datos['contenido'] ?? '')
        );

        $urlVideo = trim(
            (string) ($datos['url_video'] ?? '')
        );

        if ($idNivel === false) {
            return [
                'exito' => false,
                'mensaje' => 'Nivel inválido.'
            ];
        }

        if ($titulo === '' || strlen($titulo) > 200) {
            return [
                'exito' => false,
                'mensaje' => 'Escribe un título válido para la lección.'
            ];
        }

        if (
            $urlVideo !== ''
            && !filter_var(
                $urlVideo,
                FILTER_VALIDATE_URL
            )
        ) {
            return [
                'exito' => false,
                'mensaje' => 'La URL del video no es válida.'
            ];
        }

        $creada = $this->modelo->crearLeccion(
            $idCurso,
            $idInstructor,
            (int) $idNivel,
            $titulo,
            $contenido,
            $urlVideo
        );

        return [
            'exito' => $creada,
            'mensaje' => $creada
                ? 'Lección creada correctamente.'
                : 'No fue posible crear la lección.'
        ];
    }

    public function eliminar(
        int $idInstructor,
        int $idCurso
    ): array {
        $imagen = $this->modelo->eliminarCurso(
            $idCurso,
            $idInstructor
        );

        if ($imagen === null) {
            return [
                'exito' => false,
                'mensaje' => 'El curso no existe o no te pertenece.'
            ];
        }

        $this->eliminarImagenLocal($imagen);

        return [
            'exito' => true,
            'mensaje' => 'Curso e información relacionada eliminados correctamente.'
        ];
    }

    private function validarCurso(
        array $datos
    ): array {
        $titulo = trim(
            (string) ($datos['titulo'] ?? '')
        );

        $descripcion = trim(
            (string) ($datos['descripcion'] ?? '')
        );

        $idCategoria = filter_var(
            $datos['id_categoria'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        $precioTexto = trim(
            (string) ($datos['precio'] ?? '')
        );

        if ($titulo === '' || strlen($titulo) > 200) {
            return [
                'exito' => false,
                'mensaje' => 'Escribe un título válido.'
            ];
        }

        if ($descripcion === '') {
            return [
                'exito' => false,
                'mensaje' => 'La descripción es obligatoria.'
            ];
        }

        if ($idCategoria === false) {
            return [
                'exito' => false,
                'mensaje' => 'Selecciona una categoría.'
            ];
        }

        if (!$this->modelo->categoriaActivaExiste(
            (int) $idCategoria
        )) {
            return [
                'exito' => false,
                'mensaje' => 'La categoría seleccionada no está disponible.'
            ];
        }

        if (
            $precioTexto === ''
            || !is_numeric($precioTexto)
            || (float) $precioTexto < 0
        ) {
            return [
                'exito' => false,
                'mensaje' => 'Escribe un precio válido.'
            ];
        }

        return [
            'exito' => true,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'id_categoria' => (int) $idCategoria,
            'precio' => round(
                (float) $precioTexto,
                2
            )
        ];
    }

    private function guardarImagen(
        array $archivo
    ): ?string {
        if (
            !isset($archivo['error'])
            || $archivo['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'No fue posible subir la portada.'
            );
        }

        if (
            !isset($archivo['tmp_name'], $archivo['size'])
            || !is_uploaded_file($archivo['tmp_name'])
        ) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }

        if ((int) $archivo['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException(
                'La portada no puede superar 5 MB.'
            );
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file(
            $archivo['tmp_name']
        );

        $tipos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($tipos[$mime])) {
            throw new RuntimeException(
                'La portada debe ser JPG, PNG o WebP.'
            );
        }

        $directorio =
            PROJECT_ROOT . '/assets/uploads/cursos';

        if (
            !is_dir($directorio)
            && !mkdir(
                $directorio,
                0775,
                true
            )
        ) {
            throw new RuntimeException(
                'No fue posible crear la carpeta de portadas.'
            );
        }

        $nombre =
            bin2hex(random_bytes(16))
            . '.'
            . $tipos[$mime];

        $destino =
            $directorio . '/' . $nombre;

        if (!move_uploaded_file(
            $archivo['tmp_name'],
            $destino
        )) {
            throw new RuntimeException(
                'No fue posible guardar la portada.'
            );
        }

        return 'uploads/cursos/' . $nombre;
    }

    private function eliminarImagenLocal(
        ?string $imagen
    ): void {
        if (
            $imagen === null
            || !str_starts_with(
                $imagen,
                'uploads/cursos/'
            )
        ) {
            return;
        }

        $ruta =
            PROJECT_ROOT
            . '/assets/'
            . $imagen;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    private function crearSlug(
        string $texto
    ): string {
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