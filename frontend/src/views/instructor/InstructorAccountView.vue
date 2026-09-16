<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="instructor" />

    <main class="mx-auto max-w-3xl px-6 py-12 lg:px-8">
      <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Perfil profesional</p>
      <h1 class="mt-3 font-editorial text-5xl">Mi cuenta</h1>

      <form class="mt-8 grid gap-5 border border-white/10 bg-white/[0.02] p-6 sm:grid-cols-2" @submit.prevent="saved = true">
        <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Nombre</span><input v-model="form.name" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
        <label class="grid gap-2"><span class="text-xs text-white/45">Correo</span><input v-model="form.email" type="email" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
        <label class="grid gap-2"><span class="text-xs text-white/45">Especialidad</span><input v-model="form.specialty" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
        <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Biografía</span><textarea v-model="form.bio" rows="5" class="border border-white/10 bg-aula-bg p-3 text-sm leading-7 text-white"></textarea></label>
        <p v-if="saved" class="sm:col-span-2 border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">Perfil actualizado en el prototipo.</p>
        <button class="min-h-11 bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg sm:w-fit">Guardar cambios</button>
      </form>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { useSessionStore } from '../../stores/session.js'

const session = useSessionStore()
const saved = ref(false)
const form = reactive({
  name: session.user?.name || 'Psic. Ana Solís',
  email: session.user?.email || 'ana@aulago.mx',
  specialty: 'Psicología y hábitos',
  bio: 'Acompaño procesos de aprendizaje y diseño cursos enfocados en hábitos sostenibles.',
})
</script>

