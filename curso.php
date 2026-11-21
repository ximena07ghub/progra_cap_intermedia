<?php
require_once __DIR__ . '/config/app.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$course = course_by_slug($slug);

if (!$course) {
    http_response_code(404);
    exit('No hay cursos disponibles.');
}

$pageTitle = $course['title'] . ' | AulaGo';
$pageDescription = $course['description'];
$reviews = public_course_reviews((int) $course['databaseId']);
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

$priceInfo = course_price_info($course);
$purchaseMode = 'link';
$checkoutTarget = url('estudiante/checkout.php?slug=' . rawurlencode($course['slug']));

if (!is_authenticated()) {
    $purchaseTarget = url('login.php?redirect=' . rawurlencode($checkoutTarget));
    $purchaseLabel = $priceInfo['free'] ? 'Iniciar sesión para inscribirme' : 'Comprar curso';
} elseif (user_role() === 'estudiante') {
    if (student_owns_course($course['slug'])) {
        $purchaseTarget = url('estudiante/?course=' . rawurlencode($course['slug']) . '#cursos');
        $purchaseLabel = 'Ir a mis cursos';
    } elseif ($priceInfo['free']) {
        $purchaseTarget = url('actions/enroll-free.php');
        $purchaseLabel = 'Inscribirme gratis';
        $purchaseMode = 'free-form';
    } else {
        $purchaseTarget = $checkoutTarget;
        $purchaseLabel = 'Comprar curso';
    }
} else {
    $purchaseTarget = role_home_url();
    $purchaseLabel = 'Volver a mi espacio';
}

$lessons = public_course_lessons((int) $course['databaseId']);
$studentLessons = [];
$studentMessage = $_SESSION['estudiante_curso_mensaje'] ?? null;
unset($_SESSION['estudiante_curso_mensaje']);

if (is_authenticated() && user_role() === 'estudiante' && student_owns_course($course['slug'])) {
    $studentLessons = student_course_lessons((int) $course['databaseId']);
}

$isStudentClassroom = is_authenticated()
    && user_role() === 'estudiante'
    && student_owns_course($course['slug']);

$studentProgress = 0;
$studentCompleted = 0;
$selectedLesson = null;
$selectedLessonIndex = -1;

$studentCertificate = null;

