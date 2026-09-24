<?php
require_once __DIR__ . '/config/app.php';
$pageTitle = 'Recuperar contraseña | AulaGo';
$sent = isset($_GET['sent']);
$error = trim((string) ($_GET['error'] ?? ''));
require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen bg-aula-bg text-aula-cream">
  <main class="mx-auto grid min-h-screen max-w-aula place-items-center px-6 py-12 lg:px-8">
    <section class="w-full max-w-xl border border-white/10 bg-aula-surface/70 p-7 shadow-aula sm:p-10">
      <a href="<?= e(url('login.php')) ?>" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Volver al login</a>
      <p class="mt-10 text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Recuperación de acceso</p>
      <h1 class="mt-3 font-editorial text-5xl font-semibold leading-none">Recupera tu contraseña.</h1>
      <p class="mt-5 text-sm leading-7 text-white/45">Escribe el correo de tu cuenta. Por ahora simulamos la solicitud; después esta acción se conectará al correo y a MySQL.</p>
      <?php if ($sent): ?><p class="mt-6 border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">Solicitud registrada. El flujo visual ya está listo para conectar el envío real.</p><?php endif; ?>
      <?php if ($error !== ''): ?><p class="mt-6 border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-sm text-red-200"><?= e($error) ?></p><?php endif; ?>
      <form class="mt-8 grid gap-5" action="<?= e(url('actions/forgot-password.php')) ?>" method="post">
        <label class="grid gap-2"><span class="text-xs font-bold uppercase tracking-[0.08em] text-white/60">Correo electrónico</span><input type="email" name="email" autocomplete="email" required class="min-h-12 border border-white/15 bg-aula-bg px-4 text-sm text-white outline-none focus:border-aula-orange" placeholder="nombre@correo.com"></label>
        <button type="submit" class="min-h-12 bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Enviar enlace</button>
      </form>
    </section>
  </main>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
