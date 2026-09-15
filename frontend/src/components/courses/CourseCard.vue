<template>
  <article class="group overflow-hidden border border-white/10 bg-aula-surface/80 transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:shadow-aula">
    <RouterLink :to="`/cursos/${course.slug}`" class="block">
      <div class="relative aspect-[16/10] overflow-hidden bg-aula-green-dark">
        <img
          :src="course.image"
          :alt="course.title"
          class="h-full w-full object-cover opacity-75 transition duration-500 group-hover:scale-105 group-hover:opacity-90"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/80 via-transparent to-transparent"></div>
        <span class="absolute left-4 top-4 bg-aula-bg/75 px-3 py-1 font-mono text-[10px] font-semibold uppercase tracking-[0.15em] text-aula-green backdrop-blur">
          {{ course.category }}
        </span>
      </div>

      <div class="p-5">
        <div class="mb-3 flex items-center justify-between gap-4 text-[11px] font-bold uppercase tracking-[0.1em] text-white/40">
          <span>{{ course.level }}</span>
          <span>{{ course.duration }}</span>
        </div>
        <h3 class="font-editorial text-2xl font-normal leading-tight text-aula-cream">{{ course.title }}</h3>
        <p class="mt-3 text-sm leading-6 text-white/50">{{ course.description }}</p>
        <div class="mt-5 flex items-center justify-between gap-4 border-t border-white/10 pt-4">
          <span class="text-xs font-semibold text-white/55">{{ course.instructor }}</span>
          <span class="text-sm font-extrabold text-aula-orange">{{ course.price }}</span>
        </div>
      </div>
    </RouterLink>
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
