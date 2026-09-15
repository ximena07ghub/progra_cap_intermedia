<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main>
      <section class="relative overflow-hidden border-b border-white/10">
        <div class="pointer-events-none absolute -right-44 -top-52 h-[520px] w-[520px] rounded-full bg-aula-orange/10 blur-3xl"></div>
        <div class="mx-auto grid max-w-aula gap-10 px-6 py-16 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-24">
          <div class="self-center">
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Tu espacio AulaGo</p>
            <h1 class="mt-5 max-w-3xl font-editorial text-5xl font-normal leading-[0.95] text-aula-cream sm:text-6xl lg:text-7xl">
              Sigue aprendiendo desde donde <em class="text-aula-orange">lo dejaste.</em>
            </h1>
            <p class="mt-6 max-w-xl text-base leading-7 text-white/50">
              Aquí viven tus cursos inscritos, tu progreso y nuevas rutas recomendadas a partir de lo que ya exploraste.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
              <a href="#mis-cursos" class="inline-flex min-h-11 items-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5">
                Continuar aprendiendo
              </a>
              <RouterLink to="/categorias" class="inline-flex min-h-11 items-center border border-white/15 px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-white/80 transition hover:border-white/35 hover:text-white">
                Explorar categorías
              </RouterLink>
            </div>
          </div>

          <aside class="border border-white/10 bg-aula-surface/80 p-6 shadow-aula sm:p-8">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-white/35">Tu semana</p>
                <p class="mt-2 font-editorial text-3xl text-aula-cream">3 sesiones</p>
              </div>
              <span class="grid h-11 w-11 place-items-center rounded-full border border-aula-green/30 bg-aula-green/10 text-aula-green">↗</span>
            </div>
            <div class="mt-8 grid grid-cols-3 gap-3 border-t border-white/10 pt-6">
              <div>
                <strong class="block text-xl text-aula-cream">68%</strong>
                <span class="mt-1 block text-[11px] text-white/40">Curso principal</span>
              </div>
              <div>
                <strong class="block text-xl text-aula-cream">7</strong>
                <span class="mt-1 block text-[11px] text-white/40">Lecciones vistas</span>
              </div>
              <div>
                <strong class="block text-xl text-aula-cream">4.2 h</strong>
                <span class="mt-1 block text-[11px] text-white/40">Aprendiendo</span>
              </div>
            </div>
          </aside>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-16 lg:px-8 lg:py-20">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Recomendación personal</p>
            <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Porque avanzaste en tus cursos…</h2>
          </div>
          <RouterLink to="/cursos" class="text-xs font-extrabold uppercase tracking-[0.08em] text-aula-orange">Ver catálogo →</RouterLink>
        </div>

        <article class="grid overflow-hidden border border-white/10 bg-aula-green-soft/70 lg:grid-cols-[1.05fr_.95fr]">
          <div class="relative min-h-[330px] overflow-hidden lg:min-h-[430px]">
            <img :src="recommendedCourse.image" :alt="recommendedCourse.title" class="absolute inset-0 h-full w-full object-cover opacity-75" />
            <div class="absolute inset-0 bg-gradient-to-r from-aula-bg/20 via-transparent to-aula-bg/60"></div>
          </div>
          <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-12">
            <span class="w-fit bg-aula-orange px-3 py-1 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-bg">Siguiente ruta</span>
            <h3 class="mt-6 font-editorial text-4xl text-aula-cream sm:text-5xl">{{ recommendedCourse.title }}</h3>
            <p class="mt-4 max-w-lg text-sm leading-7 text-white/55">{{ recommendedCourse.description }}</p>
            <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-xs text-white/45">
              <span>{{ recommendedCourse.level }}</span>
              <span>{{ recommendedCourse.duration }}</span>
              <span>{{ recommendedCourse.instructor }}</span>
            </div>
            <RouterLink
              :to="`/cursos/${recommendedCourse.slug}`"
              class="mt-8 inline-flex w-fit min-h-11 items-center bg-aula-cream px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5"
            >
              Conocer el curso
            </RouterLink>
          </div>
        </article>
      </section>

      <section id="mis-cursos" class="border-y border-white/10 bg-white/[0.02]">
        <div class="mx-auto max-w-aula px-6 py-16 lg:px-8 lg:py-20">
          <div class="mb-9 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Mi aprendizaje</p>
              <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Mis cursos</h2>
            </div>
            <p class="max-w-sm text-sm leading-6 text-white/45">Retoma una lección o revisa cuánto has avanzado en cada ruta.</p>
          </div>

          <div class="grid gap-5 lg:grid-cols-2">
            <ProgressCourseCard v-for="course in enrolledCourses" :key="course.id" :course="course" />
          </div>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-16 lg:px-8 lg:py-20">
        <div class="grid gap-8 lg:grid-cols-[.75fr_1.25fr] lg:items-end">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Explora tu espacio</p>
            <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Aprender también es elegir qué sigue.</h2>
          </div>
          <div class="grid gap-3 sm:grid-cols-3">
            <RouterLink to="/categorias" class="border border-white/10 bg-aula-surface/60 p-5 transition hover:border-aula-orange/50">
              <span class="font-mono text-[10px] text-aula-orange">01</span>
              <strong class="mt-8 block text-sm text-aula-cream">Explorar categorías</strong>
              <p class="mt-2 text-xs leading-5 text-white/40">Descubre rutas por tema.</p>
            </RouterLink>
            <RouterLink to="/buscar" class="border border-white/10 bg-aula-surface/60 p-5 transition hover:border-aula-orange/50">
              <span class="font-mono text-[10px] text-aula-orange">02</span>
              <strong class="mt-8 block text-sm text-aula-cream">Buscar un curso</strong>
              <p class="mt-2 text-xs leading-5 text-white/40">Encuentra un tema concreto.</p>
            </RouterLink>
            <RouterLink to="/estudiante/cuenta" class="border border-white/10 bg-aula-surface/60 p-5 transition hover:border-aula-orange/50">
              <span class="font-mono text-[10px] text-aula-orange">03</span>
              <strong class="mt-8 block text-sm text-aula-cream">Mi cuenta</strong>
              <p class="mt-2 text-xs leading-5 text-white/40">Preferencias y perfil.</p>
            </RouterLink>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import StudentHeader from '../../components/layout/StudentHeader.vue'
import ProgressCourseCard from '../../components/courses/ProgressCourseCard.vue'
import { enrolledCourses, recommendedCourse } from '../../data/courses.js'
</script>
