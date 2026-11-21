<?php
$testimonials = home_public_reviews(6);
$ratingSummary = home_public_rating_summary();
?>
<section class="relative overflow-hidden bg-aula-bg-soft px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="testimonials-title">
  <div class="pointer-events-none absolute -bottom-32 right-0 h-80 w-80 rounded-full bg-brand-magenta/[0.04] blur-3xl"></div>
  <div class="relative mx-auto max-w-aula">
    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
      <div class="max-w-3xl">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-orange-soft">Opiniones verificadas</p>
        <h2 id="testimonials-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Lo que dicen quienes completaron sus cursos.</h2>
      </div>

      <?php if (($ratingSummary['total'] ?? 0) > 0 && $ratingSummary['promedio'] !== null): ?>
        <div class="flex w-fit items-center gap-3 rounded-full border border-white/10 bg-white/[0.025] px-5 py-3">
          <span class="text-aula-yellow">★</span>
          <span class="text-sm font-bold text-white/70"><?= e(number_format((float) $ratingSummary['promedio'], 1)) ?> promedio</span>
          <span class="text-xs text-white/30">· <?= e($ratingSummary['total']) ?> reseñas</span>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($testimonials): ?>
      <div class="mt-12 grid gap-5 lg:grid-cols-3">
        <?php foreach ($testimonials as $testimonial): ?>
          <?php
          $rating = max(1, min(5, (int) $testimonial['calificacion']));
          $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
          ?>
          <article class="flex h-full flex-col rounded-[1.6rem] border border-white/10 bg-white/[0.02] p-7 transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.035]">
            <div class="text-aula-yellow" aria-label="<?= e($rating) ?> de 5 estrellas"><?= e($stars) ?></div>
            <blockquote class="mt-6 flex-1 text-sm leading-7 text-white/58">“<?= e($testimonial['comentario']) ?>”</blockquote>
            <div class="mt-8 border-t border-white/10 pt-5">
              <p class="font-bold text-aula-cream"><?= e($testimonial['estudiante']) ?></p>
              <a href="<?= e(url('curso.php?slug=' . rawurlencode((string) $testimonial['curso_slug']))) ?>" class="mt-1 block text-xs leading-5 text-white/32 transition hover:text-aula-orange-soft"><?= e($testimonial['curso']) ?> →</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="mt-12 border border-dashed border-white/10 p-8">
        <p class="text-sm text-white/40">Todavía no hay reseñas verificadas de cursos publicados.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
