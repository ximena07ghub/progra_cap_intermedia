<?php
$videos = home_course_videos(3);
?>
<section class="border-b border-white/10 bg-aula-bg px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="videos-title">
  <div class="mx-auto max-w-aula">
    <div class="grid gap-7 lg:grid-cols-[1fr_.58fr] lg:items-end">
      <div class="max-w-3xl">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#c291af]">Clases publicadas</p>
        <h2 id="videos-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Mira cómo se vive una clase.</h2>
      </div>
      <p class="max-w-md text-sm leading-7 text-white/42 lg:justify-self-end">Estos videos provienen de las lecciones que los instructores han publicado en AulaGo.</p>
    </div>

    <?php if ($videos): ?>
      <div class="mt-12 grid gap-5 lg:grid-cols-3">
        <?php foreach ($videos as $video): ?>
          <article class="overflow-hidden rounded-[1.25rem] border border-white/10 bg-aula-surface">
            <div class="aspect-video overflow-hidden bg-black">
              <iframe
                src="<?= e($video['embed']) ?>"
                title="<?= e($video['lesson']['titulo']) ?>"
                class="h-full w-full"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            </div>
            <div class="p-5">
              <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green"><?= e($video['course']['title']) ?> · <?= e($video['lesson']['nivel']) ?></p>
              <h3 class="mt-2 text-base font-bold leading-6 text-aula-cream"><?= e($video['lesson']['titulo']) ?></h3>
              <a href="<?= e(url('curso.php?slug=' . rawurlencode($video['course']['slug']))) ?>" class="mt-4 inline-block text-xs font-bold text-aula-orange-soft">Conocer el curso →</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="mt-10 border border-dashed border-white/10 p-8 text-sm text-white/35">Todavía no hay lecciones con video de YouTube publicadas.</div>
    <?php endif; ?>
  </div>
</section>
