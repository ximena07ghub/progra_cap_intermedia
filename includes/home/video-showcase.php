<?php
$videos = [
    ['Sesión de ejemplo', 'Cómo iniciar sin exigirte perfección', '04:25', 'text-aula-orange-soft', 'bg-aula-orange/10'],
    ['Clase práctica', 'Una pausa breve para recuperar enfoque', '08:10', 'text-[#9fcbd5]', 'bg-[#79b8c7]/10'],
    ['Contenido complementario', 'Diseña una rutina que sí puedas sostener', '05:45', 'text-aula-green', 'bg-aula-green/10'],
];
?>
<section class="border-b border-white/10 bg-aula-bg px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="videos-title">
  <div class="mx-auto max-w-aula">
    <div class="max-w-3xl"><p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#c291af]">Conoce la experiencia</p><h2 id="videos-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Mira cómo se vive una clase.</h2><p class="mt-5 max-w-2xl text-sm leading-7 text-white/42 sm:text-base">Espacios preparados para sustituirse por tus videos reales cuando conectemos el contenido a MySQL.</p></div>
    <div class="mt-12 grid gap-5 lg:grid-cols-3">
      <?php foreach ($videos as $video): ?>
        <article class="overflow-hidden rounded-[1.6rem] border border-white/10 bg-aula-surface">
          <div class="group relative flex aspect-video items-center justify-center overflow-hidden <?= e($video[4]) ?>">
            <div class="absolute inset-0 bg-gradient-to-br from-white/[0.03] via-transparent to-aula-plum/10"></div>
            <button type="button" class="relative flex h-14 w-14 items-center justify-center rounded-full border border-white/15 bg-aula-bg/60 text-aula-cream backdrop-blur transition group-hover:scale-105 group-hover:border-aula-orange/50" aria-label="Reproducir <?= e($video[1]) ?>"><span class="ml-1 text-xl">▶</span></button>
          </div>
          <div class="p-6"><div class="flex items-center justify-between gap-4"><p class="text-[10px] font-bold uppercase tracking-[0.15em] <?= e($video[3]) ?>"><?= e($video[0]) ?></p><span class="text-xs font-semibold text-white/30"><?= e($video[2]) ?></span></div><h3 class="mt-3 text-base font-bold leading-6 text-aula-cream"><?= e($video[1]) ?></h3></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
