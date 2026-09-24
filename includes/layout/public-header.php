<?php
$searchQuery = trim((string) ($_GET['q'] ?? ''));
?>
<header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/90 text-aula-cream backdrop-blur-xl">
  <div class="mx-auto flex max-w-7xl items-center gap-5 px-6 py-4 lg:px-8">
    <a href="<?= e(url()) ?>" class="shrink-0" aria-label="Ir al inicio de AulaGo">
      <img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto">
    </a>

    <nav class="hidden items-center gap-6 md:flex" aria-label="Navegación principal">
      <a href="<?= e(url()) ?>" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Inicio</a>
      <a href="<?= e(url('cursos.php')) ?>" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Cursos</a>
      <a href="<?= e(url('categorias.php')) ?>" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Categorías</a>
    </nav>

    <form class="ml-auto hidden min-w-0 max-w-sm flex-1 md:flex" role="search" action="<?= e(url('buscar.php')) ?>" method="get">
      <input
        name="q"
        value="<?= e($searchQuery) ?>"
        type="search"
        placeholder="Buscar un curso"
        class="min-w-0 flex-1 border-b border-white/15 bg-transparent px-1 py-2 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-aula-orange/60"
      >
      <button type="submit" class="border-b border-white/15 bg-transparent px-3 text-aula-orange-soft transition hover:text-aula-cream" aria-label="Buscar">↗</button>
    </form>

    <div class="hidden items-center gap-3 md:flex">
      <?php if (is_authenticated()): ?>
        <a href="<?= e(role_home_url()) ?>" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Mi espacio</a>
        <a
          href="<?= e(role_account_url()) ?>"
          class="inline-flex min-h-10 items-center justify-center rounded-full border border-aula-orange/35 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:-translate-y-0.5 hover:bg-aula-orange hover:text-aula-bg"
        >
          Mi cuenta
        </a>
      <?php else: ?>
        <a href="<?= e(url('login.php')) ?>" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Iniciar sesión</a>
        <a
          href="<?= e(url('registro.php')) ?>"
          class="inline-flex min-h-10 items-center justify-center rounded-full border border-aula-orange/35 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:-translate-y-0.5 hover:bg-aula-orange hover:text-aula-bg"
        >
          Registrarse
        </a>
      <?php endif; ?>
    </div>

    <button
      type="button"
      class="ml-auto grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-transparent text-white md:hidden"
      aria-expanded="false"
      aria-controls="public-mobile-menu"
      aria-label="Abrir menú"
      data-menu-toggle="public-mobile-menu"
    >
      <span class="text-lg" data-menu-icon>☰</span>
    </button>
  </div>

  <div id="public-mobile-menu" class="border-t border-white/10 bg-aula-bg px-6 py-5 md:hidden" data-menu-panel hidden>
    <nav class="mx-auto grid max-w-7xl gap-4" aria-label="Navegación móvil">
      <a href="<?= e(url()) ?>" class="text-sm font-bold text-white/80" data-menu-close>Inicio</a>
      <a href="<?= e(url('cursos.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Cursos</a>
      <a href="<?= e(url('categorias.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Categorías</a>
      <?php if (is_authenticated()): ?>
        <a href="<?= e(role_home_url()) ?>" class="text-sm font-bold text-aula-orange-soft" data-menu-close>Mi espacio</a>
      <?php else: ?>
        <a href="<?= e(url('login.php')) ?>" class="text-sm font-bold text-aula-orange-soft" data-menu-close>Iniciar sesión</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
