<template>
  <header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-aula items-center gap-5 px-6 py-4 lg:px-8">
      <RouterLink to="/estudiante" class="shrink-0" aria-label="Ir a mi espacio AulaGo">
        <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
      </RouterLink>

      <nav class="hidden items-center gap-6 lg:flex" aria-label="Navegación del estudiante">
        <RouterLink class="text-sm font-bold text-white/65 transition hover:text-white" to="/estudiante">
          Mi espacio
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/65 transition hover:text-white" to="/estudiante#mis-cursos">
          Mis cursos
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/65 transition hover:text-white" to="/estudiante/kardex">
          Kardex
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/65 transition hover:text-white" to="/estudiante/mensajes">
          Mensajes
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/65 transition hover:text-white" to="/categorias">
          Categorías
        </RouterLink>
      </nav>

      <form class="ml-auto hidden max-w-xs flex-1 md:flex" role="search" @submit.prevent="submitSearch">
        <input
          v-model="query"
          type="search"
          placeholder="Buscar cursos"
          class="min-w-0 flex-1 border-b border-white/20 bg-transparent py-2 text-sm text-white outline-none placeholder:text-white/30 focus:border-aula-orange"
        />
        <button class="border-b border-white/20 bg-transparent px-3 text-aula-orange" type="submit" aria-label="Buscar">↗</button>
      </form>

      <div class="flex items-center gap-3">
        <RouterLink
          to="/estudiante/cuenta"
          class="hidden items-center gap-3 border-l border-white/10 pl-4 sm:flex"
        >
          <span class="grid h-9 w-9 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-white">E</span>
          <span class="text-sm font-bold text-white/80">Mi cuenta</span>
        </RouterLink>

        <button
          type="button"
          class="hidden bg-transparent text-xs font-bold text-white/45 transition hover:text-aula-orange sm:block"
          @click="logout"
        >
          Salir
        </button>

        <button
          type="button"
          class="grid h-10 w-10 place-items-center border border-white/15 bg-transparent text-white lg:hidden"
          :aria-expanded="menuOpen"
          aria-label="Abrir menú"
          @click="menuOpen = !menuOpen"
        >
          {{ menuOpen ? '×' : '☰' }}
        </button>
      </div>
    </div>

    <div v-if="menuOpen" class="border-t border-white/10 px-6 py-5 lg:hidden">
      <nav class="mx-auto grid max-w-aula gap-4">
        <RouterLink to="/estudiante" class="text-sm font-bold text-white/80" @click="menuOpen = false">Mi espacio</RouterLink>
        <RouterLink to="/estudiante/kardex" class="text-sm font-bold text-white/80" @click="menuOpen = false">Kardex</RouterLink>
        <RouterLink to="/estudiante/mensajes" class="text-sm font-bold text-white/80" @click="menuOpen = false">Mensajes</RouterLink>
        <RouterLink to="/categorias" class="text-sm font-bold text-white/80" @click="menuOpen = false">Categorías</RouterLink>
        <RouterLink to="/estudiante/cuenta" class="text-sm font-bold text-white/80" @click="menuOpen = false">Mi cuenta</RouterLink>
        <button type="button" class="w-fit bg-transparent text-sm font-bold text-aula-orange" @click="logout">Cerrar sesión</button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logoUrl from '../../assets/images/aula-logo.svg'
import { useSessionStore } from '../../stores/session.js'

const router = useRouter()
const query = ref('')
const menuOpen = ref(false)
const session = useSessionStore()

function submitSearch() {
  const q = query.value.trim()
  router.push({ path: '/buscar', query: q ? { q } : {} })
}

function logout() {
  session.logout()
  router.push('/')
}
</script>

