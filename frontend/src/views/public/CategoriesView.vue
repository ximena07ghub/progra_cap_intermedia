<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <section class="border-b border-white/10">
        <div class="mx-auto grid max-w-aula gap-8 px-6 py-16 lg:grid-cols-[1fr_.8fr] lg:px-8 lg:py-20">
          <div>
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Explora por tema</p>
            <h1 class="mt-4 font-editorial text-5xl leading-[0.98] text-aula-cream sm:text-6xl">
              Encuentra una categoría que despierte tu curiosidad.
            </h1>
          </div>
          <p class="self-end text-sm leading-7 text-white/45">
            Esta sección mantiene el mismo contenido estés dentro o fuera de sesión. Si ya ingresaste, puedes volver a tu espacio desde el encabezado sin perder la navegación pública.
          </p>
        </div>
      </section>

      <section class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
        <div class="grid gap-4 md:grid-cols-2">
          <button
            v-for="category in categories"
            :key="category.id"
            type="button"
            class="group border p-6 text-left transition sm:p-7"
            :class="selectedCategory === category.id ? 'border-aula-orange bg-aula-orange/10' : 'border-white/10 bg-aula-surface/60 hover:border-white/25'"
            @click="selectCategory(category.id)"
          >
            <div class="flex items-start justify-between gap-4">
              <div>
                <span class="font-mono text-[10px] text-aula-orange">{{ category.symbol }}</span>
                <p class="mt-5 text-[10px] font-bold uppercase tracking-[0.12em] text-aula-green">{{ category.eyebrow }}</p>
                <h2 class="mt-2 font-editorial text-3xl text-aula-cream">{{ category.name }}</h2>
              </div>
              <span class="text-xl text-white/25 transition group-hover:text-aula-orange">↗</span>
            </div>
            <p class="mt-4 max-w-xl text-sm leading-6 text-white/45">{{ category.description }}</p>
          </button>
        </div>

        <div class="mt-16 border-t border-white/10 pt-10">
          <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Cursos disponibles</p>
              <h2 class="mt-3 font-editorial text-4xl text-aula-cream">{{ selectedTitle }}</h2>
            </div>
            <button v-if="selectedCategory" type="button" class="bg-transparent text-xs font-bold uppercase tracking-[0.08em] text-aula-orange" @click="selectCategory(null)">
              Ver todos
            </button>
          </div>

          <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <CourseCard v-for="course in filteredCourses" :key="course.id" :course="course" />
          </div>
        </div>
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
const initialCategory = categories.some((category) => category.id === route.query.categoria) ? route.query.categoria : null
const selectedCategory = ref(initialCategory)

const filteredCourses = computed(() => {
  if (!selectedCategory.value) return courses
  return courses.filter((course) => course.categoryId === selectedCategory.value)
})

const selectedTitle = computed(() => {
  const category = categories.find((item) => item.id === selectedCategory.value)
  return category ? category.name : 'Todos los cursos'
})

function selectCategory(id) {
  selectedCategory.value = id
  router.replace({ query: id ? { categoria: id } : {} })
}
</script>
