<?php

declare(strict_types=1);

class AdminCategoria
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $sql = '
            SELECT
                c.id_categoria,
                c.nombre,
                c.slug,
                c.descripcion,
                c.activo,
                COUNT(cu.id_curso) AS total_cursos
            FROM categorias AS c
            LEFT JOIN cursos AS cu
                ON cu.id_categoria = c.id_categoria
            GROUP BY
                c.id_categoria,
                c.nombre,
                c.slug,
                c.descripcion,
                c.activo
            ORDER BY c.nombre ASC
        ';

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $idCategoria): ?array
    {
        $sql = '
            SELECT
                id_categoria,
                nombre,
                slug,
                descripcion,
                activo
            FROM categorias
            WHERE id_categoria = :id_categoria
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_categoria' => $idCategoria
        ]);

        $categoria = $consulta->fetch(PDO::FETCH_ASSOC);

        return $categoria ?: null;
    }

    public function existeSlug(string $slug): bool
    {
        $sql = '
            SELECT id_categoria
            FROM categorias
            WHERE slug = :slug
            LIMIT 1
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'slug' => $slug
        ]);

        return $consulta->fetch() !== false;
    }

    public function crear(
        string $nombre,
        string $slug,
        string $descripcion
    ): int {
        $sql = '
            INSERT INTO categorias (
                nombre,
                slug,
                descripcion,
                activo
            )
            VALUES (
                :nombre,
                :slug,
                :descripcion,
                1
            )
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'nombre' => $nombre,
            'slug' => $slug,
            'descripcion' => $descripcion
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function cambiarEstado(
        int $idCategoria,
        int $activo
    ): bool {
        $sql = '
            UPDATE categorias
            SET activo = :activo
            WHERE id_categoria = :id_categoria
        ';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'activo' => $activo,
            'id_categoria' => $idCategoria
        ]);

        return $consulta->rowCount() === 1;
    }

    public function eliminarConRelacionados(
        int $idCategoria
    ): void {
        $this->conexion->beginTransaction();

        try {
            /*
             * Mensajes pertenecientes a conversaciones
             * de cursos de esta categoría.
             */
            $sql = '
                DELETE m
                FROM mensajes AS m
                INNER JOIN conversaciones AS co
                    ON co.id_conversacion = m.id_conversacion
                INNER JOIN cursos AS cu
                    ON cu.id_curso = co.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Conversaciones de los cursos.
             */
            $sql = '
                DELETE co
                FROM conversaciones AS co
                INNER JOIN cursos AS cu
                    ON cu.id_curso = co.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Reseñas de los cursos.
             */
            $sql = '
                DELETE r
                FROM resenas AS r
                INNER JOIN cursos AS cu
                    ON cu.id_curso = r.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Progreso relacionado con las inscripciones.
             */
            $sql = '
                DELETE p
                FROM progreso_lecciones AS p
                INNER JOIN inscripciones AS i
                    ON i.id_inscripcion = p.id_inscripcion
                INNER JOIN cursos AS cu
                    ON cu.id_curso = i.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Certificados de las inscripciones.
             */
            $sql = '
                DELETE ce
                FROM certificados AS ce
                INNER JOIN inscripciones AS i
                    ON i.id_inscripcion = ce.id_inscripcion
                INNER JOIN cursos AS cu
                    ON cu.id_curso = i.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Pagos relacionados con los cursos.
             */
            $sql = '
                DELETE p
                FROM pagos AS p
                INNER JOIN cursos AS cu
                    ON cu.id_curso = p.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Inscripciones de los cursos.
             */
            $sql = '
                DELETE i
                FROM inscripciones AS i
                INNER JOIN cursos AS cu
                    ON cu.id_curso = i.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Lecciones pertenecientes a niveles
             * de los cursos.
             */
            $sql = '
                DELETE l
                FROM lecciones AS l
                INNER JOIN niveles AS n
                    ON n.id_nivel = l.id_nivel
                INNER JOIN cursos AS cu
                    ON cu.id_curso = n.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Niveles de los cursos.
             */
            $sql = '
                DELETE n
                FROM niveles AS n
                INNER JOIN cursos AS cu
                    ON cu.id_curso = n.id_curso
                WHERE cu.id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Cursos de la categoría.
             */
            $sql = '
                DELETE FROM cursos
                WHERE id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            /*
             * Finalmente se elimina la categoría.
             */
            $sql = '
                DELETE FROM categorias
                WHERE id_categoria = :id_categoria
            ';

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'id_categoria' => $idCategoria
            ]);

            if ($consulta->rowCount() !== 1) {
                throw new RuntimeException(
                    'No fue posible eliminar la categoría.'
                );
            }

            $this->conexion->commit();

        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }
}