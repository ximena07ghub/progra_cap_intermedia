<?php
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Búsqueda | AulaGo';
$pageDescription = 'Busca cursos por nombre, instructor, categoría o nivel.';
$query = trim((string) ($_GET['q'] ?? ''));
$allCourses = courses();

$filteredCourses = array_values(array_filter($allCourses, function (array $course) use ($query): bool {
    if ($query === '') {
        return true;
    }

    $haystack = implode(' ', [
        $course['title'] ?? '',
        $course['category'] ?? '',
        $course['level'] ?? '',
        $course['instructor'] ?? '',
        $course['description'] ?? '',
        $course['accent'] ?? '',
    ]);

    return text_contains_ci($haystack, $query);
}));

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
    <section class="max-w-4xl">
      <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Búsqueda AulaGo</p>
      <h1 class="mt-4 font-editorial text-5xl font-semibold text-aula-cream sm:text-6xl">Busca una idea, una habilidad o un tema.</h1>

      <form class="mt-8 flex border-b border-white/20" action="<?= e(url('buscar.php')) ?>" method="get">
        <input
          name="q"
          value="<?= e($query) ?>"
          type="search"
          placeholder="Ej. software, hábitos, diseño…"
          class="min-w-0 flex-1 bg-transparent py-4 text-lg text-white outline-none placeholder:text-white/25"
        >
        <button type="submit" class="bg-transparent px-4 text-sm font-extrabold uppercase tracking-[0.08em] text-aula-orange">Buscar ↗</button>
      </form>
    </section>

    <section class="mt-12 border-t border-white/10 pt-8">
      <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.08em] text-white/35"><?= $query !== '' ? 'Resultados encontrados' : 'Empieza por aquí' ?></p>
          <h2 class="mt-2 font-editorial text-4xl text-aula-cream">
            <?= $query !== '' ? 'Resultados para “' . e($query) . '”' : 'Cursos para explorar' ?>
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
          <p class="mt-3 max-w-xl text-sm leading-6 text-white/45">Prueba con otro término o explora directamente por categorías.</p>
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
