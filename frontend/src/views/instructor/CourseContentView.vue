<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <RouterLink to="/instructor/cursos" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Mis cursos</RouterLink>
      <p class="mt-8 font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Contenido por niveles</p>
      <h1 class="mt-3 font-editorial text-5xl">{{ course.title }}</h1>
      <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45">Cada nivel puede contener video, PDF o enlace. En el backend real estos registros irán a tu tabla de contenidos.</p>

      <section class="mt-8 grid gap-5">
        <article v-for="(level, levelIndex) in levels" :key="level.id" class="border border-white/10 bg-white/[0.02] p-5">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div><span class="text-[10px] uppercase tracking-[0.12em] text-aula-green">Nivel {{ levelIndex + 1 }}</span><h2 class="mt-1 font-editorial text-3xl">{{ level.title }}</h2></div>
            <button type="button" class="text-xs font-bold text-[#f0907b]" @click="removeLevel(level.id)">Eliminar nivel</button>
          </div>

          <div class="mt-5 grid gap-3">
            <div v-for="item in level.items" :key="item.id" class="grid gap-3 border-t border-white/10 pt-4 sm:grid-cols-[100px_1fr_auto] sm:items-center">
              <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-aula-orange">{{ item.type }}</span>
              <div><strong class="block text-sm">{{ item.title }}</strong><span class="mt-1 block truncate text-xs text-white/30">{{ item.url }}</span></div>
              <button type="button" class="text-xs text-white/45 hover:text-[#f0907b]" @click="removeItem(level, item.id)">Quitar</button>
            </div>
          </div>

          <form class="mt-5 grid gap-3 border-t border-white/10 pt-5 sm:grid-cols-[120px_1fr_1fr_auto]" @submit.prevent="addItem(level)">
            <select v-model="drafts[level.id].type" class="min-h-10 border border-white/10 bg-aula-bg px-2 text-xs text-white"><option>Video</option><option>PDF</option><option>Link</option></select>
            <input v-model="drafts[level.id].title" required class="min-h-10 border border-white/10 bg-aula-bg px-3 text-xs text-white" placeholder="Título del recurso" />
            <input v-model="drafts[level.id].url" required class="min-h-10 border border-white/10 bg-aula-bg px-3 text-xs text-white" placeholder="URL o ruta del archivo" />
            <button class="bg-aula-green px-4 text-xs font-bold text-aula-bg">Agregar</button>
          </form>
        </article>
      </section>

      <button type="button" class="mt-6 border border-aula-orange/50 px-5 py-3 text-xs font-bold uppercase tracking-[0.08em] text-aula-orange" @click="addLevel">+ Agregar nivel</button>
    </main>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses } from '../../data/mockPortal.js'

const route = useRoute()
const course = computed(() => instructorCourses.find((item) => item.slug === route.params.slug) || instructorCourses[0])
const levels = ref([
  { id: 1, title: 'Fundamentos', items: [{ id: 11, type: 'Video', title: 'Introducción', url: 'https://video.example/intro' }, { id: 12, type: 'PDF', title: 'Guía de trabajo', url: '/docs/guia-nivel-1.pdf' }] },
  { id: 2, title: 'Práctica guiada', items: [{ id: 21, type: 'Link', title: 'Actividad externa', url: 'https://example.com/actividad' }] },
])
const drafts = reactive({})

function ensureDraft(levelId) {
  if (!drafts[levelId]) drafts[levelId] = { type: 'Video', title: '', url: '' }
}
levels.value.forEach((level) => ensureDraft(level.id))

function addLevel() {
  const id = Date.now()
  levels.value.push({ id, title: `Nuevo nivel ${levels.value.length + 1}`, items: [] })
  ensureDraft(id)
}
function removeLevel(id) { levels.value = levels.value.filter((level) => level.id !== id) }
function addItem(level) {
  const draft = drafts[level.id]
  level.items.push({ id: Date.now(), ...draft })
  drafts[level.id] = { type: 'Video', title: '', url: '' }
}
function removeItem(level, itemId) { level.items = level.items.filter((item) => item.id !== itemId) }
</script>

