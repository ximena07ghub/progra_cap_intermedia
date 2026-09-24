<?php
require_once dirname(__DIR__) . '/config/app.php';
require_role('instructor');

$pageTitle = 'Workspace del instructor | AulaGo';
$pageDescription = 'Espacio unificado para cursos, ventas y mensajes del instructor.';
$pageScripts = ['workspace.js'];
$bodyClass = 'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased lg:overflow-hidden';
$portal = portal_data();
$instructorCourses = $portal['instructorCourses'] ?? [];
$sales = $portal['salesTransactions'] ?? [];
$conversations = $portal['instructorConversations'] ?? [];
$allCategories = categories();
$totalRevenue = array_sum(array_column($sales, 'amount'));
$totalStudents = array_sum(array_column($instructorCourses, 'students'));
$publishedRatings = array_values(array_filter(array_column($instructorCourses, 'rating'), fn($rating): bool => (float) $rating > 0));
$avgRating = $publishedRatings ? array_sum($publishedRatings) / count($publishedRatings) : 0;
$workspaceRoleLabel = 'Workspace del instructor';
$workspaceItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => '⌂'],
    ['id' => 'cursos', 'label' => 'Mis cursos', 'icon' => '▦'],
    ['id' => 'crear', 'label' => 'Crear curso', 'icon' => '+'],
    ['id' => 'mensajes', 'label' => 'Mensajes', 'icon' => '✉'],
    ['id' => 'ventas', 'label' => 'Ventas', 'icon' => '$'],
    ['id' => 'cuenta', 'label' => 'Mi cuenta', 'icon' => '○'],
];

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream lg:grid lg:h-screen lg:grid-cols-[230px_minmax(0,1fr)_320px] lg:overflow-hidden" data-workspace>
  <?php require PROJECT_ROOT . '/includes/workspace/sidebar.php'; ?>

  <section class="flex min-w-0 flex-col border-white/10 lg:border-r">
    <header class="border-b border-white/10 bg-aula-bg/95 px-5 py-5 backdrop-blur md:px-7 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green" data-workspace-eyebrow>Actividad del instructor</p><h1 class="mt-2 font-editorial text-3xl font-semibold sm:text-4xl" data-workspace-title>Dashboard</h1></div>
        <a href="#crear" data-workspace-link="crear" class="inline-flex min-h-10 items-center justify-center bg-aula-orange px-4 text-xs font-extrabold text-aula-bg">+ Crear curso</a>
      </div>
    </header>

    <main class="workspace-main min-h-0 flex-1 overflow-y-auto px-5 py-6 md:px-7 lg:px-8 lg:py-7">
      <section data-workspace-panel="dashboard" data-title="Dashboard" data-eyebrow="Actividad del instructor">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <article class="border border-white/10 bg-white/[0.02] p-4"><span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Cursos</span><strong class="mt-2 block text-2xl"><?= e(count($instructorCourses)) ?></strong><small class="mt-1 block text-[10px] text-white/30">Publicados y borradores</small></article>
          <article class="border border-white/10 bg-white/[0.02] p-4"><span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Estudiantes</span><strong class="mt-2 block text-2xl text-aula-green"><?= e($totalStudents) ?></strong><small class="mt-1 block text-[10px] text-white/30">Inscripciones acumuladas</small></article>
          <article class="border border-white/10 bg-white/[0.02] p-4"><span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Ingresos</span><strong class="mt-2 block text-2xl text-aula-orange-soft"><?= e(format_money($totalRevenue)) ?></strong><small class="mt-1 block text-[10px] text-white/30">Ventas registradas</small></article>
          <article class="border border-white/10 bg-white/[0.02] p-4"><span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Mensajes</span><strong class="mt-2 block text-2xl text-[#9fcbd5]"><?= e(count($conversations)) ?></strong><small class="mt-1 block text-[10px] text-white/30">Conversaciones abiertas</small></article>
        </div>

        <div class="mt-8 grid gap-7 xl:grid-cols-[1.25fr_.75fr]">
          <section>
            <div class="flex items-end justify-between border-b border-white/10 pb-4"><div><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Tus cursos</p><h2 class="mt-2 font-editorial text-3xl font-semibold">Estado de publicación.</h2></div><a href="#cursos" data-workspace-link="cursos" class="text-xs font-bold text-white/40 hover:text-aula-orange">Ver todos →</a></div>
            <div class="divide-y divide-white/10">
              <?php foreach ($instructorCourses as $course): ?>
                <article class="grid gap-4 py-5 sm:grid-cols-[1fr_auto] sm:items-center"><div><div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full <?= $course['status'] === 'Publicado' ? 'bg-aula-green' : 'bg-aula-yellow' ?>"></span><span class="text-[10px] font-bold uppercase tracking-[0.1em] text-white/35"><?= e($course['status']) ?> · <?= e($course['category']) ?></span></div><h3 class="mt-2 font-editorial text-xl font-semibold"><?= e($course['title']) ?></h3><p class="mt-2 text-xs text-white/35"><?= e($course['students']) ?> estudiantes · <?= e($course['rating'] ?: 'Sin') ?> calificación · actualizado <?= e(format_date_es($course['updatedAt'])) ?></p></div><div class="sm:text-right"><strong class="block text-sm text-aula-orange-soft"><?= e(format_money($course['price'])) ?></strong><button type="button" class="mt-2 text-xs font-bold text-white/45 hover:text-white">Editar →</button></div></article>
              <?php endforeach; ?>
            </div>
          </section>

          <aside class="border border-white/10 bg-white/[0.02] p-5"><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-orange-soft">Ventas recientes</p><div class="mt-4 divide-y divide-white/10"><?php foreach (array_slice($sales, 0, 4) as $sale): ?><div class="py-4"><div class="flex items-start justify-between gap-4"><div><strong class="block text-sm"><?= e($sale['course']) ?></strong><span class="mt-1 block text-xs text-white/35"><?= e($sale['student']) ?> · <?= e(format_date_es($sale['date'])) ?></span></div><strong class="text-sm text-aula-green"><?= e(format_money($sale['amount'])) ?></strong></div></div><?php endforeach; ?></div><a href="#ventas" data-workspace-link="ventas" class="mt-5 inline-block text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">Abrir ventas →</a></aside>
        </div>
      </section>

      <section data-workspace-panel="cursos" data-title="Mis cursos" data-eyebrow="Contenido y publicación" hidden>
        <div class="flex flex-col gap-4 border-b border-white/10 pb-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="max-w-2xl text-sm leading-7 text-white/45">Gestiona el estado, contenido, precio y desempeño de tus cursos sin salir del workspace.</p></div><a href="#crear" data-workspace-link="crear" class="inline-flex min-h-10 items-center bg-aula-orange px-4 text-xs font-extrabold text-aula-bg">Nuevo curso</a></div>
        <div class="border-t border-white/10">
          <?php foreach ($instructorCourses as $course): ?>
            <article class="grid gap-5 border-b border-white/10 py-6 md:grid-cols-[1fr_auto] md:items-center"><div><div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.1em]"><span class="text-aula-green"><?= e($course['category']) ?></span><span class="text-white/25">·</span><span class="<?= $course['status'] === 'Publicado' ? 'text-aula-green' : 'text-aula-yellow' ?>"><?= e($course['status']) ?></span></div><h2 class="mt-2 font-editorial text-2xl font-semibold"><?= e($course['title']) ?></h2><div class="mt-3 flex flex-wrap gap-5 text-xs text-white/35"><span><?= e($course['students']) ?> estudiantes</span><span>★ <?= e($course['rating'] ?: '—') ?></span><span><?= e(format_money($course['price'])) ?></span><span>Actualizado <?= e(format_date_es($course['updatedAt'])) ?></span></div></div><div class="flex gap-2"><button type="button" class="min-h-9 border border-white/10 px-3 text-xs font-bold text-white/50 hover:text-white">Contenido</button><button type="button" class="min-h-9 border border-aula-orange/30 bg-aula-orange/10 px-3 text-xs font-bold text-aula-orange-soft">Editar</button></div></article>
          <?php endforeach; ?>
        </div>
      </section>

      <section data-workspace-panel="crear" data-title="Crear curso" data-eyebrow="Editor integrado" hidden>
        <form class="grid gap-7" onsubmit="return false;">
          <section class="border border-white/10 p-6"><div class="flex items-center gap-4 border-b border-white/10 pb-4"><span class="text-xs font-bold text-aula-orange-soft">01</span><h2 class="font-editorial text-2xl font-semibold">Información básica</h2></div><div class="mt-5 grid gap-5 md:grid-cols-2"><label class="grid gap-2 md:col-span-2"><span class="text-xs font-bold text-white/55">Título del curso</span><input class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange" placeholder="Ej. Fundamentos de fotografía consciente"></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Categoría</span><select class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"><?php foreach ($allCategories as $category): ?><option><?= e($category['name']) ?></option><?php endforeach; ?></select></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Nivel</span><select class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"><option>Inicial</option><option>Intermedio</option><option>Avanzado</option></select></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Precio</span><input type="number" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange" placeholder="680"></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Duración estimada</span><input class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange" placeholder="4 h 20 min"></label><label class="grid gap-2 md:col-span-2"><span class="text-xs font-bold text-white/55">Descripción</span><textarea class="min-h-32 border border-white/10 bg-aula-bg p-3 text-sm text-white outline-none focus:border-aula-orange" placeholder="Describe la experiencia y objetivos del curso."></textarea></label></div></section>
          <section class="border border-white/10 p-6"><div class="flex items-center gap-4 border-b border-white/10 pb-4"><span class="text-xs font-bold text-[#9fcbd5]">02</span><h2 class="font-editorial text-2xl font-semibold">Estructura de contenido</h2></div><div class="mt-5 divide-y divide-white/10 border-y border-white/10"><?php foreach (['Introducción', 'Conceptos esenciales', 'Práctica guiada'] as $index => $lesson): ?><div class="grid grid-cols-[42px_1fr_auto] items-center gap-4 py-4"><span class="text-xs text-aula-orange"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span><input value="<?= e($lesson) ?>" class="min-h-10 bg-transparent text-sm text-white/75 outline-none"><button type="button" class="text-xs text-white/30">Editar</button></div><?php endforeach; ?></div><button type="button" class="mt-4 text-xs font-bold text-aula-green">+ Agregar lección</button></section>
          <div class="flex justify-end gap-3"><button type="button" class="min-h-10 border border-white/10 px-4 text-xs font-bold text-white/50">Guardar borrador</button><button type="button" class="min-h-10 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg">Publicar curso</button></div>
        </form>
      </section>

      <section data-workspace-panel="mensajes" data-title="Mensajes" data-eyebrow="Comunicación con estudiantes" hidden data-messages-panel>
        <div class="workspace-message-shell grid min-h-0 border border-white/10 lg:grid-cols-[270px_minmax(0,1fr)]">
          <aside class="conversation-list min-h-0 border-b border-white/10 bg-white/[0.015] lg:border-b-0 lg:border-r">
            <div class="border-b border-white/10 p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-white/35">Estudiantes</p><span class="mt-1 block text-[10px] text-white/25">Seguimiento de tus cursos</span></div>
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
          <?php $active = $conversations[0] ?? null; ?>
          <section class="flex min-h-0 min-w-0 flex-col" data-message-thread>
            <header class="flex items-center gap-3 border-b border-white/10 p-4 sm:p-5"><span class="grid h-10 w-10 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-aula-green" data-thread-avatar><?= e($active['avatar'] ?? 'AG') ?></span><div class="min-w-0"><strong class="block truncate text-sm" data-thread-name><?= e($active['participant'] ?? 'Conversación') ?></strong><span class="mt-1 block truncate text-xs text-white/35" data-thread-course><?= e($active['course'] ?? '') ?></span></div></header>
            <div class="message-stream min-h-0 flex-1 space-y-4 overflow-y-auto p-4 sm:p-5" data-message-stream><?php foreach (($active['messages'] ?? []) as $message): ?><article class="max-w-[82%] p-4 text-sm leading-6 <?= $message['from'] === 'me' ? 'ml-auto bg-aula-green-dark text-white' : 'bg-white/[0.05] text-white/75' ?>"><p><?= e($message['text']) ?></p><span class="mt-2 block text-[10px] uppercase tracking-[0.08em] text-white/35"><?= e(format_date_es($message['date'])) ?> · <?= e($message['time']) ?></span></article><?php endforeach; ?></div>
            <form class="grid gap-3 border-t border-white/10 p-4 sm:grid-cols-[1fr_auto] sm:p-5" data-message-form><input class="min-h-11 min-w-0 border border-white/10 bg-aula-bg px-4 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange" placeholder="Escribe un mensaje"><button type="submit" class="min-h-11 bg-aula-orange px-6 text-xs font-extrabold text-aula-bg">Enviar</button></form>
          </section>
        </div>
        <script type="application/json" data-conversation-data><?= json_encode($conversations, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
      </section>

      <section data-workspace-panel="ventas" data-title="Ventas" data-eyebrow="Ingresos y transacciones" hidden>
        <div class="grid gap-3 sm:grid-cols-3"><article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/30">Ingresos totales</span><strong class="mt-2 block text-2xl text-aula-orange-soft"><?= e(format_money($totalRevenue)) ?></strong></article><article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/30">Transacciones</span><strong class="mt-2 block text-2xl"><?= e(count($sales)) ?></strong></article><article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/30">Ticket promedio</span><strong class="mt-2 block text-2xl text-aula-green"><?= e(format_money(count($sales) ? $totalRevenue / count($sales) : 0)) ?></strong></article></div>
        <div class="mt-7 overflow-x-auto border border-white/10"><table class="min-w-full text-left text-sm"><thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35"><tr><th class="px-5 py-4">Venta</th><th class="px-5 py-4">Curso</th><th class="px-5 py-4">Estudiante</th><th class="px-5 py-4">Fecha</th><th class="px-5 py-4">Monto</th></tr></thead><tbody class="divide-y divide-white/10"><?php foreach ($sales as $sale): ?><tr><td class="px-5 py-4 text-white/35"><?= e($sale['id']) ?></td><td class="px-5 py-4 font-bold text-white/75"><?= e($sale['course']) ?></td><td class="px-5 py-4 text-white/45"><?= e($sale['student']) ?></td><td class="px-5 py-4 text-white/45"><?= e(format_date_es($sale['date'])) ?></td><td class="px-5 py-4 font-bold text-aula-green"><?= e(format_money($sale['amount'])) ?></td></tr><?php endforeach; ?></tbody></table></div>
      </section>

      <section data-workspace-panel="cuenta" data-title="Mi cuenta" data-eyebrow="Perfil del instructor" hidden>
        <div class="grid gap-6 xl:grid-cols-[.75fr_1.25fr]"><aside class="border border-white/10 bg-white/[0.02] p-6"><span class="grid h-20 w-20 place-items-center rounded-full bg-aula-green-dark text-xl font-extrabold text-aula-green"><?= e(user_initials()) ?></span><h2 class="mt-5 font-editorial text-3xl font-semibold"><?= e(user_name()) ?></h2><p class="mt-2 text-sm text-white/35"><?= e(user_email()) ?></p><div class="mt-6 border-t border-white/10 pt-5 text-xs text-white/35"><p><?= e(count($instructorCourses)) ?> cursos · <?= e($totalStudents) ?> estudiantes</p></div></aside><form class="border border-white/10 p-6" onsubmit="return false;"><h2 class="font-editorial text-2xl font-semibold">Perfil profesional</h2><div class="mt-6 grid gap-5"><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Nombre público</span><input value="<?= e(user_name()) ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"></label><label class="grid gap-2"><span class="text-xs font-bold text-white/55">Biografía</span><textarea class="min-h-32 border border-white/10 bg-aula-bg p-3 text-sm text-white outline-none focus:border-aula-orange">Comparte tu experiencia, metodología y especialidad.</textarea></label></div><button type="button" class="mt-6 min-h-10 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg">Guardar cambios</button></form></div>
      </section>
    </main>
  </section>

  <aside class="workspace-context hidden overflow-hidden bg-[#151116] p-5 lg:block">
    <div class="flex items-center justify-between"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/30">Resumen profesional</p><span class="text-aula-green">●</span></div>
    <div class="mt-6 flex items-center gap-4"><span class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark text-lg font-extrabold text-aula-green"><?= e(user_initials()) ?></span><div><strong class="block text-sm text-white/85"><?= e(user_name()) ?></strong><span class="mt-1 block text-xs text-white/30">Instructor · AulaGo</span></div></div>
    <div class="mt-8 grid grid-cols-2 gap-px bg-white/10"><div class="bg-[#151116] p-4"><span class="text-[9px] uppercase tracking-[0.1em] text-white/30">Estudiantes</span><strong class="mt-2 block text-2xl text-aula-green"><?= e($totalStudents) ?></strong></div><div class="bg-[#151116] p-4"><span class="text-[9px] uppercase tracking-[0.1em] text-white/30">Rating</span><strong class="mt-2 block text-2xl text-aula-yellow">★ <?= e(number_format($avgRating, 1)) ?></strong></div><div class="bg-[#151116] p-4"><span class="text-[9px] uppercase tracking-[0.1em] text-white/30">Ingresos</span><strong class="mt-2 block text-lg text-aula-orange-soft"><?= e(format_money($totalRevenue)) ?></strong></div><div class="bg-[#151116] p-4"><span class="text-[9px] uppercase tracking-[0.1em] text-white/30">Mensajes</span><strong class="mt-2 block text-2xl text-[#9fcbd5]"><?= e(count($conversations)) ?></strong></div></div>
    <?php $latestSale = $sales[0] ?? null; ?>
    <?php if ($latestSale): ?><section class="mt-8 border border-white/10 bg-aula-bg/35 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">Última venta</p><h2 class="mt-3 font-editorial text-xl font-semibold"><?= e($latestSale['course']) ?></h2><p class="mt-2 text-xs text-white/35"><?= e($latestSale['student']) ?> · <?= e(format_date_es($latestSale['date'])) ?></p><strong class="mt-4 block text-lg text-aula-green"><?= e(format_money($latestSale['amount'])) ?></strong></section><?php endif; ?>
    <section class="mt-8 border-t border-white/10 pt-6"><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30">Pendiente</p><p class="mt-3 text-sm leading-6 text-white/55">Tienes <strong class="text-aula-yellow">1 curso en borrador</strong>. Completa su contenido antes de publicarlo.</p></section>
  </aside>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
