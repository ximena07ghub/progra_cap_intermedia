<?php
require_once __DIR__ . '/config/app.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$course = course_by_slug($slug) ?? (courses()[0] ?? null);

if (!$course) {
    http_response_code(404);
    exit('No hay cursos disponibles.');
}

$pageTitle = $course['title'] . ' | AulaGo';
$pageDescription = $course['description'];
$reviews = portal_data()['courseReviews'][$course['slug']] ?? [];
$averageRating = '—';
if ($reviews) {
    $sum = array_sum(array_map(fn(array $review): int => (int) $review['rating'], $reviews));
    $averageRating = number_format($sum / count($reviews), 1);
}

$relatedCourses = array_values(array_filter(courses(), fn(array $item): bool => $item['id'] !== $course['id']));
usort($relatedCourses, function (array $a, array $b) use ($course): int {
    return (($b['categoryId'] === $course['categoryId']) <=> ($a['categoryId'] === $course['categoryId']));
});
$relatedCourses = array_slice($relatedCourses, 0, 3);

if (!is_authenticated()) {
    $purchaseTarget = url('login.php?redirect=' . rawurlencode(url('curso.php?slug=' . $course['slug'])));
    $purchaseLabel = 'Comenzar curso';
} elseif (user_role() === 'estudiante') {
    $purchaseTarget = url('estudiante/#cursos');
    $purchaseLabel = 'Comprar curso';
} else {
    $purchaseTarget = role_home_url();
    $purchaseLabel = 'Volver a mi espacio';
}

