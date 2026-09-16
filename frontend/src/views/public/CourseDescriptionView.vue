<template>
  <div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main class="flex-1">
      <section class="border-b border-white/10">
        <div class="mx-auto grid max-w-aula gap-10 px-6 py-14 lg:grid-cols-[.88fr_1.12fr] lg:px-8 lg:py-20">
          <div>
            <div class="relative overflow-hidden border border-white/10 bg-aula-green-dark shadow-aula">
              <img :src="course.image" :alt="course.title" class="aspect-[4/3] h-full w-full object-cover opacity-80" />
              <div class="absolute inset-0 bg-gradient-to-t from-aula-bg/70 via-transparent to-transparent"></div>
              <span class="absolute left-5 top-5 bg-aula-bg/75 px-3 py-1 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green backdrop-blur">
                {{ course.category }}
              </span>
            </div>

            <div class="flex flex-col gap-4 border-x border-b border-white/10 bg-aula-surface/70 p-5 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <span class="block text-[10px] uppercase tracking-[0.12em] text-white/35">Acceso completo</span>
                <strong class="mt-1 block text-xl text-aula-cream">{{ course.price }}</strong>
              </div>
              <RouterLink
                :to="purchaseTarget"
                class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5"
              >
                {{ purchaseLabel }}
              </RouterLink>
            </div>
          </div>

          <div class="self-center">
            <div class="flex flex-wrap gap-2">
              <span class="border border-aula-green/30 bg-aula-green/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green">{{ course.category }}</span>
              <span class="border border-aula-yellow/30 bg-aula-yellow/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-yellow">{{ course.level }}</span>
            </div>

            <h1 class="mt-5 font-editorial text-5xl leading-[0.95] text-aula-cream sm:text-6xl lg:text-7xl">{{ course.title }}</h1>
            <p class="mt-6 max-w-2xl text-base leading-8 text-white/50">{{ course.description }}</p>

            <div class="mt-8 border-l border-aula-orange pl-4">
              <span class="block text-[10px] uppercase tracking-[0.12em] text-white/35">Instructor</span>
              <strong class="mt-1 block text-sm text-aula-cream">{{ course.instructor }}</strong>
            </div>

            <div class="mt-9 grid grid-cols-3 gap-4 border-y border-white/10 py-6">
              <div>
                <strong class="block text-2xl text-aula-cream">{{ averageRating }}</strong>
                <span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Calificación</span>
              </div>
              <div>
                <strong class="block text-2xl text-aula-cream">{{ course.lessons }}</strong>
                <span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Lecciones</span>
              </div>
              <div>
                <strong class="block text-2xl text-aula-cream">{{ course.duration }}</strong>
                <span class="mt-1 block text-[10px] uppercase tracking-[0.1em] text-white/30">Contenido</span>
              </div>
            </div>

            <div class="mt-8">
              <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green">Sobre este curso</p>
              <h2 class="mt-3 font-editorial text-3xl text-aula-cream sm:text-4xl">Una ruta práctica para profundizar en {{ course.accent.toLowerCase() }}.</h2>
              <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45">Combina explicaciones claras, actividades breves y práctica guiada. La estructura ya está preparada para que después cada lección venga desde tu backend.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
        <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Contenido</p>
            <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Lo que encontrarás dentro.</h2>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/40">Una estructura genérica que puedes reemplazar por módulos y lecciones reales desde la base de datos.</p>
          </div>

          <div class="border-t border-white/10">
            <article v-for="(lesson, index) in lessons" :key="lesson.title" class="grid grid-cols-[42px_1fr_auto] gap-4 border-b border-white/10 py-5 sm:items-center">
              <span class="font-mono text-[11px] text-aula-orange">{{ String(index + 1).padStart(2, '0') }}</span>
              <div>
                <h3 class="text-sm font-bold text-aula-cream">{{ lesson.title }}</h3>
                <p class="mt-1 text-xs leading-5 text-white/40">{{ lesson.description }}</p>
              </div>
              <span class="text-[10px] font-bold text-white/30">{{ lesson.duration }}</span>
            </article>
          </div>
        </div>
      </section>

      <section class="border-y border-white/10 bg-white/[0.015]">
        <div class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
          <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
            <div>
              <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Opiniones verificadas</p>
              <h2 class="mt-3 font-editorial text-4xl text-aula-cream sm:text-5xl">Comentarios de quienes terminaron el curso.</h2>
              <div class="mt-6 flex items-end gap-3">
                <strong class="text-5xl text-aula-orange">{{ averageRating }}</strong>
                <span class="pb-1 text-sm text-white/35">/ 5 · {{ reviews.length }} opiniones</span>
              </div>
            </div>

            <div class="border-t border-white/10">
              <article v-for="review in reviews" :key="review.id" class="grid grid-cols-[44px_1fr] gap-4 border-b border-white/10 py-5">
                <span class="grid h-11 w-11 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold">{{ review.avatar }}</span>
                <div>
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <strong class="text-sm">{{ review.name }}</strong>
                    <span class="text-xs text-aula-yellow">{{ '★'.repeat(review.rating) }}<span class="text-white/15">{{ '★'.repeat(5 - review.rating) }}</span></span>
                  </div>
                  <span class="mt-1 block text-[10px] uppercase tracking-[0.08em] text-white/30">Curso completado · {{ formatDate(review.completedAt) }}</span>
                  <p class="mt-3 text-sm leading-7 text-white/50">{{ review.comment }}</p>
                </div>
              </article>
              <p v-if="!reviews.length" class="py-8 text-sm text-white/35">Todavía no hay opiniones de estudiantes que hayan terminado este curso.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="border-y border-white/10 bg-white/[0.02]">
        <div class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
          <div class="mb-8 flex items-end justify-between gap-4">
            <div>
              <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">También podría interesarte</p>
              <h2 class="mt-3 font-editorial text-4xl text-aula-cream">Continúa explorando.</h2>
            </div>
            <RouterLink to="/cursos" class="hidden text-xs font-bold uppercase tracking-[0.08em] text-aula-orange sm:block">Ver catálogo →</RouterLink>
          </div>

          <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <CourseCard v-for="item in relatedCourses" :key="item.id" :course="item" />
          </div>
        </div>
      </section>
    </main>
    <PublicFooter />
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import PublicHeader from '../../components/layout/PublicHeader.vue'
import PublicFooter from '../../components/layout/PublicFooter.vue'
import CourseCard from '../../components/courses/CourseCard.vue'
import { courses } from '../../data/courses.js'
import { courseReviews } from '../../data/mockPortal.js'
import { useSessionStore } from '../../stores/session.js'
import { formatDate } from '../../utils/format.js'

