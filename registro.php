<?php
require_once __DIR__ . '/config/app.php';

if (is_authenticated()) {
    header('Location: ' . role_home_url());
    exit;
}

$pageTitle = 'Crear cuenta | AulaGo';
$pageDescription = 'Crea tu cuenta de estudiante o instructor en AulaGo.';
$pageScripts = ['auth.js'];
$error = trim((string) ($_GET['error'] ?? ''));

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<main class="grid min-h-screen bg-aula-bg text-white xl:grid-cols-[1.18fr_0.82fr]">
  <section class="px-6 py-8 sm:px-10 lg:px-14 xl:px-16">
    <div class="mx-auto max-w-3xl">
      <div class="flex items-center justify-between">
        <a href="<?= e(url()) ?>" class="inline-flex"><img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-10 w-auto"></a>
        <a href="<?= e(url()) ?>" class="text-xs font-bold text-white/45 transition hover:text-aula-orange-soft">← Volver</a>
      </div>

      <div class="mt-12">
        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-aula-green">Únete a AulaGo</p>
        <h1 class="mt-4 font-editorial text-5xl font-semibold leading-none text-white sm:text-6xl">Crea tu cuenta.</h1>
        <p class="mt-4 max-w-xl text-sm leading-7 text-white/45">Elige cómo participarás dentro del portal y completa tus datos básicos.</p>
      </div>

      <?php if ($error !== ''): ?>
        <p class="mt-6 border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-xs leading-5 text-red-200" role="alert"><?= e($error) ?></p>
      <?php endif; ?>

      <form class="mt-10 grid gap-10" action="<?= e(url('actions/register.php')) ?>" method="post" enctype="multipart/form-data" novalidate data-auth-form="register">
        <section class="border-t border-white/10 pt-7">
          <div class="mb-6 flex items-center gap-4"><span class="text-xs font-bold text-aula-orange-soft">01</span><h2 class="text-lg font-extrabold text-white">Información personal</h2></div>
          <div class="grid gap-5 sm:grid-cols-2">
            <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Nombre completo</span><input type="text" name="nombre" placeholder="Tu nombre completo" autocomplete="name" required class="min-h-12 rounded-xl border border-white/15 bg-white/[0.04] px-4 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-brand-orange"><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="nombre"></small></label>
            <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Correo electrónico</span><input type="email" name="email" placeholder="nombre@correo.com" autocomplete="email" required class="min-h-12 rounded-xl border border-white/15 bg-white/[0.04] px-4 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-brand-orange"><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="email"></small></label>
            <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Fecha de nacimiento</span><input type="date" name="fecha_nacimiento" required class="min-h-12 rounded-xl border border-white/15 bg-white/[0.04] px-4 text-sm text-white outline-none transition focus:border-brand-orange"><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="fecha_nacimiento"></small></label>
            <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Género</span><select name="genero" required class="min-h-12 rounded-xl border border-white/15 bg-aula-surface px-4 text-sm text-white outline-none transition focus:border-brand-orange"><option value="">Selecciona</option><option value="mujer">Mujer</option><option value="hombre">Hombre</option><option value="otro">Otro</option><option value="prefiero_no_decir">Prefiero no decirlo</option></select><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="genero"></small></label>
          </div>
        </section>

        <section class="border-t border-white/10 pt-7">
          <div class="mb-6 flex items-center gap-4"><span class="text-xs font-bold text-[#9fcbd5]">02</span><h2 class="text-lg font-extrabold text-white">¿Cómo usarás AulaGo?</h2></div>
          <div class="grid gap-4 sm:grid-cols-2" data-role-selector>
            <label class="role-choice cursor-pointer rounded-2xl border border-brand-blue bg-brand-blue/10 p-5 transition" data-role-card="estudiante">
              <input type="radio" name="rol" value="estudiante" class="sr-only" checked>
              <span class="text-[10px] font-bold text-[#9fcbd5]">01</span><strong class="mt-5 block text-base text-white">Estudiante</strong><small class="mt-2 block text-xs leading-6 text-white/45">Quiero tomar cursos, avanzar por niveles y obtener certificados.</small>
            </label>
            <label class="role-choice cursor-pointer rounded-2xl border border-white/10 bg-white/[0.02] p-5 transition" data-role-card="instructor">
              <input type="radio" name="rol" value="instructor" class="sr-only">
              <span class="text-[10px] font-bold text-aula-orange-soft">02</span><strong class="mt-5 block text-base text-white">Instructor</strong><small class="mt-2 block text-xs leading-6 text-white/45">Quiero crear cursos, niveles y consultar mis ventas.</small>
            </label>
          </div>
        </section>

        <section class="border-t border-white/10 pt-7">
          <div class="mb-6 flex items-center gap-4"><span class="text-xs font-bold text-aula-green">03</span><h2 class="text-lg font-extrabold text-white">Imagen de perfil</h2></div>
          <label class="flex cursor-pointer flex-col gap-5 rounded-2xl border border-dashed border-white/20 bg-white/[0.02] p-5 transition hover:border-brand-green sm:flex-row sm:items-center">
            <span class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-full border border-white/15 bg-white/[0.04] text-2xl text-aula-green" data-avatar-preview>+</span>
            <span><strong class="block text-sm text-white">Selecciona una fotografía</strong><small class="mt-2 block text-xs leading-6 text-white/40">JPG o PNG. Esta vista previa seguirá siendo local hasta conectar el almacenamiento real.</small></span>
            <input type="file" name="avatar" accept="image/png,image/jpeg" class="sr-only" data-avatar-input>
          </label>
          <small class="mt-2 block min-h-4 text-[11px] text-brand-coral" data-error-for="avatar"></small>
        </section>

        <section class="border-t border-white/10 pt-7">
          <div class="mb-6 flex items-center gap-4"><span class="text-xs font-bold text-brand-magenta">04</span><h2 class="text-lg font-extrabold text-white">Seguridad</h2></div>
          <div class="grid gap-6 lg:grid-cols-[1fr_0.75fr]">
            <div class="grid gap-5">
              <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Contraseña</span><div class="relative"><input type="password" name="password" placeholder="Crea una contraseña" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-brand-orange" data-password-input data-password-primary><button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-xs font-bold text-aula-orange-soft transition hover:bg-white/5" data-password-toggle>Ver</button></div><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="password"></small></label>
              <label class="grid gap-2"><span class="text-xs font-bold text-white/70">Confirmar contraseña</span><div class="relative"><input type="password" name="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25 focus:border-brand-orange" data-password-input><button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-xs font-bold text-aula-orange-soft transition hover:bg-white/5" data-password-toggle>Ver</button></div><small class="min-h-4 text-[11px] text-brand-coral" data-error-for="password_confirmation"></small></label>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-5" aria-label="Requisitos de contraseña">
              <span class="text-xs font-bold text-white/60">Tu contraseña necesita:</span>
              <ul class="mt-4 grid gap-3 text-xs text-white/35" data-password-rules>
                <li data-rule="length"><span class="mr-2">○</span> mínimo 8 caracteres</li>
                <li data-rule="uppercase"><span class="mr-2">○</span> una letra mayúscula</li>
                <li data-rule="number"><span class="mr-2">○</span> un número</li>
                <li data-rule="special"><span class="mr-2">○</span> un carácter especial</li>
              </ul>
            </div>
          </div>
        </section>

        <p class="hidden rounded-xl border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-xs leading-5 text-red-200" data-form-status role="alert"></p>

        <div class="flex flex-col gap-4 border-t border-white/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
          <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-3 rounded-full bg-aula-orange px-7 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5">Crear cuenta <span>→</span></button>
          <p class="text-sm text-white/45">¿Ya tienes una cuenta? <a href="<?= e(url('login.php')) ?>" class="font-bold text-white transition hover:text-[#9fcbd5]">Iniciar sesión</a></p>
        </div>
      </form>
    </div>
  </section>

  <aside class="relative hidden min-h-screen overflow-hidden border-l border-white/10 xl:block">
    <img src="<?= e(asset('images/registro-bg.png')) ?>" alt="Espacio de aprendizaje AulaGo" class="absolute inset-0 h-full w-full object-cover opacity-45">
    <div class="absolute inset-0 bg-gradient-to-b from-aula-bg/20 via-aula-bg/58 to-aula-bg"></div>
    <div class="absolute -right-40 top-1/3 h-[520px] w-[520px] rounded-full bg-[#79b8c7]/10 blur-3xl"></div>
    <div class="relative z-10 flex h-full min-h-screen flex-col justify-between p-12">
      <div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#9fcbd5]">Tu espacio</p><h2 class="mt-5 font-editorial text-6xl font-semibold leading-[0.94] text-white">Aprende.<br>Comparte.<br><span class="text-aula-orange-soft">Sigue creciendo.</span></h2></div>
      <div class="rounded-2xl border border-white/10 bg-aula-bg/60 p-6 backdrop-blur"><span class="text-[10px] uppercase tracking-[0.15em] text-white/35">AulaGo / 2026</span><p class="mt-3 text-sm leading-7 text-white/65" data-role-description>Explora nuevas ideas y avanza por cursos diseñados para aprender a tu ritmo.</p></div>
    </div>
  </aside>
</main>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
