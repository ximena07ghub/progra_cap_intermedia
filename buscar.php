<?php
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Búsqueda | AulaGo';
$pageDescription = 'Busca cursos por título, instructor, categoría o fecha de publicación.';

$query = trim((string) ($_GET['q'] ?? ''));
$categoria = trim((string) ($_GET['categoria'] ?? ''));
$instructor = trim((string) ($_GET['instructor'] ?? ''));
$desde = trim((string) ($_GET['desde'] ?? ''));
$hasta = trim((string) ($_GET['hasta'] ?? ''));

$allCourses = courses();
$categoriasBusqueda = categories();

$instructoresBusqueda = [];
foreach ($allCourses as $course) {
    $nombreInstructor = trim((string) ($course['instructor'] ?? ''));
    if ($nombreInstructor !== '') {
        $instructoresBusqueda[$nombreInstructor] = $nombreInstructor;
    }
}
natcasesort($instructoresBusqueda);

$filteredCourses = array_values(array_filter(
    $allCourses,
    function (array $course) use ($query, $categoria, $instructor, $desde, $hasta): bool {
        if ($query !== '') {
            $haystack = implode(' ', [
                $course['title'] ?? '',
                $course['description'] ?? '',
            ]);

            if (!text_contains_ci($haystack, $query)) {
                return false;
            }
        }

        if (
            $categoria !== ''
            && (string) ($course['categoryId'] ?? '') !== $categoria
        ) {
            return false;
        }

        if (
            $instructor !== ''
            && (string) ($course['instructor'] ?? '') !== $instructor
        ) {
            return false;
        }

        $fechaCurso = substr((string) ($course['publishedAt'] ?? ''), 0, 10);

        if ($desde !== '' && ($fechaCurso === '' || $fechaCurso < $desde)) {
            return false;
        }

        if ($hasta !== '' && ($fechaCurso === '' || $fechaCurso > $hasta)) {
            return false;
        }

        return true;
    }
));

$hayFiltros = $query !== ''
    || $categoria !== ''
    || $instructor !== ''
    || $desde !== ''
    || $hasta !== '';

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
    <section class="max-w-5xl">
      <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Búsqueda AulaGo</p>
      <h1 class="mt-4 font-editorial text-5xl font-semibold text-aula-cream sm:text-6xl">Encuentra tu próximo curso.</h1>

      <form class="mt-8 border-y border-white/10 py-6" action="<?= e(url('buscar.php')) ?>" method="get">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <label class="grid gap-2 md:col-span-2 xl:col-span-4">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Título</span>
            <input
              name="q"
              value="<?= e($query) ?>"
              type="search"
              placeholder="Ej. fotografía, diseño, programación…"
              class="min-h-11 border border-white/10 bg-aula-surface px-3 text-sm text-white outline-none focus:border-aula-orange"
            >
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Categoría</span>
            <select name="categoria" class="min-h-11 border border-white/10 bg-aula-surface px-3 text-sm text-white outline-none focus:border-aula-orange">
              <option value="">Todas</option>
              <?php foreach ($categoriasBusqueda as $item): ?>
                <option value="<?= e($item['id']) ?>" <?= $categoria === (string) $item['id'] ? 'selected' : '' ?>>
                  <?= e($item['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Instructor</span>
            <select name="instructor" class="min-h-11 border border-white/10 bg-aula-surface px-3 text-sm text-white outline-none focus:border-aula-orange">
              <option value="">Todos</option>
              <?php foreach ($instructoresBusqueda as $nombreInstructor): ?>
                <option value="<?= e($nombreInstructor) ?>" <?= $instructor === $nombreInstructor ? 'selected' : '' ?>>
                  <?= e($nombreInstructor) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Publicado desde</span>
            <input name="desde" value="<?= e($desde) ?>" type="date" class="min-h-11 border border-white/10 bg-aula-surface px-3 text-sm text-white outline-none focus:border-aula-orange">
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Publicado hasta</span>
            <input name="hasta" value="<?= e($hasta) ?>" type="date" class="min-h-11 border border-white/10 bg-aula-surface px-3 text-sm text-white outline-none focus:border-aula-orange">
          </label>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
          <button type="submit" class="bg-aula-orange px-5 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Aplicar filtros</button>
          <?php if ($hayFiltros): ?>
            <a href="<?= e(url('buscar.php')) ?>" class="border border-white/10 px-5 py-3 text-xs font-bold text-white/55">Limpiar filtros</a>
          <?php endif; ?>
        </div>
      </form>
    </section>

    <section class="mt-10 border-t border-white/10 pt-8">
      <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.08em] text-white/35"><?= $hayFiltros ? 'Resultados filtrados' : 'Cursos publicados' ?></p>
          <h2 class="mt-2 font-editorial text-4xl text-aula-cream">
            <?= $hayFiltros ? 'Cursos que coinciden con tu búsqueda' : 'Cursos para explorar' ?>
          </h2>
        </div>
        <span class="text-xs text-white/35"><?= e(count($filteredCourses)) ?> resultado<?= count($filteredCourses) === 1 ? '' : 's' ?></span>
      </div>

      <?php if ($filteredCourses): ?>
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filteredCourses as $course): ?>
            <?php require PROJECT_ROOT . '/includes/components/course-card.php'; ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="border border-white/10 bg-aula-surface/50 p-8 sm:p-10">
          <p class="font-editorial text-3xl text-aula-cream">No encontramos coincidencias.</p>
          <p class="mt-3 max-w-xl text-sm leading-6 text-white/45">Ajusta los filtros o explora directamente las categorías disponibles.</p>
          <a href="<?= e(url('categorias.php')) ?>" class="mt-6 inline-flex bg-aula-orange px-5 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Ver categorías</a>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
