<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="border-b border-white/10">
        <div class="mx-auto max-w-aula px-6 py-12 lg:px-8 lg:py-16">
          <div class="max-w-3xl">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Explora por categoría</p>
            <h1 class="mt-4 font-editorial text-4xl font-semibold leading-[1.02] text-aula-cream sm:text-5xl lg:text-6xl">
              Encuentra el tema que mejor encaje con lo que quieres aprender.
            </h1>
            <p class="mt-5 max-w-2xl text-sm leading-7 text-white/40">
              Busca una categoría, filtra por el nivel disponible y entra directamente a los cursos relacionados.
            </p>
          </div>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-10 lg:px-8 lg:py-12">
        <form
          class="grid gap-4 rounded-aula-lg border border-white/10 bg-aula-surface/60 p-4 md:grid-cols-[1.25fr_.8fr_.8fr_auto] md:items-end md:p-5"
          @submit.prevent
        >
          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Buscar categoría</span>
            <input
              v-model="query"
              type="search"
              placeholder="Creatividad, tecnología, música…"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange/60"
            />
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Nivel disponible</span>
            <select
              v-model="level"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60"
            >
              <option value="">Cualquier nivel</option>
              <option value="Inicial">Inicial</option>
              <option value="Intermedio">Intermedio</option>
              <option value="Avanzado">Avanzado</option>
            </select>
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Ordenar</span>
            <select
              v-model="sortBy"
              class="rounded-aula border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange/60"
            >
              <option value="default">Recomendadas</option>
              <option value="name">A–Z</option>
              <option value="courses">Más cursos</option>
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

        <div class="mt-10 flex items-end justify-between gap-4 border-b border-white/10 pb-6">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Categorías disponibles</p>
            <h2 class="mt-2 font-editorial text-3xl font-semibold text-aula-cream sm:text-4xl">Explora por tema</h2>
          </div>
          <span class="text-xs text-white/40">{{ filteredCategories.length }} categoría{{ filteredCategories.length === 1 ? '' : 's' }}</span>
        </div>

        <div v-if="filteredCategories.length" class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <article
            v-for="category in filteredCategories"
            :key="category.id"
            class="group rounded-aula-lg border bg-aula-surface/60 p-6 transition hover:-translate-y-0.5 hover:border-white/20"
            :class="selectedCategory === category.id ? 'border-aula-orange/40 bg-aula-orange/10' : 'border-white/10'"
          >
            <div class="flex items-start justify-between gap-4">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-orange">{{ category.symbol }}</span>
                <p class="mt-5 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green">{{ category.eyebrow }}</p>
                <h3 class="mt-2 font-editorial text-3xl font-semibold text-aula-cream">{{ category.name }}</h3>
              </div>
              <span class="rounded-full border border-white/10 px-3 py-1 text-[10px] font-bold text-white/40">
                {{ categoryCourseCount(category.id) }} cursos
              </span>
            </div>

            <p class="mt-4 text-sm leading-6 text-white/40">{{ category.description }}</p>

            <div class="mt-5 flex flex-wrap gap-2">
              <span
                v-for="itemLevel in levelsForCategory(category.id)"
                :key="itemLevel"
                class="rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-semibold text-white/40"
              >
                {{ itemLevel }}
              </span>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
              <button
                type="button"
                class="text-xs font-extrabold text-aula-orange-soft transition hover:text-aula-cream"
                @click="selectCategory(category.id)"
              >
                {{ selectedCategory === category.id ? 'Ocultar cursos' : 'Ver cursos aquí' }}
              </button>

              <RouterLink
                :to="{ path: '/cursos', query: { categoria: category.id } }"
                class="text-xs font-bold text-white/40 transition hover:text-aula-green"
              >
                Abrir catálogo ↗
              </RouterLink>
            </div>
          </article>
        </div>

        <div v-else class="mt-8 rounded-aula-lg border border-white/10 bg-aula-surface/50 p-8">
          <h3 class="font-editorial text-3xl font-semibold text-aula-cream">No encontramos categorías con esos filtros.</h3>
          <p class="mt-3 text-sm text-white/40">Prueba otro término o cambia el nivel disponible.</p>
        </div>

        <section v-if="selectedCategory" class="mt-14 border-t border-white/10 pt-10">
          <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-aula-green">Cursos de la categoría</p>
              <h2 class="mt-3 font-editorial text-4xl font-semibold text-aula-cream">{{ selectedTitle }}</h2>
            </div>

            <button
              type="button"
              class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange-soft"
              @click="selectCategory(null)"
            >
              Cerrar selección
            </button>
          </div>

          <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <CourseCard v-for="course in selectedCourses" :key="course.id" :course="course" />
          </div>
        </section>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PublicHeader from '../../components/layout/PublicHeader.vue'
import CourseCard from '../../components/courses/CourseCard.vue'
import { categories, courses } from '../../data/courses.js'

const route = useRoute()
const router = useRouter()

const query = ref('')
const level = ref('')
const sortBy = ref('default')
const initialCategory = categories.some((category) => category.id === route.query.categoria)
  ? route.query.categoria
  : null
const selectedCategory = ref(initialCategory)

const filteredCategories = computed(() => {
  const search = query.value.trim().toLocaleLowerCase('es')

  const result = categories.filter((category) => {
    const haystack = `${category.name} ${category.eyebrow} ${category.description}`.toLocaleLowerCase('es')
    const matchesSearch = !search || haystack.includes(search)
    const matchesLevel = !level.value || courses.some(
      (course) => course.categoryId === category.id && course.level === level.value,
    )

    return matchesSearch && matchesLevel
  })

  if (sortBy.value === 'name') {
    return [...result].sort((a, b) => a.name.localeCompare(b.name, 'es'))
  }

  if (sortBy.value === 'courses') {
    return [...result].sort((a, b) => categoryCourseCount(b.id) - categoryCourseCount(a.id))
  }

  return result
})

const selectedCourses = computed(() => {
  if (!selectedCategory.value) return []
  return courses.filter((course) => course.categoryId === selectedCategory.value)
})

const selectedTitle = computed(() => {
  return categories.find((item) => item.id === selectedCategory.value)?.name || ''
})

function categoryCourseCount(categoryId) {
  return courses.filter((course) => course.categoryId === categoryId).length
}

function levelsForCategory(categoryId) {
  return [...new Set(courses.filter((course) => course.categoryId === categoryId).map((course) => course.level))]
}

function selectCategory(id) {
  selectedCategory.value = selectedCategory.value === id ? null : id
  router.replace({ query: selectedCategory.value ? { categoria: selectedCategory.value } : {} })
}

function clearFilters() {
  query.value = ''
  level.value = ''
  sortBy.value = 'default'
}
</script>
