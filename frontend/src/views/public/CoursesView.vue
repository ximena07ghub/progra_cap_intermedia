<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="relative overflow-hidden border-b border-white/10">
        <div class="pointer-events-none absolute -right-44 -top-48 h-[500px] w-[500px] rounded-full bg-aula-orange/[0.07] blur-3xl"></div>
        <div class="pointer-events-none absolute left-[18%] top-8 h-60 w-60 rounded-full bg-aula-plum/[0.06] blur-3xl"></div>

        <div class="mx-auto grid max-w-aula gap-8 px-6 py-14 lg:grid-cols-[1fr_.72fr] lg:px-8 lg:py-18">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Catálogo AulaGo</p>
            <h1 class="mt-4 max-w-3xl font-editorial text-5xl font-semibold leading-[0.98] text-aula-cream sm:text-6xl lg:text-7xl">
              Elige un curso y conoce <span class="text-aula-orange-soft">todo lo que incluye.</span>
            </h1>
          </div>

          <div class="self-end lg:pb-1">
            <p class="max-w-lg text-sm leading-7 text-white/45">
              Aquí encuentras la colección completa. Entra al curso que te interese para revisar su contenido, instructor, precio y continuar con la inscripción o pago.
            </p>

            <RouterLink
              to="/categorias"
              class="mt-6 inline-flex min-h-11 items-center justify-center rounded-full border border-white/15 bg-white/[0.025] px-5 text-xs font-extrabold text-aula-cream/80 transition hover:border-aula-orange/40 hover:text-aula-orange-soft"
            >
              Buscar por categoría o nivel ↗
            </RouterLink>
          </div>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-12 lg:px-8 lg:py-16">
        <div class="flex flex-col gap-3 border-b border-white/10 pb-7 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Cursos disponibles</p>
            <h2 class="mt-2 font-editorial text-4xl font-semibold text-aula-cream">Explora la colección</h2>
          </div>
          <span class="text-xs text-white/35">{{ courses.length }} cursos</span>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          <CourseCard v-for="course in courses" :key="course.id" :course="course" />
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import PublicHeader from '../../components/layout/PublicHeader.vue'
import CourseCard from '../../components/courses/CourseCard.vue'
import { courses } from '../../data/courses.js'
</script>
