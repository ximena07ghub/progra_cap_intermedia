<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8 lg:py-16">
      <div class="flex flex-col gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Panel del instructor</p>
          <h1 class="mt-3 font-editorial text-5xl">Resumen de tu actividad.</h1>
        </div>
        <RouterLink to="/instructor/cursos/nuevo" class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Crear curso</RouterLink>
      </div>

      <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article v-for="stat in stats" :key="stat.label" class="border border-white/10 bg-white/[0.02] p-5">
          <span class="text-[10px] uppercase tracking-[0.12em] text-white/35">{{ stat.label }}</span>
          <strong class="mt-3 block text-3xl text-aula-cream">{{ stat.value }}</strong>
          <small class="mt-2 block text-white/30">{{ stat.note }}</small>
        </article>
      </section>

      <section class="mt-10 grid gap-8 lg:grid-cols-[1.2fr_.8fr]">
        <div>
          <div class="mb-4 flex items-center justify-between">
            <h2 class="font-editorial text-3xl">Mis cursos</h2>
            <RouterLink to="/instructor/cursos" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">Ver todos →</RouterLink>
          </div>
          <div class="border-t border-white/10">
            <article v-for="course in instructorCourses" :key="course.id" class="grid gap-4 border-b border-white/10 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
              <div>
                <strong class="block text-sm">{{ course.title }}</strong>
                <span class="mt-1 block text-xs text-white/35">{{ course.status }} · {{ course.students }} estudiantes · {{ course.rating || 'Sin' }} calificación</span>
              </div>
              <RouterLink :to="`/instructor/cursos/${course.slug}/editar`" class="text-xs font-bold text-aula-orange">Editar →</RouterLink>
            </article>
          </div>
        </div>

        <aside class="border border-white/10 bg-white/[0.02] p-5">
          <h2 class="font-editorial text-3xl">Ventas recientes</h2>
          <div class="mt-4 divide-y divide-white/10">
            <div v-for="sale in recentSales" :key="sale.id" class="py-4">
              <div class="flex items-start justify-between gap-4">
                <div><strong class="block text-sm">{{ sale.course }}</strong><span class="mt-1 block text-xs text-white/35">{{ sale.student }} · {{ formatDate(sale.date) }}</span></div>
                <strong class="text-sm text-aula-green">{{ formatCurrency(sale.amount) }}</strong>
              </div>
            </div>
          </div>
          <RouterLink to="/instructor/ventas" class="mt-5 inline-block text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">Abrir reporte de ventas →</RouterLink>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses, salesTransactions } from '../../data/mockPortal.js'
import { formatCurrency, formatDate } from '../../utils/format.js'

const totalRevenue = computed(() => salesTransactions.reduce((sum, sale) => sum + sale.amount, 0))
const totalStudents = computed(() => instructorCourses.reduce((sum, course) => sum + course.students, 0))
const recentSales = salesTransactions.slice(0, 3)
const stats = computed(() => [
  { label: 'Cursos', value: instructorCourses.length, note: 'Publicados y borradores' },
  { label: 'Estudiantes', value: totalStudents.value, note: 'Inscripciones acumuladas' },
  { label: 'Ingresos', value: formatCurrency(totalRevenue.value), note: 'Ventas mock registradas' },
  { label: 'Mensajes', value: 2, note: 'Conversaciones abiertas' },
])
</script>

