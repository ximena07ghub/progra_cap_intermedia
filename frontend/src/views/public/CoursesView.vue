<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="relative overflow-hidden border-b border-white/10">
        <div class="pointer-events-none absolute -right-48 -top-52 h-[520px] w-[520px] rounded-full bg-aula-orange/10 blur-3xl"></div>
        <div class="mx-auto grid max-w-aula gap-8 px-6 py-16 lg:grid-cols-[1fr_.75fr] lg:px-8 lg:py-20">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Catálogo AulaGo</p>
            <h1 class="mt-4 font-editorial text-5xl leading-[0.98] text-aula-cream sm:text-6xl lg:text-7xl">
              Encuentra tu próxima <em class="text-aula-orange">ruta de aprendizaje.</em>
            </h1>
          </div>
          <p class="self-end max-w-lg text-sm leading-7 text-white/45">
            Explora cursos por categoría, nivel o tema. Por ahora los datos viven en Vue; después puedes sustituirlos por tu API sin cambiar la estructura visual.
          </p>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-12 lg:px-8 lg:py-16">
        <form class="grid gap-4 border border-white/10 bg-aula-surface/60 p-5 md:grid-cols-[1.3fr_.8fr_.8fr_auto] md:items-end" @submit.prevent>
          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Buscar</span>
            <input
              v-model="query"
              type="search"
              placeholder="Ej. diseño, hábitos, Vue…"
              class="border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange"
            />
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Categoría</span>
            <select v-model="category" class="border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange">
              <option value="">Todas</option>
              <option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
          </label>

          <label class="grid gap-2">
            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/40">Nivel</span>
            <select v-model="level" class="border border-white/10 bg-aula-bg/70 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange">
              <option value="">Todos</option>
              <option value="Inicial">Inicial</option>
              <option value="Intermedio">Intermedio</option>
              <option value="Avanzado">Avanzado</option>
            </select>
          </label>

          <button
            type="button"
            class="min-h-[46px] bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg transition hover:-translate-y-0.5"
            @click="clearFilters"
          >
            Limpiar
          </button>
        </form>

        <div class="mt-12 flex flex-col gap-3 border-b border-white/10 pb-7 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Cursos activos</p>
            <h2 class="mt-2 font-editorial text-4xl text-aula-cream">Explora la colección</h2>
          </div>
          <span class="text-xs text-white/35">{{ filteredCourses.length }} curso{{ filteredCourses.length === 1 ? '' : 's' }}</span>
        </div>

        <div v-if="filteredCourses.length" class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <CourseCard v-for="course in filteredCourses" :key="course.id" :course="course" />
        </div>

        <div v-else class="mt-8 border border-white/10 bg-aula-surface/50 p-8">
          <h3 class="font-editorial text-3xl text-aula-cream">No encontramos cursos con esos filtros.</h3>
          <p class="mt-3 text-sm text-white/45">Cambia el nivel, la categoría o el término de búsqueda.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import PublicHeader from '../../components/layout/PublicHeader.vue'
import CourseCard from '../../components/courses/CourseCard.vue'
import { categories, courses } from '../../data/courses.js'

const query = ref('')
const category = ref('')
const level = ref('')

const filteredCourses = computed(() => {
  const search = query.value.trim().toLocaleLowerCase('es')

  return courses.filter((course) => {
    const matchesCategory = !category.value || course.categoryId === category.value
    const matchesLevel = !level.value || course.level === level.value
    const haystack = `${course.title} ${course.category} ${course.instructor} ${course.description}`.toLocaleLowerCase('es')
    const matchesSearch = !search || haystack.includes(search)
    return matchesCategory && matchesLevel && matchesSearch
  })
})

function clearFilters() {
  query.value = ''
  category.value = ''
  level.value = ''
}
</script>
