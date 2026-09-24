<?php
require_once dirname(__DIR__) . '/config/app.php';
require_role('estudiante');

$pageTitle = 'Mi espacio | AulaGo';
$pageDescription = 'Workspace del estudiante de AulaGo.';
$pageScripts = ['workspace.js'];
$bodyClass = 'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased lg:overflow-hidden';

$enrolled = enrolled_courses();
$allCourses = courses();
$portal = portal_data();
$kardex = $portal['kardexRecords'] ?? [];
$conversations = $portal['conversations'] ?? [];
$recommended = array_slice(array_values(array_filter($allCourses, fn(array $course): bool => !in_array($course['slug'], array_column($enrolled, 'slug'), true))), 0, 4);
$averageProgress = $enrolled ? (int) round(array_sum(array_column($enrolled, 'progress')) / count($enrolled)) : 0;
$completedCount = count($kardex);
$currentCourse = $enrolled[0] ?? null;
$dashboardLessons = [
    ['title' => $currentCourse['nextLesson'] ?? 'Continúa donde te quedaste', 'meta' => 'Lección actual', 'duration' => '18 min', 'state' => 'current'],
    ['title' => 'Práctica guiada y registro', 'meta' => 'Siguiente', 'duration' => '22 min', 'state' => 'next'],
    ['title' => 'Cómo sostener el cambio', 'meta' => 'Después', 'duration' => '17 min', 'state' => 'next'],
    ['title' => 'Cierre y plan personal', 'meta' => 'Final del módulo', 'duration' => '14 min', 'state' => 'next'],
];
$weeklyActivity = [35, 62, 28, 76, 54, 88, 42];
$weekLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
$workspaceRoleLabel = 'Espacio del estudiante';
$workspaceItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => '⌂'],
    ['id' => 'cursos', 'label' => 'Todos los cursos', 'icon' => '▦'],
    ['id' => 'mensajes', 'label' => 'Mensajes', 'icon' => '✉'],
    ['id' => 'kardex', 'label' => 'Kardex', 'icon' => '≡'],
    ['id' => 'certificados', 'label' => 'Certificados', 'icon' => '◇'],
    ['id' => 'cuenta', 'label' => 'Mi cuenta', 'icon' => '○'],
];

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<style>
/*
 * Layout exclusivo del workspace del estudiante.
 * En escritorio las columnas laterales permanecen fijas y solamente
 * el contenido central se desplaza. La barra de scroll central se oculta,
 * pero mouse wheel, touchpad, teclado y gesto táctil siguen funcionando.
 */
@media (min-width: 1024px) {
  html,
  body {
    height: 100%;
    overflow: hidden;
  }

  .student-workspace {
    height: 100vh;
    min-height: 0;
    overflow: hidden;
  }

  .student-workspace > .workspace-sidebar {
    height: 100vh;
    overflow: hidden;
  }

  .student-workspace .workspace-nav {
    overflow: hidden !important;
  }

  .student-workspace-center {
    height: 100vh;
    min-height: 0;
    overflow: hidden;
  }

  .student-workspace-header {
    flex: 0 0 auto;
  }

  .student-main-scroll {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto !important;
    overflow-x: hidden;
    overscroll-behavior: contain;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }

  .student-main-scroll::-webkit-scrollbar {
    width: 0;
    height: 0;
    display: none;
  }

  .student-progress-panel {
    display: flex !important;
    height: 100vh;
    min-height: 0;
    flex-direction: column;
    overflow: hidden !important;
    padding: 16px 18px;
  }

  .student-progress-profile {
    margin-top: 14px !important;
  }

  .student-progress-profile > span:first-child {
    width: 52px;
    height: 52px;
  }

  .student-progress-stats {
    margin-top: 16px !important;
    padding-top: 14px !important;
    padding-bottom: 14px !important;
  }

  .student-progress-activity {
    margin-top: 16px !important;
  }

  .student-progress-chart {
    height: 72px !important;
    margin-top: 10px !important;
  }

  .student-current-course {
    margin-top: 16px !important;
    padding: 13px !important;
  }

  .student-current-course h2 {
    margin-top: 8px !important;
    font-size: 1.08rem !important;
  }

  .student-current-course p:nth-of-type(2) {
    margin-top: 6px !important;
    line-height: 1.15rem !important;
  }

  .student-current-course .student-current-progress {
    margin-top: 10px !important;
  }

  .student-next-achievement {
    margin-top: 14px !important;
    padding-top: 13px !important;
  }

  .student-next-achievement p:last-child {
    margin-top: 7px !important;
    font-size: 0.75rem !important;
    line-height: 1.15rem !important;
  }
}

