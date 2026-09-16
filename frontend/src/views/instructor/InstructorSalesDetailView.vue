<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <RouterLink to="/instructor/ventas" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Todas las ventas</RouterLink>
      <p class="mt-8 font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Detalle por curso</p>
      <h1 class="mt-3 font-editorial text-5xl">{{ course?.title || 'Curso' }}</h1>

      <section class="mt-8 grid gap-4 sm:grid-cols-3">
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Ventas</span><strong class="mt-3 block text-3xl">{{ sales.length }}</strong></article>
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Ingresos</span><strong class="mt-3 block text-3xl text-aula-green">{{ formatCurrency(total) }}</strong></article>
        <article class="border border-white/10 p-5"><span class="text-[10px] uppercase tracking-[0.12em] text-white/35">Precio actual</span><strong class="mt-3 block text-3xl">{{ formatCurrency(course?.price || 0) }}</strong></article>
      </section>

      <section class="mt-8 border border-white/10">
        <article v-for="sale in sales" :key="sale.id" class="grid gap-3 border-b border-white/10 p-5 sm:grid-cols-[110px_1fr_auto] sm:items-center">
          <span class="text-xs text-white/35">{{ sale.id }}</span>
          <div><strong class="block text-sm">{{ sale.student }}</strong><span class="mt-1 block text-xs text-white/35">{{ formatDate(sale.date) }}</span></div>
          <strong class="text-aula-green">{{ formatCurrency(sale.amount) }}</strong>
        </article>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses, salesTransactions } from '../../data/mockPortal.js'
import { formatCurrency, formatDate } from '../../utils/format.js'

const route = useRoute()
const course = computed(() => instructorCourses.find((item) => item.slug === route.params.slug))
const sales = computed(() => salesTransactions.filter((sale) => sale.courseSlug === route.params.slug))
const total = computed(() => sales.value.reduce((sum, sale) => sum + sale.amount, 0))
</script>

