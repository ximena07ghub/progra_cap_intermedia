<?php
$workspaceMode = $workspaceMode ?? 'instructor';

$workspaceConfigs = [
    'instructor' => [
        'label' => 'Instructor',
        'home' => url('instructor/'),
        'account' => url('instructor/cuenta.php'),
        'links' => [
            ['label' => 'Resumen', 'to' => url('instructor/')],
            ['label' => 'Mis cursos', 'to' => url('instructor/cursos.php')],
            ['label' => 'Ventas', 'to' => url('instructor/ventas.php')],
            ['label' => 'Mensajes', 'to' => url('instructor/mensajes.php')],
        ],
    ],
    'administrador' => [
        'label' => 'Administrador',
        'home' => url('admin/'),
        'account' => null,
        'links' => [
            ['label' => 'Resumen', 'to' => url('admin/')],
            ['label' => 'Categorías', 'to' => url('admin/categorias.php')],
            ['label' => 'Usuarios', 'to' => url('admin/usuarios.php')],
            ['label' => 'Comentarios', 'to' => url('admin/comentarios.php')],
        ],
    ],
];

$config = $workspaceConfigs[$workspaceMode] ?? $workspaceConfigs['instructor'];
$menuId = 'workspace-' . $workspaceMode . '-mobile-menu';
?>
<header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/90 backdrop-blur-xl">
  <div class="mx-auto flex max-w-aula items-center gap-5 px-6 py-4 lg:px-8">
    <a href="<?= e($config['home']) ?>" class="shrink-0" aria-label="Ir al panel de AulaGo">
      <img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto">
    </a>

    <nav class="hidden items-center gap-5 lg:flex" aria-label="Navegación de <?= e($config['label']) ?>">
      <?php foreach ($config['links'] as $item): ?>
        <a href="<?= e($item['to']) ?>" class="text-sm font-bold text-white/65 transition hover:text-white"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="ml-auto flex items-center gap-3">
      <?php if ($config['account']): ?>
        <a href="<?= e($config['account']) ?>" class="hidden text-sm font-bold text-white/65 transition hover:text-white sm:block">Mi cuenta</a>
      <?php endif; ?>

      <span class="hidden border-l border-white/10 pl-4 text-xs font-bold uppercase tracking-[0.08em] text-aula-green sm:block"><?= e($config['label']) ?></span>
      <a href="<?= e(url('actions/logout.php')) ?>" class="hidden text-xs font-bold text-white/45 transition hover:text-aula-orange sm:block">Salir</a>

      <button type="button" class="grid h-10 w-10 place-items-center border border-white/15 bg-transparent text-white lg:hidden" aria-expanded="false" aria-controls="<?= e($menuId) ?>" aria-label="Abrir menú" data-menu-toggle="<?= e($menuId) ?>">
        <span data-menu-icon>☰</span>
      </button>
    </div>
  </div>

  <div id="<?= e($menuId) ?>" class="border-t border-white/10 px-6 py-5 lg:hidden" data-menu-panel hidden>
    <nav class="mx-auto grid max-w-aula gap-4">
      <?php foreach ($config['links'] as $item): ?>
        <a href="<?= e($item['to']) ?>" class="text-sm font-bold text-white/80" data-menu-close><?= e($item['label']) ?></a>
      <?php endforeach; ?>
      <?php if ($config['account']): ?>
        <a href="<?= e($config['account']) ?>" class="text-sm font-bold text-white/80" data-menu-close>Mi cuenta</a>
      <?php endif; ?>
      <a href="<?= e(url('actions/logout.php')) ?>" class="w-fit text-sm font-bold text-aula-orange">Cerrar sesión</a>
    </nav>
  </div>
</header>
