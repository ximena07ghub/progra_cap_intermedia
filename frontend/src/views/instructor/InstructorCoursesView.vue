<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <div class="flex flex-col gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
        <div><p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Catálogo del instructor</p><h1 class="mt-3 font-editorial text-5xl">Mis cursos</h1></div>
        <RouterLink to="/instructor/cursos/nuevo" class="inline-flex min-h-11 items-center justify-center bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Nuevo curso</RouterLink>
      </div>

      <section class="mt-8 grid gap-5">
        <article v-for="course in instructorCourses" :key="course.id" class="grid gap-5 border border-white/10 bg-white/[0.02] p-5 md:grid-cols-[1fr_auto] md:items-center">
          <div>
            <div class="flex flex-wrap items-center gap-2"><span class="border border-white/10 px-2 py-1 text-[10px] uppercase tracking-[0.1em] text-white/45">{{ course.status }}</span><span class="text-[10px] uppercase tracking-[0.1em] text-aula-green">{{ course.category }}</span></div>
            <h2 class="mt-3 font-editorial text-3xl">{{ course.title }}</h2>
            <p class="mt-2 text-xs text-white/35">{{ course.students }} estudiantes · {{ formatCurrency(course.price) }} · Actualizado {{ formatDate(course.updatedAt) }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <RouterLink :to="`/instructor/cursos/${course.slug}/editar`" class="border border-white/15 px-4 py-3 text-xs font-bold">Editar</RouterLink>
            <RouterLink :to="`/instructor/cursos/${course.slug}/contenido`" class="border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-xs font-bold text-aula-green">Contenido</RouterLink>
            <RouterLink :to="`/instructor/ventas/${course.slug}`" class="bg-aula-orange px-4 py-3 text-xs font-bold text-aula-bg">Ventas</RouterLink>
          </div>
        </article>
      </section>
    </main>
  </div>
</template>

<script setup>
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses } from '../../data/mockPortal.js'
import { formatCurrency, formatDate } from '../../utils/format.js'
</script>

