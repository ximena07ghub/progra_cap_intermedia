<template>
  <article class="grid overflow-hidden border border-white/10 bg-aula-surface/70 sm:grid-cols-[150px_1fr]">
    <div class="min-h-36 overflow-hidden bg-aula-green-dark sm:min-h-full">
      <img :src="course.image" :alt="course.title" class="h-full w-full object-cover opacity-70" />
    </div>

    <div class="flex flex-col p-5">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green">{{ course.category }}</p>
          <h3 class="mt-2 font-editorial text-2xl text-aula-cream">{{ course.title }}</h3>
        </div>
        <span class="text-xs font-extrabold text-aula-orange">{{ course.progress }}%</span>
      </div>

      <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/10" aria-label="Progreso del curso">
        <div class="h-full rounded-full bg-aula-orange" :style="{ width: `${course.progress}%` }"></div>
      </div>

      <p class="mt-4 text-xs text-white/45">Siguiente: {{ course.nextLesson }}</p>

      <RouterLink
        :to="`/estudiante/curso/${course.slug}`"
        class="mt-5 inline-flex w-fit items-center gap-2 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-cream transition hover:text-aula-orange"
      >
        Continuar <span aria-hidden="true">→</span>
      </RouterLink>
    </div>
  </article>
</template>

<script setup>
defineProps({
  course: {
    type: Object,
    required: true,
  },
})
</script>
