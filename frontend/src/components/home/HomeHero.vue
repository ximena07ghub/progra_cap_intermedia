<script setup>
import heroImage from '../../assets/images/hero-butterflies.jpeg'
</script>

<template>
  <section class="relative isolate overflow-hidden border-b border-white/10 bg-aula-bg" aria-labelledby="home-hero-title">
    <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute -left-28 top-20 h-72 w-72 rounded-full bg-brand-magenta/[0.07] blur-3xl"></div>
      <div class="absolute bottom-0 left-[34%] h-80 w-80 rounded-full bg-brand-orange/[0.06] blur-3xl"></div>
      <div class="absolute right-[8%] top-10 h-72 w-72 rounded-full bg-brand-green/[0.04] blur-3xl"></div>
    </div>

    <div class="mx-auto grid min-h-[calc(100vh-73px)] max-w-[1440px] lg:grid-cols-[0.96fr_1.04fr]">
      <div class="flex items-center px-6 py-16 sm:px-10 sm:py-20 lg:px-14 lg:py-24 xl:pl-20">
        <div class="max-w-2xl">
          <p class="inline-flex items-center gap-3 text-[11px] font-bold uppercase tracking-[0.2em] text-aula-green">
            <span class="h-1.5 w-1.5 rounded-full bg-aula-green"></span>
            Aprendizaje flexible · bienestar cotidiano
          </p>

          <h1 id="home-hero-title" class="mt-6 font-editorial text-5xl font-semibold leading-[0.94] tracking-[-0.035em] text-aula-cream sm:text-6xl lg:text-[4.7rem] xl:text-[5.4rem]">
            Aprende algo que
            <span class="text-[#79b8c7]">despierte</span>
            tu
            <span class="text-aula-orange">curiosidad.</span>
          </h1>

          <p class="mt-7 max-w-xl text-sm leading-7 text-white/55 sm:text-base sm:leading-8">
            Cursos para desarrollar habilidades, cuidar tu energía mental y aprender sin convertir el progreso en una carrera. Explora temas creativos, tecnológicos y personales con rutas claras y prácticas.
          </p>

          <div class="mt-9 flex flex-wrap gap-3">
            <RouterLink
              to="/cursos"
              class="inline-flex min-h-12 items-center justify-center rounded-full bg-aula-orange px-6 text-sm font-extrabold text-aula-bg transition duration-300 hover:-translate-y-0.5 hover:bg-aula-orange-soft focus:outline-none focus:ring-2 focus:ring-aula-orange focus:ring-offset-2 focus:ring-offset-aula-bg"
            >
              Explorar cursos
            </RouterLink>

            <a
              href="#como-funciona"
              class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/15 bg-white/[0.025] px-6 text-sm font-bold text-aula-cream transition hover:border-white/30 hover:bg-white/[0.05] focus:outline-none focus:ring-2 focus:ring-white/25 focus:ring-offset-2 focus:ring-offset-aula-bg"
            >
              ¿Cómo funciona?
            </a>
          </div>

          <div class="mt-11 grid max-w-lg grid-cols-3 gap-5 border-t border-white/10 pt-6">
            <div>
              <strong class="block text-2xl font-semibold text-aula-cream">12</strong>
              <span class="mt-1 block text-[11px] leading-5 text-white/35">categorías</span>
            </div>
            <div>
              <strong class="block text-2xl font-semibold text-[#79b8c7]">24+</strong>
              <span class="mt-1 block text-[11px] leading-5 text-white/35">rutas</span>
            </div>
            <div>
              <strong class="block text-2xl font-semibold text-aula-orange-soft">4.9</strong>
              <span class="mt-1 block text-[11px] leading-5 text-white/35">valoración</span>
            </div>
          </div>
        </div>
      </div>

      <div class="relative min-h-[440px] overflow-hidden lg:min-h-full">
        <img
          :src="heroImage"
          alt="Mariposas blancas entre luces suaves"
          class="absolute inset-0 h-full w-full object-cover object-center"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-aula-bg via-aula-bg/25 to-transparent lg:from-aula-bg lg:via-aula-bg/10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/70 via-transparent to-aula-plum-soft/10"></div>
        <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10 lg:p-12">
          <div class="ml-auto max-w-sm rounded-2xl border border-white/10 bg-aula-bg/55 p-5 backdrop-blur-md">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-orange-soft">Aprender también es cuidarte</p>
            <p class="mt-3 text-sm leading-6 text-white/70">Avanza con curiosidad, pausas y objetivos que sí puedas sostener.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
