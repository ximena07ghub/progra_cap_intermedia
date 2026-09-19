<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="border-b border-white/10">
        <div class="mx-auto grid max-w-aula gap-8 px-6 py-14 lg:grid-cols-[1fr_.78fr] lg:px-8 lg:py-18">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Explora por tema</p>
            <h1 class="mt-4 max-w-3xl font-editorial text-5xl font-semibold leading-[0.98] text-aula-cream sm:text-6xl">
              Filtra la colección hasta encontrar <span class="text-aula-orange-soft">tu siguiente curso.</span>
            </h1>
          </div>

          <p class="self-end max-w-lg text-sm leading-7 text-white/45">
            Usa esta sección cuando quieras buscar por tema, categoría o nivel. Los resultados aparecen aquí mismo para evitar saltos innecesarios entre pantallas.
          </p>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-12">
        <form
          class="grid gap-4 rounded-aula-lg border border-white/10 bg-aula-surface/60 p-4 md:grid-cols-[1.4fr_.8fr_auto] md:items-end md:p-5"
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
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Nivel</span>
            <select
              v-model="level"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60"
            >
              <option value="">Todos los niveles</option>
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

        <div class="mt-8 flex flex-wrap gap-2">
          <button
            type="button"
            class="rounded-full border px-4 py-2 text-xs font-bold transition"
            :class="!category ? 'border-aula-orange/40 bg-aula-orange/10 text-aula-orange-soft' : 'border-white/10 bg-aula-surface/50 text-white/40 hover:border-white/20 hover:text-white/70'"
            @click="setCategory('')"
          >
            Todos
          </button>

          <button
            v-for="item in categories"
            :key="item.id"
            type="button"
            class="rounded-full border px-4 py-2 text-xs font-bold transition"
            :class="category === item.id ? 'border-aula-orange/40 bg-aula-orange/10 text-aula-orange-soft' : 'border-white/10 bg-aula-surface/50 text-white/40 hover:border-white/20 hover:text-white/70'"
            @click="setCategory(item.id)"
          >
            {{ item.name }}
            <span class="ml-1 text-[10px] opacity-60">{{ categoryCourseCount(item.id) }}</span>
          </button>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-b border-white/10 pb-6 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Resultados</p>
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
          <p class="mt-3 text-sm text-white/40">Prueba otro término, nivel o categoría.</p>
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
  if (selected && query.value.trim()) return `${selected.name}: “${query.value.trim()}”`
  if (selected) return selected.name
  if (query.value.trim()) return `Resultados para “${query.value.trim()}”`
  if (level.value) return `Nivel ${level.value}`
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

function categoryCourseCount(categoryId) {
  return courses.filter((course) => course.categoryId === categoryId).length
}

function setCategory(id) {
  category.value = id
}

function clearFilters() {
  query.value = ''
  category.value = ''
  level.value = ''
}
</script>
