<?php
/**
 * Variables esperadas:
 * $workspaceRoleLabel string
 * $workspaceItems array [['id'=>'dashboard','label'=>'Dashboard','icon'=>'⌂'], ...]
 */
$workspaceRoleLabel = $workspaceRoleLabel ?? 'Mi espacio';
$workspaceItems = $workspaceItems ?? [];
?>
<aside class="workspace-sidebar border-b border-white/10 bg-aula-bg-soft lg:border-b-0 lg:border-r" data-workspace-sidebar>
  <div class="flex h-full flex-col">
    <div class="flex items-center justify-between border-b border-white/10 px-5 py-5 lg:block lg:px-6 lg:py-7">
      <a href="<?= e(url()) ?>" class="inline-flex" aria-label="Ir al inicio de AulaGo">
        <img src="<?= e(asset('images/aula-logo.svg')) ?>" alt="AulaGo" class="h-9 w-auto">
      </a>
      <button type="button" class="grid h-10 w-10 place-items-center border border-white/10 text-white/70 lg:hidden" data-workspace-menu-toggle aria-label="Abrir navegación">☰</button>
      <p class="mt-5 hidden text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green lg:block"><?= e($workspaceRoleLabel) ?></p>
    </div>

    <nav class="workspace-nav hidden flex-1 gap-1 overflow-y-auto px-3 py-4 lg:flex lg:flex-col lg:px-4" data-workspace-nav aria-label="Navegación del espacio">
      <?php foreach ($workspaceItems as $item): ?>
        <a
          href="#<?= e($item['id']) ?>"
          class="workspace-nav-link flex items-center gap-3 border-l-2 border-transparent px-4 py-3 text-sm font-semibold text-white/45 transition hover:bg-white/[0.03] hover:text-white"
          data-workspace-link="<?= e($item['id']) ?>"
        >
          <span class="w-5 text-center text-xs text-white/30" aria-hidden="true"><?= e($item['icon'] ?? '•') ?></span>
          <span><?= e($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="hidden border-t border-white/10 p-4 lg:block">
      <div class="mb-3 flex items-center gap-3 px-2 py-2">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-aula-green-dark text-[11px] font-extrabold text-aula-green"><?= e(user_initials()) ?></span>
        <div class="min-w-0"><strong class="block truncate text-xs text-white/80"><?= e(user_name()) ?></strong><span class="mt-0.5 block truncate text-[10px] text-white/30"><?= e(user_email()) ?></span></div>
      </div>
      <a href="<?= e(url('actions/logout.php')) ?>" class="flex items-center justify-between border border-white/10 px-3 py-2.5 text-xs font-bold text-white/45 transition hover:border-aula-orange/30 hover:text-aula-orange-soft">Cerrar sesión <span>↗</span></a>
    </div>
  </div>
</aside>
