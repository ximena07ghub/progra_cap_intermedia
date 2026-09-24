<?php
/** @var array $course */
$courseHref = url('curso.php?slug=' . rawurlencode((string) $course['slug']));
?>
<article
  class="group overflow-hidden rounded-aula-lg border border-white/10 bg-aula-surface/80 transition duration-300 hover:-translate-y-1 hover:border-aula-orange/30 hover:shadow-aula"
  data-course-card
  data-title="<?= e(text_lower($course['title'] . ' ' . $course['category'] . ' ' . $course['instructor'] . ' ' . $course['description'] . ' ' . ($course['accent'] ?? '') . ' ' . ($course['wellbeingFocus'] ?? ''))) ?>"
  data-category="<?= e($course['categoryId']) ?>"
  data-level="<?= e($course['level']) ?>"
>
  <a href="<?= e($courseHref) ?>" class="block h-full">
    <div class="relative aspect-[16/10] overflow-hidden bg-aula-green-dark">
      <img
        src="<?= e(asset('images/' . $course['image'])) ?>"
        alt="<?= e($course['title']) ?>"
        class="h-full w-full object-cover opacity-80 transition duration-500 group-hover:scale-[1.03] group-hover:opacity-95"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/75 via-transparent to-aula-bg/5"></div>
      <span class="absolute left-4 top-4 rounded-full border border-white/10 bg-aula-bg/75 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green backdrop-blur-md">
        <?= e($course['category']) ?>
      </span>
      <span class="absolute bottom-4 right-4 rounded-full bg-aula-orange px-3 py-1.5 text-xs font-extrabold text-aula-bg shadow-aula">
        <?= e($course['price']) ?>
      </span>
    </div>

    <div class="flex h-full flex-col p-5">
      <div class="flex flex-wrap items-center gap-2">
        <span class="rounded-full bg-aula-green/10 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-[0.1em] text-aula-green">
          <?= e($course['level']) ?>
        </span>
        <span class="text-[11px] font-semibold text-white/40"><?= e($course['duration']) ?></span>
      </div>

      <h3 class="mt-4 font-editorial text-[1.65rem] font-semibold leading-[1.08] text-aula-cream transition group-hover:text-white">
        <?= e($course['title']) ?>
      </h3>
      <p class="mt-3 line-clamp-2 text-sm leading-6 text-white/50">
        <?= e($course['accent'] ?: $course['description']) ?>
      </p>

      <div class="mt-5 flex items-center gap-2 text-xs">
        <span class="font-extrabold text-aula-orange-soft"><?= e(number_format((float) ($course['rating'] ?? 0), 1)) ?></span>
        <span class="tracking-[0.05em] text-aula-yellow" aria-hidden="true">★★★★★</span>
        <span class="text-white/30"><?= e($course['reviews']) ?> reseñas</span>
      </div>

      <div class="mt-5 flex items-center justify-between gap-4 border-t border-white/10 pt-4 text-[11px] text-white/40">
        <span><?= e(number_format((int) ($course['students'] ?? 0), 0, '.', ',')) ?> estudiantes</span>
        <span><?= e($course['language']) ?></span>
      </div>
    </div>
  </a>
</article>
