<footer class="mt-auto border-t border-white/10 bg-aula-bg-soft text-aula-cream">
  <div class="mx-auto grid max-w-7xl gap-10 px-6 py-12 md:grid-cols-[1fr_auto_auto] md:items-start lg:px-8">
    <div>
      <a href="<?= e(url()) ?>" class="inline-flex" aria-label="Ir al inicio de AulaGo">
        <img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto">
      </a>
      <p class="mt-4 max-w-xs text-sm leading-6 text-white/42">
        Aprende a tu ritmo y construye una ruta de aprendizaje que cuide tu curiosidad, tu energía y tu bienestar.
      </p>
    </div>

    <nav class="grid gap-3 text-sm" aria-label="Navegación secundaria">
      <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-aula-green">Explorar</span>
      <a href="<?= e(url('cursos.php')) ?>" class="font-semibold text-white/60 transition hover:text-white">Cursos</a>
      <a href="<?= e(url('categorias.php')) ?>" class="font-semibold text-white/60 transition hover:text-white">Categorías</a>
      <a href="<?= e(url('buscar.php')) ?>" class="font-semibold text-white/60 transition hover:text-white">Buscar</a>
    </nav>

    <nav class="grid gap-3 text-sm" aria-label="Navegación de cuenta">
      <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-aula-green">Cuenta</span>
      <?php if (is_authenticated()): ?>
        <a href="<?= e(role_home_url()) ?>" class="font-semibold text-white/60 transition hover:text-white">Mi espacio</a>
        <a href="<?= e(role_account_url()) ?>" class="font-semibold text-white/60 transition hover:text-white">Mi cuenta</a>
      <?php else: ?>
        <a href="<?= e(url('login.php')) ?>" class="font-semibold text-white/60 transition hover:text-white">Iniciar sesión</a>
        <a href="<?= e(url('registro.php')) ?>" class="font-semibold text-white/60 transition hover:text-white">Registrarse</a>
      <?php endif; ?>
    </nav>
  </div>

  <div class="border-t border-white/10">
    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-5 text-[11px] text-white/28 sm:flex-row sm:items-center sm:justify-between lg:px-8">
      <span>© <?= date('Y') ?> AulaGo</span>
      <span>Aprendizaje autodidacta y bienestar cotidiano</span>
    </div>
  </div>
</footer>
