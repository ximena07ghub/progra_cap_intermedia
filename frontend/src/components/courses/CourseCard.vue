<template>
  <article
    class="group overflow-hidden rounded-aula-lg border border-white/10 bg-aula-surface/80 transition duration-300 hover:-translate-y-1 hover:border-aula-orange/30 hover:shadow-aula"
  >
    <RouterLink :to="`/cursos/${course.slug}`" class="block h-full">
      <div class="relative aspect-[16/10] overflow-hidden bg-aula-green-dark">
        <img
          :src="course.image"
          :alt="course.title"
          class="h-full w-full object-cover opacity-80 transition duration-500 group-hover:scale-[1.03] group-hover:opacity-95"
        />

        <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/75 via-transparent to-aula-bg/5"></div>

        <span
          class="absolute left-4 top-4 rounded-full border border-white/10 bg-aula-bg/75 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green backdrop-blur-md"
        >
          {{ course.category }}
        </span>

        <span
          class="absolute bottom-4 right-4 rounded-full bg-aula-orange px-3 py-1.5 text-xs font-extrabold text-aula-bg shadow-aula"
        >
          {{ course.price }}
        </span>
      </div>

      <div class="flex h-full flex-col p-5">
        <div class="flex flex-wrap items-center gap-2">
          <span class="rounded-full bg-aula-green/10 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-[0.1em] text-aula-green">
            {{ course.level }}
          </span>
          <span class="text-[11px] font-semibold text-white/40">{{ course.duration }}</span>
        </div>

        <h3 class="mt-4 font-editorial text-[1.65rem] font-semibold leading-[1.08] text-aula-cream transition group-hover:text-white">
          {{ course.title }}
        </h3>

        <p class="mt-3 line-clamp-2 text-sm leading-6 text-white/50">
          {{ course.accent || course.description }}
        </p>

        <div class="mt-5 flex items-center gap-2 text-xs">
          <span class="font-extrabold text-aula-orange-soft">{{ Number(course.rating || 0).toFixed(1) }}</span>
          <span class="tracking-[0.05em] text-aula-yellow" aria-hidden="true">★★★★★</span>
          <span class="text-white/30">{{ course.reviews }} reseñas</span>
        </div>

        <div class="mt-5 flex items-center justify-between gap-4 border-t border-white/10 pt-4 text-[11px] text-white/40">
          <span>{{ Number(course.students || 0).toLocaleString('es-MX') }} estudiantes</span>
          <span>{{ course.language }}</span>
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
