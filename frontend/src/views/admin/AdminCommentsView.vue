<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="administrador" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <div class="border-b border-white/10 pb-8"><p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Moderación</p><h1 class="mt-3 font-editorial text-5xl">Comentarios</h1></div>

      <div class="mt-8 flex gap-3">
        <button v-for="filter in ['Todos', 'Visible', 'Reportado', 'Oculto']" :key="filter" class="border px-3 py-2 text-xs font-bold" :class="statusFilter === filter ? 'border-aula-orange text-aula-orange' : 'border-white/15 text-white/45'" @click="statusFilter = filter">{{ filter }}</button>
      </div>

      <section class="mt-8 grid gap-4">
        <article v-for="comment in filteredComments" :key="comment.id" class="border border-white/10 bg-white/[0.02] p-5">
          <div class="flex flex-wrap items-start justify-between gap-4"><div><strong class="block text-sm">{{ comment.user }} · {{ '★'.repeat(comment.rating) }}</strong><span class="mt-1 block text-xs text-white/35">{{ comment.course }} · {{ formatDate(comment.date) }} · {{ comment.status }}</span></div><div class="flex gap-2"><button class="border border-aula-green/30 px-3 py-2 text-xs text-aula-green" @click="comment.status = 'Visible'">Mostrar</button><button class="border border-white/15 px-3 py-2 text-xs" @click="comment.status = 'Oculto'">Ocultar</button><button class="border border-[#f0907b]/30 px-3 py-2 text-xs text-[#f0907b]" @click="removeComment(comment.id)">Eliminar</button></div></div>
          <p class="mt-4 text-sm leading-7 text-white/55">{{ comment.text }}</p>
        </article>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { adminComments } from '../../data/mockPortal.js'
import { formatDate } from '../../utils/format.js'

const comments = ref(structuredClone(adminComments))
const statusFilter = ref('Todos')
const filteredComments = computed(() => statusFilter.value === 'Todos' ? comments.value : comments.value.filter((item) => item.status === statusFilter.value))
function removeComment(id) { comments.value = comments.value.filter((item) => item.id !== id) }
</script>

