<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="relative overflow-hidden border-b border-white/10">
        <div class="pointer-events-none absolute -right-36 top-0 h-72 w-72 rounded-full bg-aula-orange/10 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/4 top-0 h-56 w-56 rounded-full bg-aula-plum/10 blur-3xl"></div>

        <div class="mx-auto max-w-aula px-6 py-12 text-center lg:px-8 lg:py-16">
          <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Cursos AulaGo</p>
          <h1 class="mx-auto mt-4 max-w-3xl font-editorial text-4xl font-semibold leading-[1.02] text-aula-cream sm:text-5xl lg:text-6xl">
            Descubre una ruta para <span class="text-aula-orange-soft">seguir aprendiendo.</span>
          </h1>
          <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/40">
            Explora cursos creativos, técnicos y personales conectados con formas más sostenibles de estudiar, crear y trabajar.
          </p>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-12">
        <form
          class="grid gap-4 rounded-aula-lg border border-white/10 bg-aula-surface/60 p-4 md:grid-cols-[1.3fr_.8fr_.75fr_auto] md:items-end md:p-5"
          @submit.prevent
        >
          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Buscar curso</span>
            <input
              v-model="query"
              type="search"
              placeholder="Diseño, hábitos, Vue, dibujo…"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange/60"
            />
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Categoría</span>
            <select
              v-model="category"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60"
            >
              <option value="">Todas</option>
              <option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Nivel</span>
            <select
              v-model="level"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60"
            >
              <option value="">Todos</option>
              <option value="Inicial">Inicial</option>
              <option value="Intermedio">Intermedio</option>
              <option value="Avanzado">Avanzado</option>
            </select>
          </label>

          <button
            type="button"
            class="min-h-[46px] rounded-aula border border-aula-orange/30 bg-aula-orange/10 px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg"
            @click="clearFilters"
          >
            Limpiar
          </button>
        </form>

        <div class="mt-8 flex gap-2 overflow-x-auto pb-2">
          <button
            type="button"
            class="shrink-0 rounded-full border px-4 py-2 text-xs font-bold transition"
            :class="!category ? 'border-aula-orange/40 bg-aula-orange/10 text-aula-orange-soft' : 'border-white/10 bg-aula-surface/50 text-white/40 hover:text-white'"
            @click="setCategory('')"
          >
            Todos
          </button>

          <button
            v-for="item in categories"
            :key="item.id"
            type="button"
            class="shrink-0 rounded-full border px-4 py-2 text-xs font-bold transition"
            :class="category === item.id ? 'border-aula-orange/40 bg-aula-orange/10 text-aula-orange-soft' : 'border-white/10 bg-aula-surface/50 text-white/40 hover:text-white'"
            @click="setCategory(item.id)"
          >
            {{ item.name }}
          </button>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-b border-white/10 pb-6 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Cursos disponibles</p>
            <h2 class="mt-2 font-editorial text-3xl font-semibold text-aula-cream sm:text-4xl">
              {{ collectionTitle }}
            </h2>
          </div>
          <span class="text-xs text-white/40">{{ filteredCourses.length }} curso{{ filteredCourses.length === 1 ? '' : 's' }}</span>
        </div>

        <div v-if="filteredCourses.length" class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          <CourseCard v-for="course in filteredCourses" :key="course.id" :course="course" />
        </div>

        <div v-else class="mt-8 rounded-aula-lg border border-white/10 bg-aula-surface/50 p-8">
          <h3 class="font-editorial text-3xl font-semibold text-aula-cream">No encontramos cursos con esos filtros.</h3>
          <p class="mt-3 text-sm text-white/40">Prueba otra categoría, nivel o término de búsqueda.</p>
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
import { categories, courses } from '../../data/courses.js'

const route = useRoute()
const router = useRouter()

const query = ref(typeof route.query.q === 'string' ? route.query.q : '')
const category = ref(
  categories.some((item) => item.id === route.query.categoria)
    ? route.query.categoria
    : '',
)
const level = ref(['Inicial', 'Intermedio', 'Avanzado'].includes(route.query.nivel) ? route.query.nivel : '')

const filteredCourses = computed(() => {
  const search = query.value.trim().toLocaleLowerCase('es')

  return courses.filter((course) => {
    const matchesCategory = !category.value || course.categoryId === category.value
    const matchesLevel = !level.value || course.level === level.value
    const haystack = `${course.title} ${course.category} ${course.instructor} ${course.description} ${course.accent} ${course.wellbeingFocus}`.toLocaleLowerCase('es')
    const matchesSearch = !search || haystack.includes(search)

    return matchesCategory && matchesLevel && matchesSearch
  })
})

const collectionTitle = computed(() => {
  const selected = categories.find((item) => item.id === category.value)
  if (selected) return selected.name
  if (query.value.trim()) return `Resultados para “${query.value.trim()}”`
  return 'Todos los cursos'
})

watch([query, category, level], () => {
  router.replace({
    query: {
      ...(query.value.trim() ? { q: query.value.trim() } : {}),
      ...(category.value ? { categoria: category.value } : {}),
      ...(level.value ? { nivel: level.value } : {}),
    },
  })
})

function setCategory(id) {
  category.value = id
}

function clearFilters() {
  query.value = ''
  category.value = ''
  level.value = ''
}
</script>
