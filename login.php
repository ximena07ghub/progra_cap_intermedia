<?php
require_once __DIR__ . '/config/app.php';

if (is_authenticated()) {
    header('Location: ' . role_home_url());
    exit;
}

$pageTitle = 'Iniciar sesión | AulaGo';
$pageDescription = 'Accede a tu espacio personal de AulaGo.';
$pageScripts = ['auth.js'];
$redirect = trim((string) ($_GET['redirect'] ?? ''));
$requestedRole = trim((string) ($_GET['role'] ?? ''));
$error = trim((string) ($_GET['error'] ?? ''));
$registered = isset($_GET['registered']);

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<main class="grid min-h-screen bg-aula-bg text-white lg:grid-cols-[1.1fr_0.9fr]">
  <section class="relative hidden min-h-screen overflow-hidden border-r border-white/10 lg:flex lg:flex-col">
    <img src="<?= e(asset('images/login-bg.png')) ?>" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-b from-aula-bg/25 via-aula-bg/65 to-aula-bg"></div>
    <div class="absolute -bottom-40 left-1/4 h-[520px] w-[520px] rounded-full bg-aula-orange/[0.12] blur-3xl"></div>

    <div class="relative z-10 flex items-center justify-between px-10 py-9 xl:px-14">
      <a href="<?= e(url()) ?>" class="inline-flex" aria-label="Volver a AulaGo"><img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-10 w-auto"></a>
      <a href="<?= e(url()) ?>" class="text-xs font-bold text-white/50 transition hover:text-aula-orange-soft">← Volver al inicio</a>
    </div>

    <div class="relative z-10 mt-auto max-w-3xl px-10 pb-16 xl:px-14 xl:pb-20">
      <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-aula-green">Continúa aprendiendo</p>
      <h1 class="mt-5 font-editorial text-6xl font-semibold leading-[0.95] text-white xl:text-7xl">
        Regresa a lo que <span class="block text-aula-orange-soft">despertó tu curiosidad.</span>
      </h1>
      <p class="mt-7 max-w-xl text-sm leading-7 text-white/55">Retoma tus cursos, continúa tu progreso y descubre qué puedes aprender después sin perder de vista tu ritmo y tu bienestar.</p>
    </div>
  </section>

  <section class="grid min-h-screen place-items-center px-6 py-12 sm:px-10 lg:px-12" aria-labelledby="login-title">
    <div class="w-full max-w-md">
      <div class="mb-10 flex items-center justify-between lg:hidden">
        <a href="<?= e(url()) ?>" class="inline-flex"><img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto"></a>
        <a href="<?= e(url()) ?>" class="text-xs font-bold text-white/45 transition hover:text-aula-orange-soft">← Volver</a>
      </div>

      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#9fcbd5]">Acceso</p>
        <h2 id="login-title" class="mt-4 font-editorial text-5xl font-semibold leading-none text-white">Iniciar sesión</h2>
        <p class="mt-3 text-sm text-white/45">Ingresa tus datos para continuar.</p>
      </div>

      <?php if ($registered): ?>
        <p class="mt-6 border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-xs leading-5 text-aula-green">Tu registro de prueba fue correcto. Ya puedes iniciar sesión.</p>
      <?php endif; ?>
      <?php if ($error !== ''): ?>
        <p class="mt-6 border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-xs leading-5 text-red-200" role="alert"><?= e($error) ?></p>
      <?php endif; ?>

      <form class="mt-9 grid gap-5" action="<?= e(url('actions/login.php')) ?>" method="post" novalidate data-auth-form="login">
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <input type="hidden" name="role" value="<?= e($requestedRole) ?>">

        <label class="grid gap-2">
          <span class="text-xs font-bold text-white/70">Correo electrónico</span>
          <input type="email" name="email" placeholder="nombre@correo.com" autocomplete="email" required class="min-h-12 rounded-xl border border-white/15 bg-white/[0.04] px-4 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-aula-orange" data-required-email>
          <small class="min-h-4 text-[11px] text-brand-coral" data-error-for="email"></small>
        </label>

        <label class="grid gap-2">
          <span class="text-xs font-bold text-white/70">Contraseña</span>
          <div class="relative">
            <input type="password" name="password" placeholder="Tu contraseña" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-aula-orange" data-password-input>
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-xs font-bold text-aula-orange-soft transition hover:bg-white/5" data-password-toggle>Ver</button>
          </div>
          <small class="min-h-4 text-[11px] text-brand-coral" data-error-for="password"></small>
        </label>

        <div class="flex flex-col gap-3 text-xs sm:flex-row sm:items-center sm:justify-between">
          <label class="inline-flex items-center gap-2 text-white/60"><input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-transparent accent-aula-orange"><span>Recordarme</span></label>
          <a href="<?= e(url('recuperar-contrasena.php')) ?>" class="font-bold text-[#9fcbd5] transition hover:text-aula-orange-soft">¿Olvidaste tu contraseña?</a>
        </div>

        <p class="hidden rounded-xl border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-xs leading-5 text-red-200" data-form-status role="alert"></p>

        <button type="submit" class="mt-2 inline-flex min-h-12 items-center justify-center gap-3 rounded-full bg-aula-orange px-6 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-aula-orange/10">Iniciar sesión <span>→</span></button>
      </form>

      <div class="mt-8 flex flex-wrap items-center gap-2 border-t border-white/10 pt-6 text-sm text-white/45">
        <span>¿Todavía no tienes una cuenta?</span>
        <a href="<?= e(url('registro.php')) ?>" class="font-bold text-white transition hover:text-[#9fcbd5]">Crear cuenta</a>
      </div>
    </div>
  </section>
</main>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