$lessons = [
    ['Introducción y mapa del curso', 'Conoce el objetivo de la ruta y cómo aprovechar cada módulo.', '10 min'],
    ['Conceptos esenciales', 'Construye una base clara antes de pasar a la práctica.', '18 min'],
    ['Práctica guiada', 'Aplica lo aprendido mediante una actividad acompañada paso a paso.', '24 min'],
    ['Proyecto y próximos pasos', 'Integra los conceptos y define cómo continuar aprendiendo.', '28 min'],
];

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main class="flex-1">
    <section class="border-b border-white/10">
      <div class="mx-auto grid max-w-aula gap-10 px-6 py-14 lg:grid-cols-[.88fr_1.12fr] lg:px-8 lg:py-20">
        <div>
          <div class="relative overflow-hidden border border-white/10 bg-aula-green-dark shadow-aula">
            <img src="<?= e(asset('images/' . $course['image'])) ?>" alt="<?= e($course['title']) ?>" class="aspect-[4/3] h-full w-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/70 via-transparent to-transparent"></div>
            <span class="absolute left-5 top-5 bg-aula-bg/75 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green backdrop-blur">
              <?= e($course['category']) ?>
            </span>
          </div>

          <div class="flex flex-col gap-4 border-x border-b border-white/10 bg-aula-surface/70 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <span class="block text-[10px] uppercase tracking-[0.12em] text-white/35">Acceso completo</span>
              <strong class="mt-1 block text-xl text-aula-cream"><?= e($course['price']) ?></strong>
            </div>
            <a href="<?= e($purchaseTarget) ?>" class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5">
              <?= e($purchaseLabel) ?>
            </a>
          </div>
        </div>

        <div class="self-center">
          <div class="flex flex-wrap gap-2">
            <span class="border border-aula-green/30 bg-aula-green/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green"><?= e($course['category']) ?></span>
            <span class="border border-aula-yellow/30 bg-aula-yellow/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-yellow"><?= e($course['level']) ?></span>
          </div>

          <h1 class="mt-5 font-editorial text-5xl font-semibold leading-[0.95] text-aula-cream sm:text-6xl lg:text-7xl"><?= e($course['title']) ?></h1>
          <p class="mt-6 max-w-2xl text-base leading-8 text-white/50"><?= e($course['description']) ?></p>

          <div class="mt-8 border-l border-aula-orange pl-4">
            <span class="block text-[10px] uppercase tracking-[0.12em] text-white/35">Instructor</span>
            <strong class="mt-1 block text-sm text-aula-cream"><?= e($course['instructor']) ?></strong>
          </div>

          <div class="mt-9 grid grid-cols-3 gap-4 border-y border-white/10 py-6">
            <div><strong class="block text-2xl text-aula-cream"><?= e($averageRating) ?></strong><span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Calificación</span></div>
            <div><strong class="block text-2xl text-aula-cream"><?= e($course['lessons']) ?></strong><span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Lecciones</span></div>
            <div><strong class="block text-2xl text-aula-cream"><?= e($course['duration']) ?></strong><span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Contenido</span></div>
          </div>

          <div class="mt-8">
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green">Sobre este curso</p>
            <h2 class="mt-3 font-editorial text-3xl text-aula-cream sm:text-4xl">Una ruta práctica para profundizar en <?= e(text_lower($course['accent'])) ?>.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45">Combina explicaciones claras, actividades breves y práctica guiada. Esta estructura queda lista para recibir módulos y lecciones desde MySQL.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
      <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Contenido</p>
          <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Lo que encontrarás dentro.</h2>
          <p class="mt-4 max-w-sm text-sm leading-7 text-white/40">La interfaz ya está preparada para sustituir este contenido de muestra por módulos y lecciones de base de datos.</p>
        </div>

        <div class="border-t border-white/10">
          <?php foreach ($lessons as $index => $lesson): ?>
            <article class="grid grid-cols-[42px_1fr_auto] gap-4 border-b border-white/10 py-5 sm:items-center">
              <span class="text-[11px] text-aula-orange"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
              <div><h3 class="text-sm font-bold text-aula-cream"><?= e($lesson[0]) ?></h3><p class="mt-1 text-xs leading-5 text-white/40"><?= e($lesson[1]) ?></p></div>
              <span class="text-[10px] font-bold text-white/30"><?= e($lesson[2]) ?></span>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="border-y border-white/10 bg-white/[0.015]">
      <div class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
        <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
          <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Opiniones verificadas</p>
            <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Comentarios de quienes terminaron el curso.</h2>
            <div class="mt-6 flex items-end gap-3"><strong class="text-5xl text-aula-orange"><?= e($averageRating) ?></strong><span class="pb-1 text-sm text-white/35">/ 5 · <?= e(count($reviews)) ?> opiniones</span></div>
          </div>

          <div class="border-t border-white/10">
            <?php if ($reviews): ?>
              <?php foreach ($reviews as $review): ?>
                <article class="grid grid-cols-[44px_1fr] gap-4 border-b border-white/10 py-5">
                  <span class="grid h-11 w-11 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold"><?= e($review['avatar']) ?></span>
                  <div>
                    <div class="flex flex-wrap items-center justify-between gap-2"><strong class="text-sm"><?= e($review['name']) ?></strong><span class="text-xs text-aula-yellow"><?= str_repeat('★', (int) $review['rating']) ?><span class="text-white/15"><?= str_repeat('★', 5 - (int) $review['rating']) ?></span></span></div>
                    <span class="mt-1 block text-[10px] uppercase tracking-[0.08em] text-white/30">Curso completado · <?= e(format_date_es($review['completedAt'])) ?></span>
                    <p class="mt-3 text-sm leading-7 text-white/50"><?= e($review['comment']) ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="py-8 text-sm text-white/35">Todavía no hay opiniones de estudiantes que hayan terminado este curso.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="border-y border-white/10 bg-white/[0.02]">
      <div class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
        <div class="mb-8 flex items-end justify-between gap-4">
          <div><p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">También podría interesarte</p><h2 class="mt-3 font-editorial text-4xl text-aula-cream">Continúa explorando.</h2></div>
          <a href="<?= e(url('cursos.php')) ?>" class="hidden text-xs font-bold uppercase tracking-[0.08em] text-aula-orange sm:block">Ver catálogo →</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($relatedCourses as $course): ?>
            <?php require PROJECT_ROOT . '/includes/components/course-card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
