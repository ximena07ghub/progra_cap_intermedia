<?php
$featuredCourses = array_slice(courses(), 0, 24);
$initialCourse = $featuredCourses[1] ?? $featuredCourses[0] ?? null;
?>
<section id="cursos-destacados" class="relative overflow-hidden border-b border-white/10 bg-aula-bg-soft px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="courses-title">
  <div class="pointer-events-none absolute -right-32 top-20 h-72 w-72 rounded-full bg-brand-blue/[0.045] blur-3xl"></div>
  <div class="pointer-events-none absolute bottom-0 left-[20%] h-80 w-80 rounded-full bg-brand-orange/[0.035] blur-3xl"></div>

  <div class="relative mx-auto max-w-aula">
    <div class="grid gap-7 lg:grid-cols-[1fr_.58fr] lg:items-end">
      <div class="max-w-3xl">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-orange-soft">Selección para explorar</p>
        <h2 id="courses-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">
          Cursos para descubrir algo nuevo.
        </h2>
      </div>
      <p class="max-w-md text-sm leading-7 text-white/45 lg:justify-self-end">
        Recorre la colección completa sin que el carrusel se detenga. La ficha de la derecha siempre corresponde al curso centrado.
      </p>
    </div>

    <?php if ($initialCourse): ?>
    <div class="mt-12 grid items-center gap-8 lg:grid-cols-[minmax(0,1.62fr)_minmax(300px,0.7fr)]" data-home-carousel>
      <div class="carousel-stage min-w-0 overflow-hidden">
        <div class="swiper home-courses-swiper">
          <div class="swiper-wrapper">
            <?php foreach ($featuredCourses as $course): ?>
              <div
                class="swiper-slide"
                data-course-title="<?= e($course['title']) ?>"
                data-course-description="<?= e($course['description']) ?>"
                data-course-level="<?= e($course['level']) ?>"
                data-course-instructor="<?= e($course['instructor']) ?>"
                data-course-wellbeing="<?= e($course['wellbeingFocus']) ?>"
                data-course-duration="<?= e($course['duration']) ?>"
                data-course-rating="<?= e($course['rating']) ?>"
                data-course-href="<?= e(url('curso.php?slug=' . rawurlencode($course['slug']))) ?>"
              >
                <article class="course-carousel-card group block w-full overflow-hidden rounded-[1.15rem] border border-white/10 bg-aula-surface text-left shadow-aula transition duration-300 hover:border-white/20">
                  <div class="relative aspect-[4/3] overflow-hidden bg-aula-green-dark">
                    <img src="<?= e(asset('images/' . $course['image'])) ?>" alt="<?= e($course['title']) ?>" class="h-full w-full object-cover opacity-72 transition duration-500 group-hover:scale-[1.025] group-hover:opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#100d10] via-[#100d10]/50 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-[#100d10] via-[#100d10]/65 to-transparent"></div>
                    <span class="absolute left-5 top-5 border border-white/12 bg-aula-bg/75 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-aula-cream/85 backdrop-blur">
                      <?= e($course['category']) ?>
                    </span>
                    <div class="absolute inset-x-0 bottom-0 p-6">
                      <p class="text-[11px] font-semibold text-aula-orange-soft drop-shadow-[0_2px_8px_rgba(0,0,0,.9)]"><?= e($course['accent']) ?></p>
                      <h3 class="mt-2 font-editorial text-3xl font-semibold leading-[1.02] text-aula-cream drop-shadow-[0_2px_12px_rgba(0,0,0,.95)]"><?= e($course['title']) ?></h3>
                    </div>
                  </div>
                  <div class="flex items-center justify-between gap-4 border-t border-white/[0.06] p-5">
                    <div class="flex items-center gap-2">
                      <span class="text-aula-yellow" aria-hidden="true">★</span>
                      <span class="text-sm font-bold text-aula-cream"><?= e($course['rating']) ?></span>
                    </div>
                    <span class="text-xs text-white/38"><?= e($course['duration']) ?></span>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="mt-5 flex items-center justify-center gap-4">
          <button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.02] text-aula-cream transition hover:border-aula-orange/60 hover:bg-white/[0.04]" aria-label="Curso anterior" data-carousel-prev>←</button>
          <span class="min-w-16 text-center text-xs font-bold text-white/35" data-carousel-counter>02 / <?= e(str_pad((string) count($featuredCourses), 2, '0', STR_PAD_LEFT)) ?></span>
          <button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.02] text-aula-cream transition hover:border-aula-orange/60 hover:bg-white/[0.04]" aria-label="Curso siguiente" data-carousel-next>→</button>
        </div>
      </div>

      <aside class="course-carousel-detail relative z-10 border border-white/10 bg-[#1b171c]/95 p-7 backdrop-blur lg:p-8" aria-live="polite">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#79b8c7]">Curso seleccionado</p>
        <h3 class="mt-4 font-editorial text-3xl font-semibold leading-tight text-aula-cream" data-course-detail="title"><?= e($initialCourse['title']) ?></h3>
        <p class="mt-5 text-sm leading-7 text-white/55" data-course-detail="description"><?= e($initialCourse['description']) ?></p>
        <div class="mt-7 space-y-3 border-t border-white/10 pt-6 text-sm">
          <div class="flex justify-between gap-5"><span class="text-white/35">Nivel</span><span class="font-semibold text-white/75" data-course-detail="level"><?= e($initialCourse['level']) ?></span></div>
          <div class="flex justify-between gap-5"><span class="text-white/35">Instructor</span><span class="text-right font-semibold text-white/75" data-course-detail="instructor"><?= e($initialCourse['instructor']) ?></span></div>
          <div class="flex justify-between gap-5"><span class="text-white/35">Bienestar relacionado</span><span class="max-w-[12rem] text-right font-semibold text-white/75" data-course-detail="wellbeing"><?= e($initialCourse['wellbeingFocus']) ?></span></div>
        </div>
        <div class="mt-5 flex items-center justify-between border-t border-white/10 pt-4 text-xs"><span class="text-white/35" data-course-detail="duration"><?= e($initialCourse['duration']) ?></span><span class="font-bold text-aula-yellow">★ <span data-course-detail="rating"><?= e($initialCourse['rating']) ?></span></span></div>
        <a href="<?= e(url('curso.php?slug=' . rawurlencode($initialCourse['slug']))) ?>" data-course-detail="href" class="mt-7 inline-flex min-h-12 w-full items-center justify-center border border-aula-orange/60 bg-aula-orange/10 px-5 text-sm font-bold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg">
          Conocer el curso
        </a>
      </aside>
    </div>
    <?php endif; ?>
  </div>
</section>
