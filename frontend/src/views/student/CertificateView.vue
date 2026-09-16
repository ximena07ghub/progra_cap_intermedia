<template>
  <div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main class="mx-auto w-full max-w-aula flex-1 px-6 py-12 lg:px-8">
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4 print:hidden">
        <RouterLink to="/estudiante/kardex" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Volver al kardex</RouterLink>
        <button type="button" class="border border-white/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.08em]" @click="printCertificate">Imprimir / Guardar PDF</button>
      </div>

      <section class="mx-auto max-w-4xl border border-aula-orange/40 bg-aula-cream p-8 text-aula-bg shadow-floating sm:p-14">
        <div class="border border-aula-bg/20 p-7 text-center sm:p-12">
          <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.22em] text-aula-green-dark">AulaGo · Certificado de finalización</p>
          <h1 class="mt-10 font-editorial text-5xl sm:text-6xl">Constancia de aprendizaje</h1>
          <p class="mt-8 text-sm text-aula-bg/60">Se certifica que</p>
          <strong class="mt-3 block font-editorial text-4xl">{{ studentName }}</strong>
          <p class="mx-auto mt-8 max-w-2xl text-base leading-8 text-aula-bg/65">ha completado satisfactoriamente el curso</p>
          <strong class="mt-2 block text-2xl">{{ course.title }}</strong>

          <div class="mt-12 grid gap-6 border-t border-aula-bg/15 pt-8 sm:grid-cols-3">
            <div><span class="block text-[10px] uppercase tracking-[0.12em] text-aula-bg/45">Fecha de terminación</span><strong class="mt-2 block text-sm">{{ completionDate }}</strong></div>
            <div><span class="block text-[10px] uppercase tracking-[0.12em] text-aula-bg/45">Instructor</span><strong class="mt-2 block text-sm">{{ course.instructor }}</strong></div>
            <div><span class="block text-[10px] uppercase tracking-[0.12em] text-aula-bg/45">Folio</span><strong class="mt-2 block text-sm">{{ certificateId }}</strong></div>
          </div>
        </div>
      </section>
    </main>

    <PublicFooter class="print:hidden" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import StudentHeader from '../../components/layout/StudentHeader.vue'
import PublicFooter from '../../components/layout/PublicFooter.vue'
import { courses } from '../../data/courses.js'
import { kardexRecords } from '../../data/mockPortal.js'
import { useSessionStore } from '../../stores/session.js'
import { formatDate } from '../../utils/format.js'

const route = useRoute()
const session = useSessionStore()
const course = computed(() => courses.find((item) => item.slug === route.params.slug) || courses[0])
const record = computed(() => kardexRecords.find((item) => item.slug === course.value.slug))
const studentName = computed(() => session.user?.name || 'Estudiante AulaGo')
const completionDate = computed(() => formatDate(record.value?.finishedAt || new Date().toISOString().slice(0, 10)))
const certificateId = computed(() => record.value?.certificateId || 'AG-DEMO-0000')

function printCertificate() {
  window.print()
}
</script>