/* En pantallas de escritorio con poca altura compactamos sin eliminar datos. */
@media (min-width: 1024px) and (max-height: 700px) {
  .student-progress-panel {
    padding-top: 12px;
    padding-bottom: 12px;
  }

  .student-progress-profile {
    margin-top: 10px !important;
  }

  .student-progress-profile > span:first-child {
    width: 46px;
    height: 46px;
  }

  .student-progress-stats,
  .student-progress-activity,
  .student-current-course {
    margin-top: 12px !important;
  }

  .student-progress-stats {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
  }

  .student-progress-chart {
    height: 58px !important;
    margin-top: 7px !important;
  }

  .student-current-course {
    padding: 10px !important;
  }

  .student-next-achievement {
    margin-top: 10px !important;
    padding-top: 10px !important;
  }
}
</style>
<div class="student-workspace min-h-screen bg-aula-bg text-aula-cream lg:grid lg:h-screen lg:grid-cols-[230px_minmax(0,1fr)_320px] lg:overflow-hidden" data-workspace>
  <?php require PROJECT_ROOT . '/includes/workspace/sidebar.php'; ?>

  <section class="student-workspace-center flex min-w-0 flex-col border-white/10 lg:border-r">
    <header class="student-workspace-header border-b border-white/10 bg-aula-bg/95 px-5 py-5 backdrop-blur md:px-7 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green" data-workspace-eyebrow>Tu espacio</p>
          <h1 class="mt-2 font-editorial text-3xl font-semibold text-aula-cream sm:text-4xl" data-workspace-title>Dashboard</h1>
        </div>
        <div class="flex items-center gap-3">
          <a href="<?= e(url('buscar.php')) ?>" class="grid h-10 w-10 place-items-center border border-white/10 text-white/55 transition hover:border-aula-orange/30 hover:text-aula-orange" aria-label="Buscar">⌕</a>
          <a href="<?= e(url('cursos.php')) ?>" class="inline-flex min-h-10 items-center border border-aula-orange/30 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg">Explorar catálogo</a>
        </div>
      </div>
    </header>

    <main class="workspace-main student-main-scroll min-h-0 flex-1 overflow-y-auto px-5 py-6 md:px-7 lg:px-8 lg:py-7">
      <section data-workspace-panel="dashboard" data-title="Dashboard" data-eyebrow="Tu espacio" data-student-dashboard>
        <div class="dashboard-tools grid gap-3 border-b border-white/10 pb-5 sm:grid-cols-[1fr_auto]">
          <label class="relative block">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-white/30" aria-hidden="true">⌕</span>
            <input type="search" class="min-h-11 w-full border border-white/10 bg-white/[0.02] pl-11 pr-4 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-aula-green/50" placeholder="Buscar dentro de tus cursos" data-dashboard-search>
          </label>
          <a href="#cursos" data-workspace-link="cursos" class="inline-flex min-h-11 items-center justify-center border border-white/10 px-5 text-xs font-extrabold text-white/60 transition hover:border-aula-orange/35 hover:text-aula-orange-soft">Ver catálogo completo</a>
        </div>

        <section class="mt-6">
          <div class="flex items-end justify-between gap-4">
            <div>
              <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Cursos en progreso</p>
              <h2 class="mt-2 font-editorial text-2xl font-semibold sm:text-3xl">Elige dónde continuar.</h2>
            </div>
            <span class="hidden text-xs text-white/30 sm:block">Simulación hasta conectar progreso real</span>
          </div>

          <div class="mt-4 grid gap-3 md:grid-cols-3" data-dashboard-course-list>
            <?php foreach ($enrolled as $index => $course): ?>
              <button
                type="button"
                class="dashboard-course-option group grid min-w-0 grid-cols-[72px_minmax(0,1fr)] gap-3 border border-white/10 bg-white/[0.015] p-3 text-left transition hover:border-aula-green/35 hover:bg-white/[0.025] <?= $index === 0 ? 'is-current' : '' ?>"
                data-dashboard-course
                data-course-title="<?= e($course['title']) ?>"
                data-course-category="<?= e($course['category']) ?>"
                data-course-level="<?= e($course['level']) ?>"
                data-course-image="<?= e(asset('images/' . $course['image'])) ?>"
                data-course-progress="<?= e($course['progress']) ?>"
                data-course-next="<?= e($course['nextLesson']) ?>"
                data-course-description="<?= e($course['description']) ?>"
                data-course-href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']))) ?>"
              >
                <img src="<?= e(asset('images/' . $course['image'])) ?>" alt="" class="h-[72px] w-[72px] object-cover opacity-75 transition group-hover:opacity-95">
                <span class="min-w-0 self-center">
                  <span class="block truncate text-[9px] font-bold uppercase tracking-[0.1em] text-aula-green"><?= e($course['category']) ?> · <?= e($course['level']) ?></span>
                  <strong class="mt-1 block truncate text-sm text-white/80"><?= e($course['title']) ?></strong>
                  <span class="mt-2 flex items-center gap-2"><span class="h-1 flex-1 overflow-hidden bg-white/[0.07]"><span class="block h-full bg-aula-orange" style="width: <?= e($course['progress']) ?>%"></span></span><small class="text-[10px] font-bold text-aula-orange-soft"><?= e($course['progress']) ?>%</small></span>
                </span>
              </button>
            <?php endforeach; ?>
          </div>
          <p class="mt-3 hidden text-xs text-aula-orange-soft" data-dashboard-empty>No encontramos coincidencias en tus cursos activos.</p>
        </section>

        <?php if ($currentCourse): ?>
        <section class="mt-7 grid gap-5 xl:grid-cols-[1.08fr_.92fr]" data-dashboard-focus>
          <article class="border border-white/10 bg-white/[0.015]">
            <div class="relative aspect-[16/9] overflow-hidden bg-aula-green-dark">
              <img src="<?= e(asset('images/' . $currentCourse['image'])) ?>" alt="<?= e($currentCourse['title']) ?>" class="h-full w-full object-cover opacity-65" data-dashboard-focus-image>
              <div class="absolute inset-0 bg-gradient-to-t from-aula-bg via-aula-bg/30 to-transparent"></div>
              <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                <div class="flex flex-wrap items-center gap-2 text-[9px] font-bold uppercase tracking-[0.12em]"><span class="text-aula-green" data-dashboard-focus-category><?= e($currentCourse['category']) ?></span><span class="text-white/30">·</span><span class="text-white/45" data-dashboard-focus-level><?= e($currentCourse['level']) ?></span></div>
                <h2 class="mt-2 max-w-xl font-editorial text-3xl font-semibold leading-tight sm:text-4xl" data-dashboard-focus-title><?= e($currentCourse['title']) ?></h2>
              </div>
              <button type="button" class="absolute left-1/2 top-1/2 grid h-14 w-14 -translate-x-1/2 -translate-y-1/2 place-items-center border border-white/20 bg-aula-bg/70 text-lg text-aula-cream backdrop-blur transition hover:border-aula-orange/60 hover:text-aula-orange-soft" aria-label="Continuar lección">▶</button>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-[1fr_auto] sm:items-end sm:p-6">
              <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">Retomar sesión</p>
                <p class="mt-2 text-sm font-semibold text-white/80" data-dashboard-focus-next><?= e($currentCourse['nextLesson']) ?></p>
                <p class="mt-2 max-w-xl text-xs leading-5 text-white/35" data-dashboard-focus-description><?= e($currentCourse['description']) ?></p>
                <div class="mt-4 flex items-center gap-3"><span class="h-1.5 flex-1 overflow-hidden bg-white/[0.07]"><span class="block h-full bg-aula-orange" style="width: <?= e($currentCourse['progress']) ?>%" data-dashboard-focus-progress-bar></span></span><strong class="text-xs text-aula-orange-soft" data-dashboard-focus-progress><?= e($currentCourse['progress']) ?>%</strong></div>
              </div>
              <a href="<?= e(url('curso.php?slug=' . rawurlencode($currentCourse['slug']))) ?>" class="inline-flex min-h-10 items-center justify-center border border-aula-orange/35 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg" data-dashboard-focus-link>Continuar curso →</a>
            </div>
          </article>

          <aside class="border border-white/10 bg-white/[0.012]" aria-label="Próximas lecciones">
            <header class="flex items-center justify-between border-b border-white/10 p-5">
              <div><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#9fcbd5]">Ruta actual</p><h3 class="mt-1 font-editorial text-xl font-semibold">Siguientes lecciones</h3></div>
              <span class="text-[10px] text-white/30"><?= e(count($dashboardLessons)) ?> pendientes</span>
            </header>
            <div class="divide-y divide-white/10" data-dashboard-lesson-list>
              <?php foreach ($dashboardLessons as $index => $lesson): ?>
                <button type="button" class="group grid w-full grid-cols-[36px_1fr_auto] items-center gap-3 px-5 py-4 text-left transition hover:bg-white/[0.025]">
                  <span class="grid h-8 w-8 place-items-center border <?= $index === 0 ? 'border-aula-orange/40 bg-aula-orange/10 text-aula-orange-soft' : 'border-white/10 text-white/35' ?> text-xs"><?= $index === 0 ? '▶' : e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                  <span class="min-w-0"><strong class="block truncate text-xs font-semibold <?= $index === 0 ? 'text-white/85' : 'text-white/60' ?>"><?= e($lesson['title']) ?></strong><small class="mt-1 block text-[10px] text-white/30"><?= e($lesson['meta']) ?></small></span>
                  <span class="text-[10px] text-white/25"><?= e($lesson['duration']) ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </aside>
        </section>
        <?php endif; ?>
      </section>

      <section data-workspace-panel="cursos" data-title="Todos los cursos" data-eyebrow="Catálogo dentro de tu espacio" hidden>
        <div class="mb-6 max-w-2xl"><p class="text-sm leading-7 text-white/45">Consulta toda la colección sin abandonar tu workspace. Los cursos inscritos muestran tu avance y el resto conserva el acceso a la ficha pública antes de comprar.</p></div>
        <div class="border-t border-white/10">
          <?php $enrolledBySlug = array_column($enrolled, null, 'slug'); ?>
          <?php foreach ($allCourses as $course): ?>
            <?php $owned = $enrolledBySlug[$course['slug']] ?? null; ?>
            <article class="grid gap-4 border-b border-white/10 py-5 md:grid-cols-[92px_minmax(0,1fr)_auto] md:items-center">
              <img src="<?= e(asset('images/' . $course['image'])) ?>" alt="<?= e($course['title']) ?>" class="h-20 w-full object-cover opacity-75 md:w-[92px]">
              <div class="min-w-0"><div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.1em]"><span class="text-aula-green"><?= e($course['category']) ?></span><span class="text-white/25">·</span><span class="text-white/35"><?= e($course['level']) ?></span></div><h3 class="mt-2 font-editorial text-xl font-semibold text-white/85"><?= e($course['title']) ?></h3><p class="mt-1 line-clamp-1 text-xs text-white/35"><?= e($course['accent']) ?> · <?= e($course['instructor']) ?></p><div class="mt-2 flex flex-wrap gap-4 text-[11px] text-white/30"><span>★ <?= e($course['rating']) ?></span><span><?= e($course['duration']) ?></span><span><?= e(number_format((int) $course['students'])) ?> estudiantes</span></div></div>
              <div class="md:text-right">
                <?php if ($owned): ?>
                  <strong class="block text-sm text-aula-orange-soft"><?= e($owned['progress']) ?>% completado</strong>
                  <a href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']))) ?>" class="mt-2 inline-block text-xs font-bold text-white/50 hover:text-white">Continuar →</a>
                <?php else: ?>
                  <strong class="block text-sm text-white/70"><?= e($course['price']) ?></strong>
                  <a href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']))) ?>" class="mt-2 inline-block text-xs font-bold text-aula-orange-soft">Ver curso →</a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section data-workspace-panel="mensajes" data-title="Mensajes" data-eyebrow="Conversaciones privadas" hidden data-messages-panel>
        <div class="workspace-message-shell grid min-h-0 border border-white/10 lg:grid-cols-[270px_minmax(0,1fr)]">
          <aside class="conversation-list min-h-0 border-b border-white/10 bg-white/[0.015] lg:border-b-0 lg:border-r">
            <div class="border-b border-white/10 p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-white/35">Conversaciones</p><span class="mt-1 block text-[10px] text-white/25">Tus instructores y seguimiento de cursos</span></div>
            <div class="conversation-list-scroll lg:overflow-y-auto">
              <?php foreach ($conversations as $index => $conversation): ?>
                <?php $lastMessage = $conversation['messages'][count($conversation['messages']) - 1] ?? null; ?>
                <button type="button" class="<?= $index === 0 ? 'is-selected ' : '' ?>conversation-button flex w-full items-center gap-3 border-b border-white/10 p-4 text-left transition hover:bg-white/[0.025]" data-conversation-button data-conversation-id="<?= e($conversation['id']) ?>" data-participant="<?= e($conversation['participant']) ?>" data-course="<?= e($conversation['course']) ?>">
                  <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-aula-green"><?= e($conversation['avatar']) ?></span>
                  <span class="min-w-0"><span class="flex items-center justify-between gap-2"><strong class="truncate text-sm"><?= e($conversation['participant']) ?></strong><small class="shrink-0 text-[9px] text-white/25"><?= e($lastMessage['time'] ?? '') ?></small></span><span class="mt-1 block truncate text-xs text-white/35"><?= e($lastMessage['text'] ?? $conversation['course']) ?></span><span class="mt-1 block truncate text-[10px] text-aula-green/70"><?= e($conversation['course']) ?></span></span>
                </button>
              <?php endforeach; ?>
            </div>
          </aside>
          <?php $activeConversation = $conversations[0] ?? null; ?>
          <section class="flex min-h-0 min-w-0 flex-col" data-message-thread>
            <header class="flex items-center gap-3 border-b border-white/10 p-4 sm:p-5"><span class="grid h-10 w-10 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-aula-green" data-thread-avatar><?= e($activeConversation['avatar'] ?? 'AG') ?></span><div class="min-w-0"><strong class="block truncate text-sm" data-thread-name><?= e($activeConversation['participant'] ?? 'Conversación') ?></strong><span class="mt-1 block truncate text-xs text-white/35" data-thread-course><?= e($activeConversation['course'] ?? '') ?></span></div></header>
            <div class="message-stream min-h-0 flex-1 space-y-4 overflow-y-auto p-4 sm:p-5" data-message-stream>
              <?php foreach (($activeConversation['messages'] ?? []) as $message): ?>
                <article class="max-w-[82%] p-4 text-sm leading-6 <?= $message['from'] === 'me' ? 'ml-auto bg-aula-green-dark text-white' : 'bg-white/[0.05] text-white/75' ?>"><p><?= e($message['text']) ?></p><span class="mt-2 block text-[10px] uppercase tracking-[0.08em] text-white/35"><?= e(format_date_es($message['date'])) ?> · <?= e($message['time']) ?></span></article>
              <?php endforeach; ?>
            </div>
            <form class="grid gap-3 border-t border-white/10 p-4 sm:grid-cols-[1fr_auto] sm:p-5" data-message-form><input class="min-h-11 min-w-0 border border-white/10 bg-aula-bg px-4 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange" placeholder="Escribe un mensaje privado"><button type="submit" class="min-h-11 bg-aula-orange px-6 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Enviar</button></form>
          </section>
        </div>
        <script type="application/json" data-conversation-data><?= json_encode($conversations, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
      </section>

      <section data-workspace-panel="kardex" data-title="Kardex" data-eyebrow="Historial académico" hidden>
        <div class="mb-6 max-w-2xl"><p class="text-sm leading-7 text-white/45">Cursos completados, calificación final y referencia de certificado en una sola vista.</p></div>
        <div class="overflow-x-auto border border-white/10">
          <table class="min-w-full border-collapse text-left text-sm"><thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35"><tr><th class="px-5 py-4">Curso</th><th class="px-5 py-4">Categoría</th><th class="px-5 py-4">Finalizado</th><th class="px-5 py-4">Calificación</th><th class="px-5 py-4">Certificado</th></tr></thead><tbody class="divide-y divide-white/10">
            <?php foreach ($kardex as $record): ?><tr><td class="px-5 py-4"><strong class="block text-white/80"><?= e($record['course']) ?></strong><span class="mt-1 block text-xs text-white/30"><?= e($record['instructor']) ?></span></td><td class="px-5 py-4 text-white/45"><?= e($record['category']) ?></td><td class="px-5 py-4 text-white/45"><?= e(format_date_es($record['finishedAt'])) ?></td><td class="px-5 py-4 font-bold text-aula-green"><?= e($record['grade']) ?>/100</td><td class="px-5 py-4 text-xs text-aula-orange-soft"><?= e($record['certificateId']) ?></td></tr><?php endforeach; ?>
          </tbody></table>
        </div>
      </section>

      <section data-workspace-panel="certificados" data-title="Certificados" data-eyebrow="Logros completados" hidden>
        <div class="grid gap-4 md:grid-cols-2">
          <?php foreach ($kardex as $record): ?>
            <article class="border border-white/10 bg-white/[0.015] p-5"><div class="flex items-start justify-between gap-4"><span class="text-2xl text-aula-yellow">◇</span><span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30"><?= e($record['certificateId']) ?></span></div><h2 class="mt-8 font-editorial text-2xl font-semibold"><?= e($record['course']) ?></h2><p class="mt-2 text-xs text-white/35"><?= e($record['instructor']) ?> · <?= e(format_date_es($record['finishedAt'])) ?></p><div class="mt-5 flex items-center justify-between border-t border-white/10 pt-4"><span class="text-xs text-aula-green">Calificación <?= e($record['grade']) ?>/100</span><button type="button" class="text-xs font-bold text-aula-orange-soft">Ver certificado →</button></div></article>
          <?php endforeach; ?>
        </div>
      </section>

      <section data-workspace-panel="cuenta" data-title="Mi cuenta" data-eyebrow="Preferencias y perfil" hidden>
        <div class="grid gap-6 xl:grid-cols-[.75fr_1.25fr]">
          <aside class="border border-white/10 bg-white/[0.02] p-6"><span class="grid h-20 w-20 place-items-center rounded-full bg-aula-green-dark text-xl font-extrabold text-aula-green"><?= e(user_initials()) ?></span><h2 class="mt-5 font-editorial text-3xl font-semibold"><?= e(user_name()) ?></h2><p class="mt-2 text-sm text-white/35"><?= e(user_email()) ?></p><p class="mt-6 border-t border-white/10 pt-5 text-xs leading-6 text-white/35">El perfil se guardará en MySQL cuando conectemos usuarios. El workspace ya está preparado para consumir esos datos desde PHP.</p></aside>
          <form class="border border-white/10 p-6" onsubmit="return false;"><h2 class="font-editorial text-2xl font-semibold">Datos de perfil</h2><div class="mt-6 grid gap-5 sm:grid-cols-2"><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Nombre</span><input value="<?= e(user_name()) ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Correo</span><input value="<?= e(user_email()) ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"></label><label class="grid gap-2 sm:col-span-2"><span class="text-xs font-bold text-white/55">Objetivo de aprendizaje</span><textarea class="min-h-28 border border-white/10 bg-aula-bg p-3 text-sm text-white outline-none focus:border-aula-orange">Aprender con constancia sin saturarme.</textarea></label></div><button type="button" class="mt-6 min-h-10 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg">Guardar cambios</button></form>
        </div>
      </section>
    </main>
  </section>

  <aside class="workspace-context student-progress-panel hidden overflow-hidden bg-[#151116] p-5 lg:block">
    <div class="flex items-center justify-between"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/30">Tu progreso</p><span class="text-aula-orange-soft">●</span></div>
    <div class="student-progress-profile mt-6 flex items-center gap-4"><span class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark text-lg font-extrabold text-aula-green"><?= e(user_initials()) ?></span><div><strong class="block text-sm text-white/85"><?= e(user_name()) ?></strong><span class="mt-1 block text-xs text-white/30">Estudiante · AulaGo</span></div></div>

    <div class="student-progress-stats mt-8 grid grid-cols-3 border-y border-white/10 py-5 text-center"><div><strong class="block text-xl text-aula-orange-soft"><?= e($averageProgress) ?>%</strong><span class="mt-1 block text-[9px] uppercase tracking-[0.1em] text-white/30">promedio</span></div><div class="border-x border-white/10"><strong class="block text-xl text-aula-green"><?= e(count($enrolled)) ?></strong><span class="mt-1 block text-[9px] uppercase tracking-[0.1em] text-white/30">en curso</span></div><div><strong class="block text-xl text-[#9fcbd5]"><?= e($completedCount) ?></strong><span class="mt-1 block text-[9px] uppercase tracking-[0.1em] text-white/30">completados</span></div></div>

    <section class="student-progress-activity mt-8"><div class="flex items-end justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30">Actividad</p><strong class="mt-1 block text-2xl">7.4 h</strong></div><span class="text-[10px] text-aula-green">+12% esta semana</span></div><div class="student-progress-chart mt-5 flex h-28 items-end gap-2 border-b border-white/10 pb-2"><?php foreach ($weeklyActivity as $index => $height): ?><div class="flex flex-1 flex-col items-center gap-2"><span class="w-full bg-aula-green/60" style="height: <?= e($height) ?>%"></span><small class="text-[9px] text-white/25"><?= e($weekLabels[$index]) ?></small></div><?php endforeach; ?></div></section>

    <?php $current = $currentCourse; ?>
    <?php if ($current): ?><section class="student-current-course mt-8 border border-white/10 bg-aula-bg/35 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">Curso actual</p><h2 class="mt-3 font-editorial text-xl font-semibold leading-tight"><?= e($current['title']) ?></h2><p class="mt-2 text-xs leading-5 text-white/35"><?= e($current['nextLesson']) ?></p><div class="student-current-progress mt-4 h-1.5 overflow-hidden rounded-full bg-white/[0.06]"><span class="block h-full rounded-full bg-aula-orange" style="width: <?= e($current['progress']) ?>%"></span></div><div class="mt-3 flex justify-between text-[10px]"><span class="text-white/30">Progreso</span><strong class="text-aula-orange-soft"><?= e($current['progress']) ?>%</strong></div></section><?php endif; ?>

    <section class="student-next-achievement mt-8 border-t border-white/10 pt-6"><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30">Próximo logro</p><p class="mt-3 text-sm leading-6 text-white/55">Completa 2 lecciones más de <strong class="text-white/80">Neuro-Hábitos</strong> para llegar al 75%.</p></section>
  </aside>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
