<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <WorkspaceHeader mode="administrador" />

    <main class="mx-auto max-w-aula px-6 py-12 lg:px-8">
      <div class="border-b border-white/10 pb-8"><p class="font-mono text-[11px] uppercase tracking-[0.16em] text-aula-green">Administración</p><h1 class="mt-3 font-editorial text-5xl">Categorías</h1></div>

      <form class="mt-8 grid gap-3 border border-white/10 bg-white/[0.02] p-5 sm:grid-cols-[1fr_1fr_auto]" @submit.prevent="saveCategory">
        <input v-model="form.name" required class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="Nombre" />
        <input v-model="form.slug" required class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="slug" />
        <button class="bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">{{ editingId ? 'Actualizar' : 'Crear' }}</button>
      </form>

      <section class="mt-8 border border-white/10">
        <article v-for="category in categories" :key="category.id" class="grid gap-4 border-b border-white/10 p-5 sm:grid-cols-[1fr_auto] sm:items-center">
          <div><strong class="block text-sm">{{ category.name }}</strong><span class="mt-1 block text-xs text-white/35">/{{ category.slug }} · {{ category.courses }} cursos · {{ category.active ? 'Activa' : 'Inactiva' }}</span></div>
          <div class="flex flex-wrap gap-2">
            <button class="border border-white/15 px-3 py-2 text-xs" @click="editCategory(category)">Editar</button>
            <button class="border border-aula-green/30 px-3 py-2 text-xs text-aula-green" @click="category.active = !category.active">{{ category.active ? 'Desactivar' : 'Activar' }}</button>
            <button class="border border-[#f0907b]/30 px-3 py-2 text-xs text-[#f0907b]" @click="removeCategory(category.id)">Eliminar</button>
          </div>
        </article>
      </section>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import { adminCategories } from '../../data/mockPortal.js'

const categories = ref(structuredClone(adminCategories))
const editingId = ref(null)
const form = reactive({ name: '', slug: '' })

function reset() { editingId.value = null; form.name = ''; form.slug = '' }
function editCategory(category) { editingId.value = category.id; form.name = category.name; form.slug = category.slug }
function saveCategory() {
  if (editingId.value) {
    const target = categories.value.find((item) => item.id === editingId.value)
    if (target) Object.assign(target, { name: form.name, slug: form.slug })
  } else {
    categories.value.push({ id: Date.now(), name: form.name, slug: form.slug, courses: 0, active: true })
  }
  reset()
}
function removeCategory(id) { categories.value = categories.value.filter((item) => item.id !== id) }
</script>

