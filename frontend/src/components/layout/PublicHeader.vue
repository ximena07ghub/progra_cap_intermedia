<template>
  <header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-aula items-center gap-5 px-6 py-4 lg:px-8">
      <RouterLink to="/" class="shrink-0" aria-label="Ir al inicio de AulaGo">
        <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
      </RouterLink>

      <nav class="hidden items-center gap-6 md:flex" aria-label="Navegación principal">
        <RouterLink class="text-sm font-bold text-white/70 transition hover:text-white" to="/">
          Inicio
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/70 transition hover:text-white" to="/cursos">
          Cursos
        </RouterLink>
        <RouterLink class="text-sm font-bold text-white/70 transition hover:text-white" to="/categorias">
          Categorías
        </RouterLink>
      </nav>

      <form class="ml-auto hidden min-w-0 max-w-sm flex-1 md:flex" role="search" @submit.prevent="submitSearch">
        <input
          v-model="query"
          type="search"
          placeholder="Buscar un curso"
          class="min-w-0 flex-1 border-b border-white/20 bg-transparent px-1 py-2 text-sm text-white outline-none placeholder:text-white/30 focus:border-aula-orange"
        />
        <button type="submit" class="border-b border-white/20 bg-transparent px-3 text-aula-orange" aria-label="Buscar">
          ↗
        </button>
      </form>

      <div class="hidden items-center gap-3 md:flex">
        <template v-if="authenticated">
          <RouterLink to="/estudiante" class="text-sm font-bold text-white/70 transition hover:text-white">
            Mi espacio
          </RouterLink>
          <RouterLink
            to="/estudiante/cuenta"
            class="inline-flex min-h-10 items-center justify-center bg-aula-cream px-4 text-xs font-extrabold text-aula-bg transition hover:-translate-y-0.5"
          >
            Mi cuenta
          </RouterLink>
        </template>
        <template v-else>
          <RouterLink to="/login" class="text-sm font-bold text-white/70 transition hover:text-white">
            Iniciar sesión
          </RouterLink>
          <RouterLink
            to="/registro"
            class="inline-flex min-h-10 items-center justify-center bg-aula-cream px-4 text-xs font-extrabold text-aula-bg transition hover:-translate-y-0.5"
          >
            Registrarse
          </RouterLink>
        </template>
      </div>

      <button
        type="button"
        class="ml-auto grid h-10 w-10 place-items-center border border-white/15 bg-transparent text-white md:hidden"
        :aria-expanded="menuOpen"
        aria-label="Abrir menú"
        @click="menuOpen = !menuOpen"
      >
        <span class="text-lg">{{ menuOpen ? '×' : '☰' }}</span>
      </button>
    </div>

    <div v-if="menuOpen" class="border-t border-white/10 px-6 py-5 md:hidden">
      <nav class="mx-auto grid max-w-aula gap-4" aria-label="Navegación móvil">
        <RouterLink to="/" class="text-sm font-bold text-white/80" @click="menuOpen = false">Inicio</RouterLink>
        <RouterLink to="/cursos" class="text-sm font-bold text-white/80" @click="menuOpen = false">Cursos</RouterLink>
        <RouterLink to="/categorias" class="text-sm font-bold text-white/80" @click="menuOpen = false">Categorías</RouterLink>
        <RouterLink v-if="authenticated" to="/estudiante" class="text-sm font-bold text-aula-orange" @click="menuOpen = false">Mi espacio</RouterLink>
        <RouterLink v-else to="/login" class="text-sm font-bold text-aula-orange" @click="menuOpen = false">Iniciar sesión</RouterLink>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logoUrl from '../../assets/images/aula-logo.svg'
import { isDemoAuthenticated } from '../../utils/demoSession.js'

const router = useRouter()
const query = ref('')
const menuOpen = ref(false)
const authenticated = ref(isDemoAuthenticated())

function submitSearch() {
  const q = query.value.trim()
  router.push({ path: '/buscar', query: q ? { q } : {} })
}
</script>
