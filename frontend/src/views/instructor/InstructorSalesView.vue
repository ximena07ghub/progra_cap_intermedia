<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <div class="border-b border-white/10 pb-8">
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Reporte de ventas</p>
        <h1 class="mt-3 font-editorial text-5xl">Ingresos y movimientos.</h1>
      </div>

      <section class="mt-8 grid gap-4 sm:grid-cols-3">
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Ingresos totales</span><strong class="mt-3 block text-3xl text-aula-green">{{ formatCurrency(totalRevenue) }}</strong></article>
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Ventas</span><strong class="mt-3 block text-3xl">{{ filteredSales.length }}</strong></article>
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Ticket promedio</span><strong class="mt-3 block text-3xl">{{ formatCurrency(averageTicket) }}</strong></article>
      </section>

      <section class="mt-8 flex flex-col gap-4 border border-white/10 bg-white/[0.02] p-5 sm:flex-row">
        <select v-model="courseFilter" class="min-h-11 flex-1 border border-white/10 bg-aula-bg px-3 text-sm text-white">
          <option value="">Todos los cursos</option>
          <option v-for="course in instructorCourses" :key="course.slug" :value="course.slug">{{ course.title }}</option>
        </select>
        <input v-model="month" type="month" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" />
      </section>

      <section class="mt-8 overflow-x-auto border border-white/10">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-white/[0.04] text-[10px] uppercase tracking-[0.12em] text-white/40"><tr><th class="px-4 py-4">Venta</th><th class="px-4 py-4">Curso</th><th class="px-4 py-4">Estudiante</th><th class="px-4 py-4">Fecha</th><th class="px-4 py-4">Importe</th></tr></thead>
          <tbody>
            <tr v-for="sale in filteredSales" :key="sale.id" class="border-t border-white/10">
              <td class="px-4 py-4 text-white/45">{{ sale.id }}</td>
              <td class="px-4 py-4"><RouterLink :to="`/instructor/ventas/${sale.courseSlug}`" class="font-bold text-aula-orange">{{ sale.course }}</RouterLink></td>
              <td class="px-4 py-4 text-white/60">{{ sale.student }}</td>
              <td class="px-4 py-4 text-white/60">{{ formatDate(sale.date) }}</td>
              <td class="px-4 py-4 font-bold text-aula-green">{{ formatCurrency(sale.amount) }}</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses, salesTransactions } from '../../data/mockPortal.js'
import { formatCurrency, formatDate } from '../../utils/format.js'

const courseFilter = ref('')
const month = ref('')
const filteredSales = computed(() => salesTransactions.filter((sale) => {
  const matchesCourse = !courseFilter.value || sale.courseSlug === courseFilter.value
  const matchesMonth = !month.value || sale.date.startsWith(month.value)
  return matchesCourse && matchesMonth
}))
const totalRevenue = computed(() => filteredSales.value.reduce((sum, sale) => sum + sale.amount, 0))
const averageTicket = computed(() => filteredSales.value.length ? totalRevenue.value / filteredSales.value.length : 0)
</script>

