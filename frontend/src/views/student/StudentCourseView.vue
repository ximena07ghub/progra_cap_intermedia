<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-14">
      <RouterLink to="/estudiante#mis-cursos" class="text-xs font-bold uppercase tracking-[0.08em] text-white/45 transition hover:text-aula-orange">
        ← Volver a mis cursos
      </RouterLink>

      <section class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div>
          <div class="overflow-hidden border border-white/10 bg-black">
            <div class="relative aspect-video">
              <img :src="course.image" :alt="course.title" class="h-full w-full object-cover opacity-45" />
              <div class="absolute inset-0 grid place-items-center bg-aula-bg/20">
                <button class="grid h-20 w-20 place-items-center rounded-full border border-white/30 bg-aula-bg/65 text-2xl text-white backdrop-blur transition hover:scale-105" type="button" aria-label="Reproducir lección">
                  ▶
                </button>
              </div>
              <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-6">
                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-aula-green">Lección actual</p>
                <h1 class="mt-2 font-editorial text-3xl text-white sm:text-4xl">{{ currentLesson.title }}</h1>
              </div>
            </div>
          </div>

          <div class="border-x border-b border-white/10 bg-aula-surface/60 p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-5">
              <div>
                <p class="bg-transparent text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">{{ course.title }}</p>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/50">{{ currentLesson.description }}</p>
              </div>
              <button type="button" class="border border-white/15 bg-transparent px-4 py-2 text-xs font-bold text-white/70 transition hover:border-aula-green hover:text-white">
                Marcar como completada
              </button>
            </div>

            <div class="mt-8 grid gap-5 border-t border-white/10 pt-6 sm:grid-cols-3">
              <div>
                <span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Duración</span>
                <strong class="mt-1 block text-sm text-aula-cream">12 min</strong>
              </div>
              <div>
                <span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Recursos</span>
                <strong class="mt-1 block text-sm text-aula-cream">2 archivos</strong>
              </div>
              <div>
                <span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Actividad</span>
                <strong class="mt-1 block text-sm text-aula-cream">1 ejercicio</strong>
              </div>
            </div>
          </div>
        </div>

        <aside class="h-fit border border-white/10 bg-aula-surface/65 lg:sticky lg:top-28">
          <div class="border-b border-white/10 p-5">
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-aula-green">Contenido</p>
                <h2 class="mt-2 font-editorial text-2xl text-aula-cream">Tu ruta</h2>
              </div>
              <span class="text-xs font-extrabold text-aula-orange">{{ progress }}%</span>
            </div>
            <div class="mt-4 h-1 overflow-hidden rounded-full bg-white/10">
              <div class="h-full bg-aula-orange" :style="{ width: `${progress}%` }"></div>
            </div>
          </div>

          <div class="max-h-[560px] overflow-y-auto">
            <section v-for="module in modules" :key="module.id" class="border-b border-white/10 last:border-b-0">
              <div class="px-5 py-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/35">Módulo {{ module.id }}</p>
                <h3 class="mt-1 text-sm font-bold text-aula-cream">{{ module.title }}</h3>
              </div>
              <button
                v-for="lesson in module.lessons"
                :key="lesson.id"
                type="button"
                class="flex w-full items-start gap-3 border-t border-white/5 bg-transparent px-5 py-4 text-left transition hover:bg-white/[0.03]"
                :class="lesson.id === currentLesson.id ? 'bg-aula-green/10' : ''"
                @click="currentLesson = lesson"
              >
                <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full border text-[10px]" :class="lesson.done ? 'border-aula-green bg-aula-green text-aula-bg' : 'border-white/15 text-white/35'">
                  {{ lesson.done ? '✓' : lesson.id }}
                </span>
                <span>
                  <strong class="block text-xs font-semibold" :class="lesson.id === currentLesson.id ? 'text-aula-green' : 'text-white/70'">{{ lesson.title }}</strong>
                  <small class="mt-1 block text-[10px] text-white/30">{{ lesson.duration }}</small>
                </span>
              </button>
            </section>
          </div>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import StudentHeader from '../../components/layout/StudentHeader.vue'
import { courses, enrolledCourses } from '../../data/courses.js'

const route = useRoute()
const course = computed(() => courses.find((item) => item.slug === route.params.slug) || courses[0])
const enrollment = computed(() => enrolledCourses.find((item) => item.slug === course.value.slug))
const progress = computed(() => enrollment.value?.progress ?? 18)

const modules = [
  {
    id: 1,
    title: 'Entender antes de cambiar',
    lessons: [
      { id: 1, title: 'Cómo funciona una rutina', duration: '09 min', done: true, description: 'Identifica las partes de una rutina y observa cómo contexto, señal y recompensa se conectan.' },
      { id: 2, title: 'Detectar tus señales', duration: '12 min', done: true, description: 'Aprende a reconocer las señales que ya disparan comportamientos automáticos en tu día.' },
      { id: 3, title: 'Diseñar una señal de inicio', duration: '12 min', done: false, description: 'Transforma una intención abstracta en una señal visible que facilite empezar incluso cuando tu motivación cambia.' },
    ],
  },
  {
    id: 2,
    title: 'Diseñar un sistema sostenible',
    lessons: [
      { id: 4, title: 'Reducir fricción', duration: '10 min', done: false, description: 'Reorganiza tu entorno para que iniciar la acción deseada requiera menos esfuerzo.' },
      { id: 5, title: 'Progreso mínimo viable', duration: '14 min', done: false, description: 'Construye una versión pequeña de tu hábito que puedas repetir con consistencia.' },
      { id: 6, title: 'Revisar y ajustar', duration: '11 min', done: false, description: 'Usa una revisión corta para entender qué funcionó y qué debes modificar.' },
    ],
  },
]

const currentLesson = ref(modules[0].lessons[2])
</script>
