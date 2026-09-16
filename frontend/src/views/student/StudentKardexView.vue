<template>
  <div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main class="mx-auto w-full max-w-aula flex-1 px-6 py-12 lg:px-8 lg:py-16">
      <div class="flex flex-col gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Historial académico</p>
          <h1 class="mt-3 font-editorial text-5xl">Kardex</h1>
          <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">Consulta cursos terminados, calificación, fecha, importe y certificado.</p>
        </div>
        <strong class="text-sm text-aula-orange">{{ filteredRecords.length }} resultados</strong>
      </div>

      <section class="mt-8 grid gap-4 border border-white/10 bg-white/[0.02] p-5 md:grid-cols-3">
        <label class="grid gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/35">Buscar</span>
          <input v-model="query" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange" placeholder="Curso o instructor" />
        </label>
        <label class="grid gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/35">Categoría</span>
          <select v-model="category" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
            <option value="">Todas</option>
            <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
          </select>
        </label>
        <label class="grid gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/35">Año</span>
          <select v-model="year" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
            <option value="">Todos</option>
            <option v-for="item in years" :key="item" :value="item">{{ item }}</option>
          </select>
        </label>
      </section>

      <section class="mt-8 overflow-x-auto border border-white/10">
        <table class="min-w-full border-collapse text-left text-sm">
          <thead class="bg-white/[0.04] text-[10px] uppercase tracking-[0.12em] text-white/40">
            <tr>
              <th class="px-4 py-4">Curso</th>
              <th class="px-4 py-4">Finalización</th>
              <th class="px-4 py-4">Calificación</th>
              <th class="px-4 py-4">Importe</th>
              <th class="px-4 py-4">Certificado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in filteredRecords" :key="record.id" class="border-t border-white/10">
              <td class="px-4 py-5">
                <strong class="block text-aula-cream">{{ record.course }}</strong>
                <span class="mt-1 block text-xs text-white/35">{{ record.category }} · {{ record.instructor }}</span>
              </td>
              <td class="px-4 py-5 text-white/60">{{ formatDate(record.finishedAt) }}</td>
              <td class="px-4 py-5 font-bold text-aula-green">{{ record.grade }}/100</td>
              <td class="px-4 py-5 text-white/60">{{ formatCurrency(record.amount) }}</td>
              <td class="px-4 py-5">
                <RouterLink :to="`/estudiante/certificado/${record.slug}`" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">Ver certificado →</RouterLink>
              </td>
            </tr>
            <tr v-if="!filteredRecords.length">
              <td colspan="5" class="px-4 py-12 text-center text-sm text-white/35">No hay cursos que coincidan con los filtros.</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>

    <PublicFooter />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import StudentHeader from '../../components/layout/StudentHeader.vue'
import PublicFooter from '../../components/layout/PublicFooter.vue'
import { kardexRecords } from '../../data/mockPortal.js'
import { formatCurrency, formatDate } from '../../utils/format.js'

const query = ref('')
const category = ref('')
const year = ref('')

const categories = [...new Set(kardexRecords.map((item) => item.category))]
const years = [...new Set(kardexRecords.map((item) => item.finishedAt.slice(0, 4)))].sort().reverse()

const filteredRecords = computed(() => {
  const text = query.value.trim().toLowerCase()

  return kardexRecords.filter((record) => {
    const matchesText = !text || `${record.course} ${record.instructor}`.toLowerCase().includes(text)
    const matchesCategory = !category.value || record.category === category.value
    const matchesYear = !year.value || record.finishedAt.startsWith(year.value)
    return matchesText && matchesCategory && matchesYear
  })
})
</script>

