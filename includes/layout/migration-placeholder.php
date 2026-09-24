<section class="min-h-[58vh] bg-aula-bg px-6 py-20 lg:px-8">
  <div class="mx-auto max-w-aula rounded-[1.8rem] border border-white/10 bg-white/[0.025] p-8 sm:p-12">
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-orange-soft">Migración PHP</p>
    <h1 class="mt-4 font-editorial text-4xl font-semibold text-aula-cream sm:text-5xl"><?= e($placeholderTitle ?? 'Vista pendiente') ?></h1>
    <p class="mt-5 max-w-2xl text-sm leading-7 text-white/50">
      <?= e($placeholderText ?? 'Esta ruta ya existe en la nueva estructura, pero su contenido se migrará en la siguiente etapa.') ?>
    </p>
    <a href="<?= e(url()) ?>" class="mt-8 inline-flex min-h-11 items-center justify-center rounded-full border border-aula-orange/45 bg-aula-orange/10 px-5 text-sm font-bold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg">Volver al inicio</a>
  </div>
</section>
