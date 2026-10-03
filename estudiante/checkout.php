<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_role('estudiante');

$slug = trim((string) ($_GET['slug'] ?? ''));
$course = course_by_slug($slug);

if (!$course) {
    http_response_code(404);
    exit('Curso no encontrado.');
}

if (student_owns_course($slug)) {
    header('Location: ' . url('estudiante/?course=' . rawurlencode($slug) . '#cursos'));
    exit;
}

$price = course_price_info($course);
$paymentState = trim((string) ($_GET['payment'] ?? ''));
$paypalConfigured = is_file(PROJECT_ROOT . '/config/paypal.local.php');
$paypalError = '';
if ($paymentState === 'error') {
    $paypalError = trim((string) ($_SESSION['paypal_last_error'] ?? 'No fue posible completar la conexión con PayPal. Intenta nuevamente.'));
}

$pageTitle = 'Comprar ' . $course['title'] . ' | AulaGo';
$pageDescription = 'Finaliza tu inscripción al curso con PayPal.';
require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main class="border-b border-white/10">
    <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-14">
      <nav class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-white/30" aria-label="Ruta de compra">
        <a href="<?= e(url('cursos.php')) ?>" class="transition hover:text-aula-orange-soft">Cursos</a>
        <span>→</span>
        <a href="<?= e(url('curso.php?slug=' . rawurlencode($slug))) ?>" class="transition hover:text-aula-orange-soft"><?= e($course['title']) ?></a>
        <span>→</span>
        <span class="text-aula-green">Compra</span>
      </nav>

      <div class="checkout-layout mt-8">
        <section>
          <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-aula-green">Finaliza tu inscripción</p>
          <h1 class="mt-4 max-w-3xl font-editorial text-4xl font-semibold leading-[0.98] sm:text-5xl lg:text-6xl">Compra el curso sin salir de la experiencia AulaGo.</h1>
          <p class="mt-5 max-w-2xl text-sm leading-7 text-white/45">Revisa el curso y continúa a PayPal. Los datos de pago se capturan en PayPal; AulaGo no guarda números de tarjeta.</p>

          <?php if ($paymentState === 'cancelled'): ?>
            <div class="mt-7 border border-aula-yellow/30 bg-aula-yellow/10 px-5 py-4 text-sm text-aula-yellow">Cancelaste el pago antes de completarlo. No se realizó ningún cargo.</div>
          <?php elseif ($paymentState === 'error'): ?>
            <div class="mt-7 border border-brand-coral/35 bg-brand-coral/10 px-5 py-4 text-sm leading-6 text-red-200">
              No pudimos confirmar el pago con PayPal. <?= e($paypalError) ?>
            </div>
          <?php endif; ?>

          <article class="checkout-course-summary mt-8 border-y border-white/10 py-6">
            <img src="<?= e(asset('images/' . $course['image'])) ?>" alt="<?= e($course['title']) ?>" class="aspect-[4/3] h-full w-full object-cover opacity-80">
            <div>
              <div class="flex flex-wrap gap-2 text-[10px] font-bold uppercase tracking-[0.1em]"><span class="text-aula-green"><?= e($course['category']) ?></span><span class="text-white/25">·</span><span class="text-white/40"><?= e($course['level']) ?></span></div>
              <h2 class="mt-2 font-editorial text-3xl font-semibold"><?= e($course['title']) ?></h2>
              <p class="mt-3 max-w-2xl text-sm leading-6 text-white/45"><?= e($course['description']) ?></p>
              <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-white/35"><span><?= e($course['instructor']) ?></span><span><?= e($course['duration']) ?></span><span>★ <?= e($course['rating']) ?></span></div>
            </div>
          </article>

          <section class="mt-8 grid gap-4 sm:grid-cols-3" aria-label="Pasos de compra">
            <div class="border-t border-white/10 pt-4"><span class="text-xs font-bold text-aula-orange-soft">01</span><strong class="mt-2 block text-sm">Revisa tu curso</strong><p class="mt-2 text-xs leading-5 text-white/35">Verifica título, instructor y precio.</p></div>
            <div class="border-t border-white/10 pt-4"><span class="text-xs font-bold text-[#9fcbd5]">02</span><strong class="mt-2 block text-sm">Autoriza en PayPal</strong><p class="mt-2 text-xs leading-5 text-white/35">PayPal procesa la forma de pago disponible.</p></div>
            <div class="border-t border-white/10 pt-4"><span class="text-xs font-bold text-aula-green">03</span><strong class="mt-2 block text-sm">Regresa a tus cursos</strong><p class="mt-2 text-xs leading-5 text-white/35">Al confirmarse, el curso aparece en tu workspace.</p></div>
          </section>
        </section>

        <aside class="border border-white/10 bg-white/[0.02] p-6 lg:p-7">
          <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#9fcbd5]">Resumen de compra</p>
          <div class="mt-5 border-b border-white/10 pb-5">
            <h2 class="font-editorial text-2xl font-semibold"><?= e($course['title']) ?></h2>
            <p class="mt-2 text-xs text-white/35"><?= e($course['category']) ?> · <?= e($course['level']) ?></p>
          </div>

          <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-5"><dt class="text-white/35">Curso</dt><dd class="font-semibold text-white/75"><?= e($price['label']) ?></dd></div>
            <div class="flex items-center justify-between gap-5"><dt class="text-white/35">Método</dt><dd class="font-semibold text-white/75"><?= $price['free'] ? 'Inscripción gratuita' : 'PayPal' ?></dd></div>
            <div class="flex items-center justify-between gap-5 border-t border-white/10 pt-4"><dt class="font-bold text-white/65">Total</dt><dd class="text-xl font-extrabold text-aula-orange-soft"><?= e($price['label']) ?></dd></div>
          </dl>

          <?php if ($price['free']): ?>
            <form action="<?= e(url('actions/enroll-free.php')) ?>" method="post" class="mt-7">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="slug" value="<?= e($slug) ?>">
              <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center bg-aula-orange px-5 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5">Inscribirme gratis →</button>
            </form>
          <?php else: ?>
            <form action="<?= e(url('actions/paypal-create-order.php')) ?>" method="post" class="mt-7">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="slug" value="<?= e($slug) ?>">
              <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center bg-aula-orange px-5 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5" <?= $paypalConfigured ? '' : 'disabled' ?>>Continuar con PayPal →</button>
            </form>
            <?php if (!$paypalConfigured): ?>
              <p class="mt-3 border-l border-aula-yellow/50 pl-3 text-xs leading-5 text-aula-yellow/80">Falta `config/paypal.local.php`. La interfaz ya está conectada, pero PayPal permanecerá deshabilitado hasta configurar Sandbox.</p>
            <?php else: ?>
              <p class="mt-3 text-xs leading-5 text-white/30">Serás enviado a PayPal Sandbox y volverás automáticamente a AulaGo cuando se confirme la orden.</p>
            <?php endif; ?>
          <?php endif; ?>

          <a href="<?= e(url('curso.php?slug=' . rawurlencode($slug))) ?>" class="mt-5 inline-flex w-full items-center justify-center text-xs font-bold text-white/40 transition hover:text-white">← Volver al curso</a>

          <div class="mt-7 border-t border-white/10 pt-5 text-[11px] leading-5 text-white/30">
            <strong class="block text-white/50">Pago externo protegido</strong>
            <span>AulaGo crea y confirma la orden desde PHP. Las credenciales privadas permanecen fuera del repositorio.</span>
          </div>
        </aside>
      </div>
    </section>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
