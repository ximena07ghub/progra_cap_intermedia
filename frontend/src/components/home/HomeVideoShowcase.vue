<script setup>
const videos = [
  { id: 1, label: 'Sesión de ejemplo', title: 'Cómo iniciar sin exigirte perfección', duration: '04:25', accent: 'text-aula-orange-soft', glow: 'bg-aula-orange/10' },
  { id: 2, label: 'Clase práctica', title: 'Una pausa breve para recuperar enfoque', duration: '08:10', accent: 'text-[#9fcbd5]', glow: 'bg-[#79b8c7]/10' },
  { id: 3, label: 'Contenido complementario', title: 'Diseña una rutina que sí puedas sostener', duration: '05:45', accent: 'text-aula-green', glow: 'bg-aula-green/10' },
]
</script>

<template>
  <section class="border-b border-white/10 bg-aula-bg px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="videos-title">
    <div class="mx-auto max-w-aula">
      <div class="max-w-3xl">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#c291af]">Conoce la experiencia</p>
        <h2 id="videos-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">
          Mira cómo se vive una clase.
        </h2>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-white/42 sm:text-base">
          Espacios preparados para sustituirse por tus videos reales cuando conectes el contenido del curso al backend.
        </p>
      </div>

      <div class="mt-12 grid gap-5 lg:grid-cols-3">
        <article v-for="video in videos" :key="video.id" class="overflow-hidden rounded-[1.6rem] border border-white/10 bg-aula-surface">
          <div :class="['group relative flex aspect-video items-center justify-center overflow-hidden', video.glow]">
            <div class="absolute inset-0 bg-gradient-to-br from-white/[0.03] via-transparent to-aula-plum/10"></div>
            <button
              type="button"
              class="relative flex h-14 w-14 items-center justify-center rounded-full border border-white/15 bg-aula-bg/60 text-aula-cream backdrop-blur transition group-hover:scale-105 group-hover:border-aula-orange/50 focus:outline-none focus:ring-2 focus:ring-aula-orange focus:ring-offset-2 focus:ring-offset-aula-bg"
              :aria-label="`Reproducir ${video.title}`"
            >
              <svg viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-6 w-6" aria-hidden="true"><path d="M8 5v14l11-7L8 5Z" /></svg>
            </button>
          </div>

          <div class="p-6">
            <div class="flex items-center justify-between gap-4">
              <p :class="['text-[10px] font-bold uppercase tracking-[0.15em]', video.accent]">{{ video.label }}</p>
              <span class="text-xs font-semibold text-white/30">{{ video.duration }}</span>
            </div>
            <h3 class="mt-3 text-base font-bold leading-6 text-aula-cream">{{ video.title }}</h3>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
