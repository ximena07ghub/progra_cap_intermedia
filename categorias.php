<?php
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Categorías | AulaGo';
$pageDescription = 'Busca cursos por categoría, tema y nivel en AulaGo.';
$allCourses = courses();
$allCategories = categories();
$pageScripts = ['catalog-filter.js'];

function category_count(array $courses, string $categoryId): int
{
    return count(array_filter($courses, fn(array $course): bool => ($course['categoryId'] ?? '') === $categoryId));
}

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main data-catalog-filter>
    <section class="relative overflow-hidden border-b border-white/10">
      <div class="pointer-events-none absolute -left-44 top-10 h-[420px] w-[420px] rounded-full bg-aula-green/[0.05] blur-3xl"></div>
      <div class="mx-auto grid max-w-aula gap-8 px-6 py-14 lg:grid-cols-[1fr_.78fr] lg:px-8 lg:py-20">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Explora por tema</p>
          <h1 class="mt-4 max-w-3xl font-editorial text-5xl font-semibold leading-[0.98] text-aula-cream sm:text-6xl">
            Filtra la colección hasta encontrar <span class="text-aula-orange-soft">tu siguiente curso.</span>
          </h1>
        </div>
        <p class="self-end max-w-lg text-sm leading-7 text-white/45">
          Usa esta sección cuando quieras buscar por tema, categoría o nivel. Los resultados aparecen aquí mismo para evitar saltos innecesarios entre pantallas.
        </p>
      </div>
    </section>

    <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-12">
      <form class="grid gap-4 rounded-aula-lg border border-white/10 bg-aula-surface/60 p-4 md:grid-cols-[1.4fr_.8fr_auto] md:items-end md:p-5" data-filter-form>
        <label class="grid gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Buscar curso</span>
          <input
            data-filter-query
            value="<?= e($_GET['q'] ?? '') ?>"
            type="search"
            placeholder="Diseño, hábitos, software, dibujo…"
            class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange/60"
          >
        </label>

        <label class="grid gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Nivel</span>
          <select data-filter-level class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60">
            <option value="">Todos los niveles</option>
            <?php foreach (['Inicial', 'Intermedio', 'Avanzado'] as $level): ?>
              <option value="<?= e($level) ?>" <?= (($_GET['nivel'] ?? '') === $level) ? 'selected' : '' ?>><?= e($level) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <button type="button" data-filter-clear class="min-h-[46px] rounded-aula border border-aula-orange/30 bg-aula-orange/10 px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg">
          Limpiar
        </button>
      </form>

      <div class="mt-8 flex flex-wrap gap-2" data-category-buttons>
        <button type="button" data-category="" class="catalog-category-button rounded-full border px-4 py-2 text-xs font-bold transition">Todos</button>
        <?php foreach ($allCategories as $category): ?>
          <button type="button" data-category="<?= e($category['id']) ?>" class="catalog-category-button rounded-full border px-4 py-2 text-xs font-bold transition">
            <?= e($category['name']) ?>
            <span class="ml-1 text-[10px] opacity-60"><?= e(category_count($allCourses, $category['id'])) ?></span>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="mt-10 flex flex-col gap-3 border-b border-white/10 pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Resultados</p>
          <h2 class="mt-2 font-editorial text-3xl font-semibold text-aula-cream sm:text-4xl" data-filter-title>Todos los cursos</h2>
        </div>
        <span class="text-xs text-white/40" data-filter-count><?= e(count($allCourses)) ?> cursos</span>
      </div>

      <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-course-grid>
        <?php foreach ($allCourses as $course): ?>
          <?php require PROJECT_ROOT . '/includes/components/course-card.php'; ?>
        <?php endforeach; ?>
      </div>

      <div class="mt-8 hidden rounded-aula-lg border border-white/10 bg-aula-surface/50 p-8" data-filter-empty>
        <h3 class="font-editorial text-3xl font-semibold text-aula-cream">No encontramos cursos con esos filtros.</h3>
        <p class="mt-3 text-sm text-white/40">Prueba otro término, nivel o categoría.</p>
      </div>
    </section>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
