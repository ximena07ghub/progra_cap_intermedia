<?php

declare(strict_types=1);

class InstructorCurso
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarPorInstructor(
        int $idInstructor
    ): array {
        $sql = '
            SELECT
                cu.id_curso,
                cu.id_categoria,
                cu.titulo,
                cu.slug,
                cu.descripcion,
                cu.imagen,
                cu.precio,
                cu.moneda,
                cu.estado,
                cu.fecha_creacion,
                cu.fecha_actualizacion,
                ca.nombre AS categoria,
                COUNT(DISTINCT i.id_inscripcion) AS estudiantes,
                ROUND(AVG(r.calificacion), 1) AS calificacion
            FROM cursos AS cu
            INNER JOIN categorias AS ca
                ON ca.id_categoria = cu.id_categoria
            LEFT JOIN inscripciones AS i
                ON i.id_curso = cu.id_curso
            LEFT JOIN resenas AS r
                ON r.id_curso = cu.id_curso
                AND r.estado = \'visible\'
            WHERE cu.id_instructor = :id_instructor
            GROUP BY
                cu.id_curso,
                cu.id_categoria,
                cu.titulo,
                cu.slug,
                cu.descripcion,
                cu.imagen,
                cu.precio,
                cu.moneda,
                cu.estado,
                cu.fecha_creacion,
                cu.fecha_actualizacion,
                ca.nombre
            ORDER BY cu.fecha_actualizacion DESC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPropio(
        int $idCurso,
        int $idInstructor
    ): ?array {
        $sql = '
            SELECT
                cu.id_curso,
                cu.id_instructor,
                cu.id_categoria,
                cu.titulo,
                cu.slug,
                cu.descripcion,
                cu.imagen,
                cu.precio,
                cu.moneda,
                cu.estado,
                cu.fecha_creacion,
                cu.fecha_actualizacion,
                ca.nombre AS categoria
            FROM cursos AS cu
            INNER JOIN categorias AS ca
                ON ca.id_categoria = cu.id_categoria
            WHERE cu.id_curso = :id_curso
              AND cu.id_instructor = :id_instructor
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_curso' => $idCurso,
            'id_instructor' => $idInstructor
        ]);

        $curso = $consulta->fetch(PDO::FETCH_ASSOC);

        return $curso ?: null;
    }

    public function listarCategoriasActivas(): array
    {
        $sql = '
            SELECT
                id_categoria,
                nombre
            FROM categorias
            WHERE activo = 1
            ORDER BY nombre ASC
        ';

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeSlug(string $slug): bool
    {
        $sql = '
            SELECT id_curso
            FROM cursos
            WHERE slug = :slug
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'slug' => $slug
        ]);

        return $consulta->fetch() !== false;
    }

    public function categoriaActivaExiste(
        int $idCategoria
    ): bool {
        $sql = '
            SELECT id_categoria
            FROM categorias
            WHERE id_categoria = :id_categoria
              AND activo = 1
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_categoria' => $idCategoria
        ]);

        return $consulta->fetch() !== false;
    }

    public function crear(
        int $idInstructor,
        int $idCategoria,
        string $titulo,
        string $slug,
        string $descripcion,
        ?string $imagen,
        float $precio,
        string $estado
    ): int {
        $sql = '
            INSERT INTO cursos (
                id_instructor,
                id_categoria,
                titulo,
                slug,
                descripcion,
                imagen,
                precio,
                moneda,
                estado
            )
            VALUES (
                :id_instructor,
                :id_categoria,
                :titulo,
                :slug,
                :descripcion,
                :imagen,
                :precio,
                \'MXN\',
                :estado
            )
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor,
            'id_categoria' => $idCategoria,
            'titulo' => $titulo,
            'slug' => $slug,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'precio' => $precio,
            'estado' => $estado
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function actualizar(
        int $idCurso,
        int $idInstructor,
        int $idCategoria,
        string $titulo,
        string $descripcion,
        ?string $imagen,
        float $precio
    ): bool {
        $sql = '
            UPDATE cursos
            SET
                id_categoria = :id_categoria,
                titulo = :titulo,
                descripcion = :descripcion,
                imagen = :imagen,
                precio = :precio
            WHERE id_curso = :id_curso
              AND id_instructor = :id_instructor
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_categoria' => $idCategoria,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'precio' => $precio,
            'id_curso' => $idCurso,
            'id_instructor' => $idInstructor
        ]);

        return $consulta->rowCount() >= 0;
    }

    public function cambiarEstado(
        int $idCurso,
        int $idInstructor,
        string $estado
    ): bool {
        $sql = '
            UPDATE cursos
            SET estado = :estado
            WHERE id_curso = :id_curso
              AND id_instructor = :id_instructor
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'estado' => $estado,
            'id_curso' => $idCurso,
            'id_instructor' => $idInstructor
        ]);

        return $consulta->rowCount() >= 0;
    }

    public function listarNiveles(
        int $idCurso,
        int $idInstructor
    ): array {
        if ($this->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return [];
        }

        $sql = '
            SELECT
                id_nivel,
                id_curso,
                titulo,
                descripcion,
                orden,
                activo
            FROM niveles
            WHERE id_curso = :id_curso
            ORDER BY orden ASC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_curso' => $idCurso
        ]);

        $niveles = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($niveles as &$nivel) {
            $nivel['lecciones'] =
                $this->listarLecciones(
                    (int) $nivel['id_nivel']
                );
        }

        unset($nivel);

        return $niveles;
    }

    private function listarLecciones(
        int $idNivel
    ): array {
        $sql = '
            SELECT
                id_leccion,
                id_nivel,
                titulo,
                contenido,
                url_video,
                orden,
                activo
            FROM lecciones
            WHERE id_nivel = :id_nivel
            ORDER BY orden ASC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_nivel' => $idNivel
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearNivel(
        int $idCurso,
        int $idInstructor,
        string $titulo,
        string $descripcion
    ): bool {
        if ($this->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return false;
        }

        $sqlOrden = '
            SELECT COALESCE(MAX(orden), 0) + 1
            FROM niveles
            WHERE id_curso = :id_curso
        ';

        $consultaOrden =
            $this->conexion->prepare($sqlOrden);

        $consultaOrden->execute([
            'id_curso' => $idCurso
        ]);

        $orden = (int) $consultaOrden->fetchColumn();

        $sql = '
            INSERT INTO niveles (
                id_curso,
                titulo,
                descripcion,
                orden,
                activo
            )
            VALUES (
                :id_curso,
                :titulo,
                :descripcion,
                :orden,
                1
            )
        ';

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_curso' => $idCurso,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'orden' => $orden
        ]);
    }

    public function crearLeccion(
        int $idCurso,
        int $idInstructor,
        int $idNivel,
        string $titulo,
        string $contenido,
        string $urlVideo
    ): bool {
        if ($this->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return false;
        }

        $sqlNivel = '
            SELECT id_nivel
            FROM niveles
            WHERE id_nivel = :id_nivel
              AND id_curso = :id_curso
            LIMIT 1
        ';

        $consultaNivel =
            $this->conexion->prepare($sqlNivel);

        $consultaNivel->execute([
            'id_nivel' => $idNivel,
            'id_curso' => $idCurso
        ]);

        if ($consultaNivel->fetch() === false) {
            return false;
        }

        $sqlOrden = '
            SELECT COALESCE(MAX(orden), 0) + 1
            FROM lecciones
            WHERE id_nivel = :id_nivel
        ';

        $consultaOrden =
            $this->conexion->prepare($sqlOrden);

        $consultaOrden->execute([
            'id_nivel' => $idNivel
        ]);

        $orden = (int) $consultaOrden->fetchColumn();

        $sql = '
            INSERT INTO lecciones (
                id_nivel,
                titulo,
                contenido,
                url_video,
                orden,
                activo
            )
            VALUES (
                :id_nivel,
                :titulo,
                :contenido,
                :url_video,
                :orden,
                1
            )
        ';

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_nivel' => $idNivel,
            'titulo' => $titulo,
            'contenido' => $contenido,
            'url_video' => $urlVideo !== ''
                ? $urlVideo
                : null,
            'orden' => $orden
        ]);
    }

    public function eliminarLeccion(
        int $idLeccion,
        int $idCurso,
        int $idInstructor
    ): bool {
        if ($this->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return false;
        }

        $this->conexion->beginTransaction();

        try {
            $sqlProgreso = '
                DELETE p
                FROM progreso_lecciones AS p
                INNER JOIN lecciones AS l
                    ON l.id_leccion = p.id_leccion
                INNER JOIN niveles AS n
                    ON n.id_nivel = l.id_nivel
                WHERE p.id_leccion = :id_leccion
                  AND n.id_curso = :id_curso
            ';

            $consulta =
                $this->conexion->prepare($sqlProgreso);

            $consulta->execute([
                'id_leccion' => $idLeccion,
                'id_curso' => $idCurso
            ]);

            $sql = '
                DELETE l
                FROM lecciones AS l
                INNER JOIN niveles AS n
                    ON n.id_nivel = l.id_nivel
                WHERE l.id_leccion = :id_leccion
                  AND n.id_curso = :id_curso
            ';

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                'id_leccion' => $idLeccion,
                'id_curso' => $idCurso
            ]);

            $eliminada = $consulta->rowCount() === 1;

            $this->conexion->commit();

            return $eliminada;

        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function eliminarNivel(
        int $idNivel,
        int $idCurso,
        int $idInstructor
    ): bool {
        if ($this->buscarPropio(
            $idCurso,
            $idInstructor
        ) === null) {
            return false;
        }

        $this->conexion->beginTransaction();

        try {
            $sqlProgreso = '
                DELETE p
                FROM progreso_lecciones AS p
                INNER JOIN lecciones AS l
                    ON l.id_leccion = p.id_leccion
                INNER JOIN niveles AS n
                    ON n.id_nivel = l.id_nivel
                WHERE n.id_nivel = :id_nivel
                  AND n.id_curso = :id_curso
            ';

            $consulta =
                $this->conexion->prepare($sqlProgreso);

            $consulta->execute([
                'id_nivel' => $idNivel,
                'id_curso' => $idCurso
            ]);

            $sqlLecciones = '
                DELETE l
                FROM lecciones AS l
                INNER JOIN niveles AS n
                    ON n.id_nivel = l.id_nivel
                WHERE n.id_nivel = :id_nivel
                  AND n.id_curso = :id_curso
            ';

            $consulta =
                $this->conexion->prepare($sqlLecciones);

            $consulta->execute([
                'id_nivel' => $idNivel,
                'id_curso' => $idCurso
            ]);

            $sqlNivel = '
                DELETE FROM niveles
                WHERE id_nivel = :id_nivel
                  AND id_curso = :id_curso
            ';

            $consulta =
                $this->conexion->prepare($sqlNivel);

            $consulta->execute([
                'id_nivel' => $idNivel,
                'id_curso' => $idCurso
            ]);

            $eliminado = $consulta->rowCount() === 1;

            $this->conexion->commit();

            return $eliminado;

        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function eliminarCurso(
        int $idCurso,
        int $idInstructor
    ): ?string {
        $curso = $this->buscarPropio(
            $idCurso,
            $idInstructor
        );

        if ($curso === null) {
            return null;
        }

        $imagen = $curso['imagen'];

        $this->conexion->beginTransaction();

        try {
            $consultas = [
                '
                    DELETE m
                    FROM mensajes AS m
                    INNER JOIN conversaciones AS co
                        ON co.id_conversacion = m.id_conversacion
                    WHERE co.id_curso = :id_curso
                ',
                '
                    DELETE FROM conversaciones
                    WHERE id_curso = :id_curso
                ',
                '
                    DELETE FROM resenas
                    WHERE id_curso = :id_curso
                ',
                '
                    DELETE p
                    FROM progreso_lecciones AS p
                    INNER JOIN inscripciones AS i
                        ON i.id_inscripcion = p.id_inscripcion
                    WHERE i.id_curso = :id_curso
                ',
                '
                    DELETE ce
                    FROM certificados AS ce
                    INNER JOIN inscripciones AS i
                        ON i.id_inscripcion = ce.id_inscripcion
                    WHERE i.id_curso = :id_curso
                ',
                '
                    DELETE FROM pagos
                    WHERE id_curso = :id_curso
                ',
                '
                    DELETE FROM inscripciones
                    WHERE id_curso = :id_curso
                ',
                '
                    DELETE p
                    FROM progreso_lecciones AS p
                    INNER JOIN lecciones AS l
                        ON l.id_leccion = p.id_leccion
                    INNER JOIN niveles AS n
                        ON n.id_nivel = l.id_nivel
                    WHERE n.id_curso = :id_curso
                ',
                '
                    DELETE l
                    FROM lecciones AS l
                    INNER JOIN niveles AS n
                        ON n.id_nivel = l.id_nivel
                    WHERE n.id_curso = :id_curso
                ',
                '
                    DELETE FROM niveles
                    WHERE id_curso = :id_curso
                '
            ];

            foreach ($consultas as $sql) {
                $consulta =
                    $this->conexion->prepare($sql);

                $consulta->execute([
                    'id_curso' => $idCurso
                ]);
            }

            $sqlCurso = '
                DELETE FROM cursos
                WHERE id_curso = :id_curso
                  AND id_instructor = :id_instructor
            ';

            $consulta =
                $this->conexion->prepare($sqlCurso);

            $consulta->execute([
                'id_curso' => $idCurso,
                'id_instructor' => $idInstructor
            ]);

            if ($consulta->rowCount() !== 1) {
                throw new RuntimeException(
                    'No fue posible eliminar el curso.'
                );
            }

            $this->conexion->commit();

            return is_string($imagen)
                ? $imagen
                : null;

        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function obtenerMetricas(
        int $idInstructor
    ): array {
        $sql = '
            SELECT
                COUNT(DISTINCT cu.id_curso) AS cursos,
                COUNT(DISTINCT i.id_inscripcion) AS estudiantes,
                COUNT(DISTINCT co.id_conversacion) AS conversaciones
            FROM cursos AS cu
            LEFT JOIN inscripciones AS i
                ON i.id_curso = cu.id_curso
            LEFT JOIN conversaciones AS co
                ON co.id_curso = cu.id_curso
            WHERE cu.id_instructor = :id_instructor
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        $metricas = $consulta->fetch(PDO::FETCH_ASSOC);

        $sqlIngresos = '
            SELECT COALESCE(SUM(p.monto), 0)
            FROM pagos AS p
            INNER JOIN cursos AS cu
                ON cu.id_curso = p.id_curso
            WHERE cu.id_instructor = :id_instructor
              AND p.estado = \'completado\'
        ';

        $consulta =
            $this->conexion->prepare($sqlIngresos);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        return [
            'cursos' => (int) ($metricas['cursos'] ?? 0),
            'estudiantes' =>
                (int) ($metricas['estudiantes'] ?? 0),
            'conversaciones' =>
                (int) ($metricas['conversaciones'] ?? 0),
            'ingresos' =>
                (float) $consulta->fetchColumn()
        ];
    }

    public function listarVentas(
        int $idInstructor
    ): array {
        $sql = '
            SELECT
                p.id_pago,
                p.monto,
                p.moneda,
                p.metodo,
                p.estado,
                p.fecha_pago,
                u.nombre AS estudiante,
                cu.titulo AS curso
            FROM pagos AS p
            INNER JOIN cursos AS cu
                ON cu.id_curso = p.id_curso
            INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_estudiante
            WHERE cu.id_instructor = :id_instructor
            ORDER BY p.fecha_pago DESC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEstudiantes(
        int $idInstructor
    ): array {
        $sql = '
            SELECT
                i.id_inscripcion,
                i.estado,
                i.fecha_inscripcion,
                u.nombre AS estudiante,
                u.email,
                cu.titulo AS curso
            FROM inscripciones AS i
            INNER JOIN cursos AS cu
                ON cu.id_curso = i.id_curso
            INNER JOIN usuarios AS u
                ON u.id_usuario = i.id_estudiante
            WHERE cu.id_instructor = :id_instructor
            ORDER BY i.fecha_inscripcion DESC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarResenas(
        int $idInstructor
    ): array {
        $sql = '
            SELECT
                r.calificacion,
                r.comentario,
                r.estado,
                r.fecha_creacion,
                u.nombre AS estudiante,
                cu.titulo AS curso
            FROM resenas AS r
            INNER JOIN cursos AS cu
                ON cu.id_curso = r.id_curso
            INNER JOIN usuarios AS u
                ON u.id_usuario = r.id_estudiante
            WHERE cu.id_instructor = :id_instructor
            ORDER BY r.fecha_creacion DESC
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_instructor' => $idInstructor
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}