const route = useRoute()
const session = useSessionStore()
const { authenticated } = storeToRefs(session)

const course = computed(() => courses.find((item) => item.slug === route.params.slug) || courses[0])
const relatedCourses = computed(() => courses.filter((item) => item.id !== course.value.id).slice(0, 3))
const reviews = computed(() => courseReviews[course.value.slug] || [])
const averageRating = computed(() => {
  if (!reviews.value.length) return '—'
  const total = reviews.value.reduce((sum, review) => sum + review.rating, 0)
  return (total / reviews.value.length).toFixed(1)
})
const purchaseTarget = computed(() => {
  if (!authenticated.value) return `/login?redirect=/estudiante/checkout/${course.value.slug}`
  if (session.role === 'estudiante') return `/estudiante/checkout/${course.value.slug}`
  return session.homeRoute
})
const purchaseLabel = computed(() => {
  if (!authenticated.value) return 'Comenzar curso'
  return session.role === 'estudiante' ? 'Comprar curso' : 'Volver a mi espacio'
})


const lessons = [
  { title: 'Introducción y mapa del curso', description: 'Conoce el objetivo de la ruta y cómo aprovechar cada módulo.', duration: '10 min' },
  { title: 'Conceptos esenciales', description: 'Construye una base clara antes de pasar a la práctica.', duration: '18 min' },
  { title: 'Práctica guiada', description: 'Aplica lo aprendido mediante una actividad acompañada paso a paso.', duration: '24 min' },
  { title: 'Proyecto y próximos pasos', description: 'Integra los conceptos y define cómo continuar aprendiendo.', duration: '28 min' },
]

watch(
  () => route.params.slug,
  () => window.scrollTo({ top: 0, behavior: 'smooth' }),
)
</script>

