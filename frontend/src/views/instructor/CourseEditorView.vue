<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-4xl px-6 py-12 lg:px-8">
      <RouterLink to="/instructor/cursos" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Mis cursos</RouterLink>
      <p class="mt-8 font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">{{ editing ? 'Edición' : 'Nuevo curso' }}</p>
      <h1 class="mt-3 font-editorial text-5xl">{{ editing ? 'Editar curso' : 'Crear curso' }}</h1>

      <form class="mt-8 grid gap-6" @submit.prevent="save">
        <section class="grid gap-5 border border-white/10 bg-white/[0.02] p-6 sm:grid-cols-2">
          <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Título</span><input v-model="form.title" required class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
          <label class="grid gap-2"><span class="text-xs text-white/45">Categoría</span><select v-model="form.category" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white"><option>Bienestar</option><option>Diseño</option><option>IT & Software</option><option>Marketing</option></select></label>
          <label class="grid gap-2"><span class="text-xs text-white/45">Precio MXN</span><input v-model.number="form.price" type="number" min="0" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
          <label class="grid gap-2"><span class="text-xs text-white/45">Nivel</span><select v-model="form.level" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white"><option>Inicial</option><option>Intermedio</option><option>Avanzado</option></select></label>
          <label class="grid gap-2"><span class="text-xs text-white/45">Estado</span><select v-model="form.status" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white"><option>Borrador</option><option>Publicado</option></select></label>
          <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Descripción</span><textarea v-model="form.description" rows="5" class="border border-white/10 bg-aula-bg p-3 text-sm leading-7 text-white"></textarea></label>
        </section>

        <p v-if="saved" class="border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">Cambios guardados en el prototipo.</p>

        <div class="flex flex-wrap gap-3">
          <button type="submit" class="min-h-11 bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Guardar</button>
          <RouterLink v-if="editing" :to="`/instructor/cursos/${route.params.slug}/contenido`" class="inline-flex min-h-11 items-center border border-white/15 px-5 text-xs font-bold uppercase tracking-[0.08em]">Editar niveles y contenido</RouterLink>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { instructorCourses } from '../../data/mockPortal.js'

const route = useRoute()
const editing = computed(() => Boolean(route.params.slug))
const existing = instructorCourses.find((item) => item.slug === route.params.slug)
const form = reactive({
  title: existing?.title || '',
  category: existing?.category || 'Bienestar',
  price: existing?.price || 0,
  level: 'Inicial',
  status: existing?.status || 'Borrador',
  description: existing ? `Descripción editable de ${existing.title}.` : '',
})
const saved = ref(false)

function save() {
  saved.value = true
}
</script>

