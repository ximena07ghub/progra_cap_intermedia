<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="administrador" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Administración</p>
      <h1 class="mt-3 font-editorial text-5xl">Estado general de AulaGo.</h1>

      <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article v-for="stat in stats" :key="stat.label" class="border border-white/10 bg-white/[0.02] p-5">
          <span class="text-[10px] uppercase tracking-[0.12em] text-white/35">{{ stat.label }}</span>
          <strong class="mt-3 block text-3xl">{{ stat.value }}</strong>
          <small class="mt-2 block text-white/30">{{ stat.note }}</small>
        </article>
      </section>

      <section class="mt-10 grid gap-5 md:grid-cols-3">
        <RouterLink to="/admin/categorias" class="border border-white/10 p-6 transition hover:border-aula-green/40"><span class="text-[10px] uppercase tracking-[0.12em] text-aula-green">01</span><h2 class="mt-3 font-editorial text-3xl">Categorías</h2><p class="mt-3 text-sm leading-6 text-white/40">Crear, editar, activar o eliminar categorías.</p></RouterLink>
        <RouterLink to="/admin/usuarios" class="border border-white/10 p-6 transition hover:border-aula-green/40"><span class="text-[10px] uppercase tracking-[0.12em] text-aula-green">02</span><h2 class="mt-3 font-editorial text-3xl">Usuarios</h2><p class="mt-3 text-sm leading-6 text-white/40">Consultar reportes y bloquear cuentas.</p></RouterLink>
        <RouterLink to="/admin/comentarios" class="border border-white/10 p-6 transition hover:border-aula-green/40"><span class="text-[10px] uppercase tracking-[0.12em] text-aula-green">03</span><h2 class="mt-3 font-editorial text-3xl">Comentarios</h2><p class="mt-3 text-sm leading-6 text-white/40">Moderar comentarios visibles o reportados.</p></RouterLink>
      </section>
    </main>
  </div>
</template>

<script setup>
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { adminCategories, adminComments, adminUsers } from '../../data/mockPortal.js'

const stats = [
  { label: 'Usuarios', value: adminUsers.length, note: 'Cuentas mock' },
  { label: 'Categorías', value: adminCategories.length, note: 'Categorías activas' },
  { label: 'Comentarios', value: adminComments.length, note: 'Registrados' },
  { label: 'Reportes', value: adminUsers.reduce((sum, user) => sum + user.reports, 0), note: 'Pendientes de revisar' },
]
</script>

