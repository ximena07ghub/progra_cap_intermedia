<?php

declare(strict_types=1);

class Catalogo
{
    public function __construct(private PDO $conexion)
    {
    }

    public function listarCursosPublicados(): array
    {
        $sql = "
            SELECT
                cu.id_curso,
                cu.slug,
                cu.titulo,
                cu.descripcion,
                cu.imagen,
                cu.precio,
                cu.moneda,
                ca.id_categoria,
                ca.slug AS categoria_slug,
                ca.nombre AS categoria,
                u.nombre AS instructor,
                COUNT(DISTINCT n.id_nivel) AS niveles,
                COUNT(DISTINCT l.id_leccion) AS lecciones,
                COUNT(DISTINCT i.id_inscripcion) AS estudiantes,
                COUNT(DISTINCT r.id_resena) AS resenas,
                COALESCE(AVG(r.calificacion), 0) AS calificacion
            FROM cursos AS cu
            INNER JOIN categorias AS ca
                ON ca.id_categoria = cu.id_categoria
            INNER JOIN usuarios AS u
                ON u.id_usuario = cu.id_instructor
            LEFT JOIN niveles AS n
                ON n.id_curso = cu.id_curso
                AND n.activo = 1
            LEFT JOIN lecciones AS l
                ON l.id_nivel = n.id_nivel
                AND l.activo = 1
            LEFT JOIN inscripciones AS i
                ON i.id_curso = cu.id_curso
                AND i.estado IN ('activa', 'completada')
            LEFT JOIN resenas AS r
                ON r.id_curso = cu.id_curso
                AND r.estado = 'visible'
            WHERE cu.estado = 'publicado'
              AND ca.activo = 1
            GROUP BY
                cu.id_curso,
                cu.slug,
                cu.titulo,
                cu.descripcion,
                cu.imagen,
                cu.precio,
                cu.moneda,
                ca.id_categoria,
                ca.slug,
                ca.nombre,
                u.nombre
            ORDER BY cu.fecha_actualizacion DESC
        ";

        $consulta = $this->conexion->query($sql);

        return array_map(
            [$this, 'normalizarCurso'],
            $consulta->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function buscarCursoPorSlug(string $slug): ?array
    {
        foreach ($this->listarCursosPublicados() as $curso) {
            if ($curso['slug'] === $slug) {
                return $curso;
            }
        }

        return null;
    }

    public function listarCategoriasActivas(): array
    {
        $sql = "
            SELECT
                ca.id_categoria,
                ca.slug,
                ca.nombre,
                ca.descripcion,
                COUNT(cu.id_curso) AS cursos
            FROM categorias AS ca
            LEFT JOIN cursos AS cu
                ON cu.id_categoria = ca.id_categoria
                AND cu.estado = 'publicado'
            WHERE ca.activo = 1
            GROUP BY
                ca.id_categoria,
                ca.slug,
                ca.nombre,
                ca.descripcion
            ORDER BY ca.nombre ASC
        ";

        $consulta = $this->conexion->query($sql);
        $categorias = [];

        foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $indice => $fila) {
            $categorias[] = [
                'id' => (string) $fila['slug'],
                'databaseId' => (int) $fila['id_categoria'],
                'name' => (string) $fila['nombre'],
                'eyebrow' => 'Categoría AulaGo',
                'description' => (string) ($fila['descripcion'] ?? ''),
                'symbol' => str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT),
                'courses' => (int) $fila['cursos'],
            ];
        }

        return $categorias;
    }

    public function listarLecciones(int $idCurso): array
    {
        $sql = "
            SELECT
                n.titulo AS nivel,
                n.orden AS orden_nivel,
                l.id_leccion,
                l.titulo,
                l.contenido,
                l.url_video,
                l.orden
            FROM niveles AS n
            INNER JOIN lecciones AS l
                ON l.id_nivel = n.id_nivel
            WHERE n.id_curso = :id_curso
              AND n.activo = 1
              AND l.activo = 1
            ORDER BY n.orden ASC, l.orden ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_curso' => $idCurso]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarResenas(int $idCurso): array
    {
        $sql = "
            SELECT
                r.calificacion,
                r.comentario,
                r.fecha_creacion,
                u.nombre
            FROM resenas AS r
            INNER JOIN usuarios AS u
                ON u.id_usuario = r.id_estudiante
            WHERE r.id_curso = :id_curso
              AND r.estado = 'visible'
            ORDER BY r.fecha_creacion DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_curso' => $idCurso]);

        $resenas = [];

        foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $partes = preg_split('/\s+/u', trim((string) $fila['nombre'])) ?: [];
            $iniciales = '';

            foreach (array_slice($partes, 0, 2) as $parte) {
                if ($parte !== '') {
                    $iniciales .= text_first_upper($parte);
                }
            }

            $resenas[] = [
                'name' => (string) $fila['nombre'],
                'avatar' => $iniciales !== '' ? $iniciales : 'AG',
                'rating' => (int) $fila['calificacion'],
                'comment' => (string) ($fila['comentario'] ?? ''),
                'completedAt' => (string) $fila['fecha_creacion'],
            ];
        }

        return $resenas;
    }

    private function normalizarCurso(array $fila): array
    {
        $precio = (float) $fila['precio'];
        $moneda = strtoupper((string) $fila['moneda']);
        $lecciones = (int) $fila['lecciones'];
        $niveles = (int) $fila['niveles'];

        return [
            'id' => (int) $fila['id_curso'],
            'databaseId' => (int) $fila['id_curso'],
            'slug' => (string) $fila['slug'],
            'title' => (string) $fila['titulo'],
            'description' => (string) $fila['descripcion'],
            'image' => (string) ($fila['imagen'] ?? ''),
            'price' => $precio <= 0
                ? 'Gratis'
                : '$' . number_format($precio, 2, '.', ',') . ' ' . $moneda,
            'amount' => $precio,
            'currency' => $moneda,
            'categoryId' => (string) $fila['categoria_slug'],
            'category' => (string) $fila['categoria'],
            'instructor' => (string) $fila['instructor'],
            'level' => $niveles . ($niveles === 1 ? ' nivel' : ' niveles'),
            'duration' => $lecciones . ($lecciones === 1 ? ' lección' : ' lecciones'),
            'lessons' => $lecciones,
            'levels' => $niveles,
            'students' => (int) $fila['estudiantes'],
            'reviews' => (int) $fila['resenas'],
            'rating' => round((float) $fila['calificacion'], 1),
            'language' => 'AulaGo',
            'accent' => (string) $fila['descripcion'],
            'wellbeingFocus' => '',
        ];
    }
}
