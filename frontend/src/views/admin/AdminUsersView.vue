<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="administrador" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <div class="border-b border-white/10 pb-8"><p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Administración</p><h1 class="mt-3 font-editorial text-5xl">Usuarios</h1></div>

      <div class="mt-8 flex flex-col gap-3 sm:flex-row">
        <input v-model="query" class="min-h-11 flex-1 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="Buscar por nombre o correo" />
        <select v-model="roleFilter" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white"><option value="">Todos los roles</option><option value="estudiante">Estudiante</option><option value="instructor">Instructor</option></select>
      </div>

      <section class="mt-8 border border-white/10">
        <article v-for="user in filteredUsers" :key="user.id" class="grid gap-4 border-b border-white/10 p-5 md:grid-cols-[1fr_auto] md:items-center">
          <div><strong class="block text-sm">{{ user.name }}</strong><span class="mt-1 block text-xs text-white/35">{{ user.email }} · {{ user.role }} · {{ user.reports }} reportes</span></div>
          <div class="flex items-center gap-3"><span class="text-xs" :class="user.status === 'Activo' ? 'text-aula-green' : 'text-[#f0907b]'">{{ user.status }}</span><button class="border border-white/15 px-3 py-2 text-xs font-bold" @click="toggleBlock(user)">{{ user.status === 'Activo' ? 'Bloquear' : 'Desbloquear' }}</button></div>
        </article>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { adminUsers } from '../../data/mockPortal.js'

const users = ref(structuredClone(adminUsers))
const query = ref('')
const roleFilter = ref('')
const filteredUsers = computed(() => {
  const text = query.value.trim().toLowerCase()
  return users.value.filter((user) => {
    const matchesText = !text || `${user.name} ${user.email}`.toLowerCase().includes(text)
    const matchesRole = !roleFilter.value || user.role === roleFilter.value
    return matchesText && matchesRole
  })
})
function toggleBlock(user) { user.status = user.status === 'Activo' ? 'Bloqueado' : 'Activo' }
</script>

