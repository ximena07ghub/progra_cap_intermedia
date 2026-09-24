<?php
$testimonials = [
    ['María T.', 'Desbloqueo creativo: dibujar sin paralizarte', 'Me gustó que el curso no tratara el bloqueo como falta de talento. Las actividades me ayudaron a volver a dibujar sin tanta presión.'],
    ['Eduardo R.', 'Fundamentos Web con ritmo sostenible', 'La estructura por niveles hizo fácil retomar el curso y organizar sesiones de estudio más realistas.'],
    ['Daniela S.', 'Movimiento para días de estrés', 'Las sesiones son breves y concretas. Me sirvió para hacer pausas más conscientes cuando llevo varias horas trabajando.'],
];
?>
<section class="relative overflow-hidden bg-aula-bg-soft px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="testimonials-title">
  <div class="pointer-events-none absolute -bottom-32 right-0 h-80 w-80 rounded-full bg-brand-magenta/[0.04] blur-3xl"></div>
  <div class="relative mx-auto max-w-aula">
    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
      <div class="max-w-3xl"><p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-orange-soft">Opiniones</p><h2 id="testimonials-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Lo que dicen quienes ya comenzaron.</h2></div>
      <div class="flex w-fit items-center gap-3 rounded-full border border-white/10 bg-white/[0.025] px-5 py-3"><span class="text-aula-yellow">★★★★★</span><span class="text-sm font-bold text-white/70">4.9 promedio</span></div>
    </div>
    <div class="mt-12 grid gap-5 lg:grid-cols-3">
      <?php foreach ($testimonials as $testimonial): ?>
        <article class="flex h-full flex-col rounded-[1.6rem] border border-white/10 bg-white/[0.02] p-7 transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.035]">
          <div class="text-aula-yellow">★★★★★</div>
          <blockquote class="mt-6 flex-1 text-sm leading-7 text-white/58">“<?= e($testimonial[2]) ?>”</blockquote>
          <div class="mt-8 border-t border-white/10 pt-5"><p class="font-bold text-aula-cream"><?= e($testimonial[0]) ?></p><p class="mt-1 text-xs leading-5 text-white/32"><?= e($testimonial[1]) ?></p></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
