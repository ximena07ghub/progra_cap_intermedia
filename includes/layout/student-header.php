<header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/90 backdrop-blur-xl">
  <div class="mx-auto flex max-w-aula items-center gap-5 px-6 py-4 lg:px-8">
    <a href="<?= e(url('estudiante/')) ?>" class="shrink-0" aria-label="Ir a mi espacio AulaGo">
      <img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto">
    </a>

    <nav class="hidden items-center gap-6 lg:flex" aria-label="Navegación del estudiante">
      <a class="text-sm font-bold text-white/65 transition hover:text-white" href="<?= e(url('estudiante/')) ?>">Mi espacio</a>
      <a class="text-sm font-bold text-white/65 transition hover:text-white" href="<?= e(url('estudiante/#mis-cursos')) ?>">Mis cursos</a>
      <a class="text-sm font-bold text-white/65 transition hover:text-white" href="<?= e(url('estudiante/kardex.php')) ?>">Kardex</a>
      <a class="text-sm font-bold text-white/65 transition hover:text-white" href="<?= e(url('estudiante/mensajes.php')) ?>">Mensajes</a>
      <a class="text-sm font-bold text-white/65 transition hover:text-white" href="<?= e(url('categorias.php')) ?>">Categorías</a>
    </nav>

    <form class="ml-auto hidden max-w-xs flex-1 md:flex" role="search" action="<?= e(url('buscar.php')) ?>" method="get">
      <input name="q" type="search" placeholder="Buscar cursos" class="min-w-0 flex-1 border-b border-white/20 bg-transparent py-2 text-sm text-white outline-none placeholder:text-white/30 focus:border-aula-orange">
      <button class="border-b border-white/20 bg-transparent px-3 text-aula-orange" type="submit" aria-label="Buscar">↗</button>
    </form>

    <div class="flex items-center gap-3">
      <a href="<?= e(url('estudiante/cuenta.php')) ?>" class="hidden items-center gap-3 border-l border-white/10 pl-4 sm:flex">
        <span class="grid h-9 w-9 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-white"><?= e(strtoupper(substr(user_name(), 0, 1))) ?></span>
        <span class="text-sm font-bold text-white/80">Mi cuenta</span>
      </a>

      <a href="<?= e(url('actions/logout.php')) ?>" class="hidden bg-transparent text-xs font-bold text-white/45 transition hover:text-aula-orange sm:block">Salir</a>

      <button type="button" class="grid h-10 w-10 place-items-center border border-white/15 bg-transparent text-white lg:hidden" aria-expanded="false" aria-controls="student-mobile-menu" aria-label="Abrir menú" data-menu-toggle="student-mobile-menu">
        <span data-menu-icon>☰</span>
      </button>
    </div>
  </div>

  <div id="student-mobile-menu" class="border-t border-white/10 px-6 py-5 lg:hidden" data-menu-panel hidden>
    <nav class="mx-auto grid max-w-aula gap-4">
      <a href="<?= e(url('estudiante/')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Mi espacio</a>
      <a href="<?= e(url('estudiante/kardex.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Kardex</a>
      <a href="<?= e(url('estudiante/mensajes.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Mensajes</a>
      <a href="<?= e(url('categorias.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Categorías</a>
      <a href="<?= e(url('estudiante/cuenta.php')) ?>" class="text-sm font-bold text-white/80" data-menu-close>Mi cuenta</a>
      <a href="<?= e(url('actions/logout.php')) ?>" class="w-fit bg-transparent text-sm font-bold text-aula-orange">Cerrar sesión</a>
    </nav>
  </div>
</header>
