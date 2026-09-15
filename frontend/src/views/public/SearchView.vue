<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
      <section class="max-w-4xl">
        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Búsqueda AulaGo</p>
        <h1 class="mt-4 font-editorial text-5xl text-aula-cream sm:text-6xl">Busca una idea, una habilidad o un tema.</h1>

        <form class="mt-8 flex border-b border-white/20" @submit.prevent="submitSearch">
          <input
            v-model="query"
            type="search"
            placeholder="Ej. Vue, hábitos, diseño…"
            class="min-w-0 flex-1 bg-transparent py-4 text-lg text-white outline-none placeholder:text-white/25"
          />
          <button type="submit" class="bg-transparent px-4 text-sm font-extrabold uppercase tracking-[0.08em] text-aula-orange">Buscar ↗</button>
        </form>
      </section>

      <section class="mt-12 border-t border-white/10 pt-8">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.08em] text-white/35">{{ resultsLabel }}</p>
            <h2 class="mt-2 font-editorial text-4xl text-aula-cream">{{ heading }}</h2>
          </div>
          <span class="text-xs text-white/35">{{ filteredCourses.length }} resultado{{ filteredCourses.length === 1 ? '' : 's' }}</span>
        </div>

        <div v-if="filteredCourses.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <CourseCard v-for="course in filteredCourses" :key="course.id" :course="course" />
        </div>

        <div v-else class="border border-white/10 bg-aula-surface/50 p-8 sm:p-10">
          <p class="font-editorial text-3xl text-aula-cream">No encontramos coincidencias.</p>
          <p class="mt-3 max-w-xl text-sm leading-6 text-white/45">Prueba con otro término o explora directamente por categorías.</p>
          <RouterLink to="/categorias" class="mt-6 inline-flex bg-aula-orange px-5 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Ver categorías</RouterLink>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PublicHeader from '../../components/layout/PublicHeader.vue'
import CourseCard from '../../components/courses/CourseCard.vue'
import { courses } from '../../data/courses.js'

const route = useRoute()
const router = useRouter()
const query = ref(String(route.query.q || ''))

watch(
  () => route.query.q,
  (value) => {
    query.value = String(value || '')
  },
)

const normalizedQuery = computed(() => query.value.trim().toLocaleLowerCase('es'))

const filteredCourses = computed(() => {
  if (!normalizedQuery.value) return courses
  return courses.filter((course) => {
    const haystack = [course.title, course.category, course.level, course.instructor, course.description]
      .join(' ')
      .toLocaleLowerCase('es')
    return haystack.includes(normalizedQuery.value)
  })
})

const heading = computed(() => normalizedQuery.value ? `Resultados para “${query.value.trim()}”` : 'Cursos para explorar')
const resultsLabel = computed(() => normalizedQuery.value ? 'Resultados encontrados' : 'Empieza por aquí')

function submitSearch() {
  const q = query.value.trim()
  router.replace({ query: q ? { q } : {} })
}
</script>