if ($isStudentClassroom) {
    $studentLessons = student_course_lessons((int) $course['databaseId']);
    $studentCompleted = count(array_filter(
        $studentLessons,
        fn(array $item): bool => (int) ($item['completada'] ?? 0) === 1
    ));
    $studentProgress = count($studentLessons) > 0
        ? (int) round(($studentCompleted / count($studentLessons)) * 100)
        : 0;

    if ($studentProgress === 100) {
        require_once PROJECT_ROOT . '/models/conexion.php';
        require_once PROJECT_ROOT . '/models/EstudiantePortal.php';

        $portalConfig = require PROJECT_ROOT . '/config/database.local.php';
        $portalConexion = new Conexion($portalConfig);
        $portalModel = new EstudiantePortal($portalConexion->obtenerConexion());
        $certificates = $portalModel->listarCertificados((int) ($_SESSION['usuario_id'] ?? 0));

        foreach ($certificates as $certificate) {
            if ((int) ($certificate['id_curso'] ?? 0) === (int) $course['databaseId']) {
                $studentCertificate = $certificate;
                break;
            }
        }
    }

    $requestedLesson = filter_var(
        $_GET['leccion'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    foreach ($studentLessons as $index => $item) {
        if ($requestedLesson !== false && $requestedLesson !== null && (int) $item['id_leccion'] === (int) $requestedLesson) {
            $selectedLesson = $item;
            $selectedLessonIndex = $index;
            break;
        }
    }

    if ($selectedLesson === null && $studentLessons !== []) {
        foreach ($studentLessons as $index => $item) {
            if ((int) $item['completada'] !== 1) {
                $selectedLesson = $item;
                $selectedLessonIndex = $index;
                break;
            }
        }
    }

    if ($selectedLesson === null && $studentLessons !== []) {
        $selectedLesson = $studentLessons[0];
        $selectedLessonIndex = 0;
    }
}

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<style>
  .aulago-classroom-shell{max-width:1320px;margin:0 auto;padding:28px 24px 42px}
  .aulago-classroom-breadcrumb{display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-bottom:24px;font-size:12px;color:rgba(245,240,232,.46)}
  .aulago-classroom-breadcrumb strong{color:#f5f0e8}
  .aulago-classroom-layout{display:grid;grid-template-columns:minmax(0,1fr);gap:22px;align-items:start}
  .aulago-classroom-sidebar{display:grid;gap:16px}
  .aulago-classroom-progress,.aulago-classroom-level,.aulago-classroom-lesson{border:1px solid rgba(255,255,255,.11);border-radius:16px;background:rgba(25,22,27,.66);overflow:hidden}
  .aulago-classroom-progress{padding:22px}
  .aulago-classroom-level summary{padding:18px 20px}
  .aulago-classroom-level-row{min-height:68px}
  .aulago-classroom-level-row:last-child{border-bottom:0}
  .aulago-classroom-level-row.is-current{margin:0 9px 9px;min-height:66px;border:1px solid rgba(232,137,80,.78);border-radius:12px;background:rgba(232,137,80,.11)}
  .aulago-classroom-lesson{padding:22px}
  .aulago-classroom-video{width:100%;aspect-ratio:16/9;margin-top:18px;overflow:hidden;border:1px solid rgba(255,255,255,.10);border-radius:13px;background:#000}
  .aulago-classroom-video iframe{display:block;width:100%;height:100%}
  .aulago-classroom-actions{border:1px solid rgba(255,255,255,.10);border-radius:12px}
  @media(min-width:1024px){
    .aulago-classroom-layout{grid-template-columns:minmax(300px,34%) minmax(0,66%);gap:28px}
    .aulago-classroom-sidebar{position:sticky;top:92px}
    .aulago-classroom-lesson{padding:24px}
  }
</style>
<div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main class="flex-1">
    <?php if ($isStudentClassroom): ?>
      <?php if ($studentMessage): ?>
        <div class="mx-auto max-w-aula px-6 pt-5 lg:px-8">
          <p class="border border-aula-green/20 bg-aula-green/5 px-4 py-3 text-sm text-aula-green"><?= e($studentMessage) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($studentProgress === 100): ?>
      <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-14">
        <div class="relative overflow-hidden border border-aula-green/25 bg-aula-surface/60 px-6 py-12 text-center shadow-aula sm:px-10 lg:py-16">
          <div class="absolute inset-x-0 top-0 h-1 bg-aula-green"></div>
          <div class="mx-auto grid h-20 w-20 place-items-center rounded-full border border-aula-yellow/30 bg-aula-yellow/10 text-4xl text-aula-yellow">★</div>
          <p class="mt-7 text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green">Meta alcanzada</p>
          <h2 class="mt-3 font-editorial text-5xl font-semibold text-aula-cream sm:text-6xl">Curso completado</h2>
          <p class="mx-auto mt-3 max-w-2xl text-lg text-white/55"><?= e($course['title']) ?></p>

          <div class="mx-auto mt-9 max-w-2xl border border-white/10 bg-aula-bg/60 p-5">
            <div class="flex items-center gap-4">
              <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-aula-green text-lg font-bold text-aula-bg">✓</span>
              <div class="h-2 flex-1 overflow-hidden bg-white/10"><div class="h-full w-full bg-aula-green"></div></div>
              <strong class="text-2xl text-aula-cream">100%</strong>
            </div>
            <p class="mt-4 text-sm text-white/45">Has completado las <?= e(count($studentLessons)) ?> lecciones de este curso.</p>
          </div>

          <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <?php if ($studentCertificate): ?>
              <a href="<?= e(url('estudiante/certificado.php?codigo=' . rawurlencode((string) $studentCertificate['codigo']))) ?>" class="inline-flex min-h-12 items-center justify-center bg-aula-green px-7 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Ver certificado →</a>
            <?php else: ?>
              <a href="<?= e(url('estudiante/#certificados')) ?>" class="inline-flex min-h-12 items-center justify-center bg-aula-green px-7 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Ir a certificados →</a>
            <?php endif; ?>
            <a href="<?= e(url('estudiante/#cursos')) ?>" class="inline-flex min-h-12 items-center justify-center border border-white/15 px-7 text-xs font-bold uppercase tracking-[0.08em] text-white/60">Volver a mis cursos</a>
          </div>

          <p class="mt-7 text-xs leading-5 text-white/30">Tu progreso final quedó registrado. Las lecciones completadas ya no pueden volver a marcarse como pendientes.</p>
        </div>
      </section>
      <?php else: ?>
      <section class="aulago-classroom-shell">
        <nav class="aulago-classroom-breadcrumb" aria-label="Ruta del curso">
          <a href="<?= e(url('estudiante/#cursos')) ?>" class="transition hover:text-aula-cream">← Mis cursos</a>
          <span>/</span>
          <span><?= e($course['title']) ?></span>
          <?php if ($selectedLesson): ?>
            <span>/</span>
            <strong><?= e($selectedLesson['titulo']) ?></strong>
          <?php endif; ?>
        </nav>

        <?php if ($studentLessons === []): ?>
          <div class="border border-dashed border-white/10 p-8 text-sm text-white/35">El instructor todavía no ha agregado lecciones a este curso.</div>
        <?php else: ?>
          <?php
          $levels = [];
          foreach ($studentLessons as $lessonItem) {
              $levelId = (int) $lessonItem['id_nivel'];
              if (!isset($levels[$levelId])) {
                  $levels[$levelId] = [
                      'id' => $levelId,
                      'title' => (string) $lessonItem['nivel'],
                      'description' => (string) ($lessonItem['descripcion_nivel'] ?? ''),
                      'order' => (int) $lessonItem['orden_nivel'],
                      'lessons' => [],
                      'completed' => 0,
                  ];
              }
              $levels[$levelId]['lessons'][] = $lessonItem;
              if ((int) $lessonItem['completada'] === 1) {
                  $levels[$levelId]['completed']++;
              }
          }

          $selectedLevelId = (int) ($selectedLesson['id_nivel'] ?? 0);
          $previousLesson = $selectedLessonIndex > 0 ? $studentLessons[$selectedLessonIndex - 1] : null;
          $nextLesson = $selectedLessonIndex >= 0 && $selectedLessonIndex < count($studentLessons) - 1
              ? $studentLessons[$selectedLessonIndex + 1]
              : null;
          ?>

          <div class="aulago-classroom-layout">
            <aside class="aulago-classroom-sidebar">
              <div class="aulago-classroom-progress">
                <div class="flex items-end justify-between gap-4">
                  <div>
                    <p class="text-sm font-bold text-aula-cream">Tu progreso</p>
                    <p class="mt-2 text-sm text-white/45"><?= e($studentCompleted) ?> de <?= e(count($studentLessons)) ?> lecciones</p>
                  </div>
                  <strong class="font-editorial text-3xl text-aula-cream"><?= e($studentProgress) ?>%</strong>
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                  <div class="h-full rounded-full bg-aula-orange" style="width: <?= e($studentProgress) ?>%"></div>
                </div>
              </div>

              <div class="space-y-3">
                <?php foreach ($levels as $level): ?>
                  <?php $levelOpen = $level['id'] === $selectedLevelId; ?>
                  <details class="aulago-classroom-level group" <?= $levelOpen ? 'open' : '' ?>>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-4">
                      <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">Nivel <?= e($level['order']) ?></p>
                        <h3 class="mt-1 truncate text-sm font-bold text-aula-cream"><?= e($level['title']) ?></h3>
                        <?php if ($levelOpen && $level['description'] !== ''): ?>
                          <p class="mt-1 line-clamp-2 text-[11px] leading-5 text-white/30"><?= e($level['description']) ?></p>
                        <?php endif; ?>
                      </div>
                      <div class="flex shrink-0 items-center gap-3">
                        <span class="text-xs text-white/45"><?= e($level['completed']) ?> / <?= e(count($level['lessons'])) ?></span>
                        <span class="text-white/35 transition group-open:rotate-180">⌄</span>
                      </div>
                    </summary>

                    <div class="border-t border-white/10">
                      <?php foreach ($level['lessons'] as $lesson): ?>
                        <?php
                        $isSelected = (int) ($selectedLesson['id_leccion'] ?? 0) === (int) $lesson['id_leccion'];
                        $isCompleted = (int) $lesson['completada'] === 1;
                        ?>
                        <a
                          href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']) . '&leccion=' . $lesson['id_leccion'])) ?>"
                          class="aulago-classroom-level-row <?= $isSelected ? 'is-current' : '' ?> grid grid-cols-[34px_1fr_auto] items-center gap-3 border-b border-white/[0.07] px-4 py-3 <?= $isSelected ? '' : 'hover:bg-white/[0.025]' ?>"
                        >
                          <span class="grid h-8 w-8 place-items-center rounded-full border text-[11px] font-bold <?= $isCompleted ? 'border-aula-green/40 bg-aula-green/15 text-aula-green' : ($isSelected ? 'border-aula-orange/60 text-aula-orange-soft' : 'border-white/15 text-white/35') ?>">
                            <?= $isCompleted ? '✓' : e(str_pad((string) $lesson['orden'], 2, '0', STR_PAD_LEFT)) ?>
                          </span>
                          <span class="min-w-0">
                            <strong class="block truncate text-xs text-aula-cream"><?= e($lesson['titulo']) ?></strong>
                            <small class="mt-1 block text-[10px] <?= $isCompleted ? 'text-aula-green/70' : 'text-white/25' ?>"><?= $isCompleted ? 'Completada' : ($isSelected ? 'En curso' : 'Pendiente') ?></small>
                          </span>
                          <?php if ($isSelected): ?><span class="text-aula-orange-soft">›</span><?php endif; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  </details>
                <?php endforeach; ?>
              </div>
            </aside>

            <?php if ($selectedLesson): ?>
              <article class="aulago-classroom-lesson">
                <header class="flex flex-wrap items-start justify-between gap-5">
                  <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-orange-soft">Nivel <?= e($selectedLesson['orden_nivel']) ?> · Lección <?= e($selectedLesson['orden']) ?></p>
                    <h2 class="mt-2 font-editorial text-4xl font-semibold text-aula-cream sm:text-5xl"><?= e($selectedLesson['titulo']) ?></h2>
                    <p class="mt-3 text-xs text-white/35">Lección <?= e($selectedLessonIndex + 1) ?> de <?= e(count($studentLessons)) ?> · <?= !empty($selectedLesson['url_video']) ? 'Video' : 'Contenido' ?></p>
                  </div>
                  <span class="border px-3 py-1.5 text-[10px] font-bold uppercase <?= (int) $selectedLesson['completada'] === 1 ? 'border-aula-green/30 bg-aula-green/10 text-aula-green' : 'border-aula-orange/30 bg-aula-orange/10 text-aula-orange-soft' ?>">
                    <?= (int) $selectedLesson['completada'] === 1 ? 'Completada' : 'En curso' ?>
                  </span>
                </header>

                <?php $lessonEmbed = youtube_embed_url($selectedLesson['url_video'] ?? null); ?>
                <?php if ($lessonEmbed): ?>
                  <div class="aulago-classroom-video">
                    <iframe
                      src="<?= e($lessonEmbed) ?>"
                      title="<?= e($selectedLesson['titulo']) ?>"
                      class="h-full w-full"
                      referrerpolicy="strict-origin-when-cross-origin"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen
                    ></iframe>
                  </div>
                <?php elseif (!empty($selectedLesson['url_video'])): ?>
                  <div class="mx-5 border border-white/10 bg-aula-bg/50 p-5 sm:mx-7">
                    <p class="text-xs text-white/40">Este recurso no es una URL de YouTube compatible con el reproductor integrado.</p>
                    <a href="<?= e($selectedLesson['url_video']) ?>" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-xs font-bold text-aula-orange-soft">Abrir recurso externo ↗</a>
                  </div>
                <?php endif; ?>

                <div class="pt-5">
                  <div class="border-b border-white/10 pb-3">
                    <span class="border-b-2 border-aula-orange pb-3 text-sm font-bold text-aula-cream">Descripción</span>
                  </div>

                  <div class="mt-5 whitespace-pre-line text-sm leading-7 text-white/55"><?= e($selectedLesson['contenido'] ?: 'El instructor no agregó contenido escrito para esta lección.') ?></div>

                  <div class="aulago-classroom-actions mt-7 flex flex-wrap items-center justify-between gap-4 bg-aula-bg/35 p-4">
                    <span class="text-xs text-white/40"><?= e($studentCompleted) ?> lecciones completadas</span>

                    <?php if ((int) $selectedLesson['completada'] === 1): ?>
                      <span class="inline-flex min-h-11 items-center justify-center border border-aula-green/30 bg-aula-green/10 px-5 text-xs font-extrabold text-aula-green">✓ Lección completada</span>
                    <?php else: ?>
                      <form action="<?= e(url('actions/progreso-curso.php')) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id_curso" value="<?= e($course['databaseId']) ?>">
                        <input type="hidden" name="id_leccion" value="<?= e($selectedLesson['id_leccion']) ?>">
                        <input type="hidden" name="slug" value="<?= e($course['slug']) ?>">
                        <input type="hidden" name="id_leccion_actual" value="<?= e($selectedLesson['id_leccion']) ?>">
                        <input type="hidden" name="completada" value="1">
                        <button type="submit" class="min-h-11 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg">Marcar como completada ✓</button>
                      </form>
                    <?php endif; ?>
                  </div>

                  <nav class="mt-5 flex items-center justify-between gap-4 border-t border-white/10 pt-5">
                    <?php if ($previousLesson): ?>
                      <a href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']) . '&leccion=' . $previousLesson['id_leccion'])) ?>" class="text-xs font-bold text-white/45 hover:text-aula-cream">← Lección anterior</a>
                    <?php else: ?><span></span><?php endif; ?>

                    <?php if ($nextLesson): ?>
                      <a href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']) . '&leccion=' . $nextLesson['id_leccion'])) ?>" class="border border-aula-orange/45 px-4 py-2.5 text-xs font-bold text-aula-orange-soft hover:bg-aula-orange/10">Siguiente lección →</a>
                    <?php endif; ?>
                  </nav>
                </div>
              </article>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <section class="border-y border-white/10 bg-white/[0.015]">
        <div class="mx-auto grid max-w-aula gap-8 px-6 py-10 lg:grid-cols-[1.1fr_.9fr] lg:px-8">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Sobre este curso</p>
            <h2 class="mt-2 font-editorial text-3xl text-aula-cream"><?= e($course['title']) ?></h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45"><?= e($course['description']) ?></p>
            <div class="mt-5 flex flex-wrap gap-x-8 gap-y-3 text-xs text-white/35"><span>Instructor · <strong class="text-aula-cream"><?= e($course['instructor']) ?></strong></span><span><?= e(count($studentLessons)) ?> lecciones</span><span><?= $studentProgress === 100 ? 'Completado' : 'En progreso' ?></span></div>
          </div>
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Opiniones</p>
            <div class="mt-3 flex items-end gap-3"><strong class="font-editorial text-5xl text-aula-orange"><?= e($averageRating) ?></strong><span class="pb-1 text-xs text-white/30">/ 5 · <?= e(count($reviews)) ?> opiniones</span></div>
            <?php if (!$reviews): ?><p class="mt-4 text-sm text-white/35">Todavía no hay opiniones de estudiantes que hayan terminado este curso.</p><?php endif; ?>
          </div>
        </div>
      </section>
    <?php else: ?>

    <?php if ($studentLessons !== []): ?>
      <section class="border-b border-aula-green/20 bg-aula-green-dark/20">
        <div class="mx-auto max-w-aula px-6 py-8 lg:px-8">
          <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Tu aprendizaje</p>
          <h2 class="mt-2 font-editorial text-3xl font-semibold">Continúa y marca cada lección al terminarla.</h2>
          <?php if ($studentMessage): ?><p class="mt-4 text-sm text-aula-green"><?= e($studentMessage) ?></p><?php endif; ?>
          <div class="mt-6 space-y-3">
            <?php $nivelActual = null; ?>
            <?php foreach ($studentLessons as $leccion): ?>
              <?php if ($nivelActual !== $leccion['id_nivel']): $nivelActual = $leccion['id_nivel']; ?>
                <h3 class="pt-4 text-xs font-bold uppercase tracking-[0.12em] text-aula-orange-soft">Nivel <?= e($leccion['orden_nivel']) ?> · <?= e($leccion['nivel']) ?></h3>
              <?php endif; ?>
              <article class="border border-white/10 bg-aula-bg/60 p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="min-w-0 flex-1">
                    <p class="text-[10px] uppercase text-white/30">Lección <?= e($leccion['orden']) ?></p>
                    <h4 class="mt-1 text-lg font-semibold"><?= e($leccion['titulo']) ?></h4>
                    <?php if (!empty($leccion['contenido'])): ?><div class="mt-3 whitespace-pre-line text-sm leading-6 text-white/50"><?= e($leccion['contenido']) ?></div><?php endif; ?>
                    <?php if (!empty($leccion['url_video'])): ?><a href="<?= e($leccion['url_video']) ?>" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-xs font-bold text-[#9fcbd5]">Abrir video →</a><?php endif; ?>
                  </div>
                  <form action="<?= e(url('actions/progreso-curso.php')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id_curso" value="<?= e($course['databaseId']) ?>">
                    <input type="hidden" name="id_leccion" value="<?= e($leccion['id_leccion']) ?>">
                    <input type="hidden" name="slug" value="<?= e($course['slug']) ?>">
                    <input type="hidden" name="completada" value="<?= (int) $leccion['completada'] === 1 ? '0' : '1' ?>">
                    <button type="submit" class="border px-4 py-2 text-xs font-bold <?= (int) $leccion['completada'] === 1 ? 'border-aula-green/40 text-aula-green' : 'border-white/15 text-white/60' ?>">
                      <?= (int) $leccion['completada'] === 1 ? '✓ Completada' : 'Marcar completada' ?>
                    </button>
                  </form>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section class="border-b border-white/10">
      <div class="mx-auto grid max-w-aula gap-10 px-6 py-14 lg:grid-cols-[.88fr_1.12fr] lg:px-8 lg:py-20">
        <div>
          <div class="relative overflow-hidden border border-white/10 bg-aula-green-dark shadow-aula">
            <img src="<?= e(course_image_url($course)) ?>" alt="<?= e($course['title']) ?>" class="aspect-[4/3] h-full w-full object-cover opacity-80">
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
            <?php if ($purchaseMode === 'free-form'): ?>
              <form action="<?= e($purchaseTarget) ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="slug" value="<?= e($course['slug']) ?>">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5"><?= e($purchaseLabel) ?></button>
              </form>
            <?php else: ?>
              <a href="<?= e($purchaseTarget) ?>" class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5">
                <?= e($purchaseLabel) ?>
              </a>
            <?php endif; ?>
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
            <h2 class="mt-3 font-editorial text-3xl text-aula-cream sm:text-4xl">Una ruta organizada para avanzar por niveles y lecciones.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45">El contenido que ves aquí corresponde a los niveles y lecciones que el instructor ha publicado en AulaGo.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
      <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Contenido</p>
          <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Lo que encontrarás dentro.</h2>
          <p class="mt-4 max-w-sm text-sm leading-7 text-white/40">Este contenido se obtiene directamente de la estructura creada por el instructor.</p>
        </div>

        <div class="border-t border-white/10">
          <?php if ($lessons): ?>
            <?php foreach ($lessons as $index => $lesson): ?>
              <article class="grid grid-cols-[42px_1fr_auto] gap-4 border-b border-white/10 py-5 sm:items-center">
                <span class="text-[11px] text-aula-orange"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                <div>
                  <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-aula-green"><?= e($lesson['nivel']) ?></span>
                  <h3 class="mt-1 text-sm font-bold text-aula-cream"><?= e($lesson['titulo']) ?></h3>
                  <?php if (!empty($lesson['contenido'])): ?>
                    <p class="mt-1 text-xs leading-5 text-white/40"><?= e($lesson['contenido']) ?></p>
                  <?php endif; ?>
                </div>
                <span class="text-[10px] font-bold text-white/30"><?= !empty($lesson['url_video']) ? 'Video' : 'Lección' ?></span>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="border-b border-white/10 py-8 text-sm text-white/35">El instructor todavía no ha publicado lecciones para este curso.</p>
          <?php endif; ?>
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

    <?php endif; ?>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
