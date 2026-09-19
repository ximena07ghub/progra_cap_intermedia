<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import logoUrl from '../../assets/images/aula-logo.svg'
import { useSessionStore } from '../../stores/session.js'

const router = useRouter()
const query = ref('')
const menuOpen = ref(false)
const session = useSessionStore()
const { authenticated } = storeToRefs(session)

function submitSearch() {
  const q = query.value.trim()
  router.push({ path: '/buscar', query: q ? { q } : {} })
}
</script>

<template>
  <header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/90 text-aula-cream backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center gap-5 px-6 py-4 lg:px-8">
      <RouterLink to="/" class="shrink-0" aria-label="Ir al inicio de AulaGo">
        <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
      </RouterLink>

      <nav class="hidden items-center gap-6 md:flex" aria-label="Navegación principal">
        <RouterLink to="/" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Inicio</RouterLink>
        <RouterLink to="/cursos" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Cursos</RouterLink>
        <RouterLink to="/categorias" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Categorías</RouterLink>
      </nav>

      <form class="ml-auto hidden min-w-0 max-w-sm flex-1 md:flex" role="search" @submit.prevent="submitSearch">
        <input
          v-model="query"
          type="search"
          placeholder="Buscar un curso"
          class="min-w-0 flex-1 border-b border-white/15 bg-transparent px-1 py-2 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-aula-orange/60"
        />
        <button type="submit" class="border-b border-white/15 bg-transparent px-3 text-aula-orange-soft transition hover:text-aula-cream" aria-label="Buscar">↗</button>
      </form>

      <div class="hidden items-center gap-3 md:flex">
        <template v-if="authenticated">
          <RouterLink :to="session.homeRoute" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Mi espacio</RouterLink>
          <RouterLink
            :to="session.accountRoute"
            class="inline-flex min-h-10 items-center justify-center rounded-full border border-aula-orange/35 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:-translate-y-0.5 hover:bg-aula-orange hover:text-aula-bg"
          >
            Mi cuenta
          </RouterLink>
        </template>

        <template v-else>
          <RouterLink to="/login" class="text-sm font-bold text-white/65 transition hover:text-aula-cream">Iniciar sesión</RouterLink>
          <RouterLink
            to="/registro"
            class="inline-flex min-h-10 items-center justify-center rounded-full border border-aula-orange/35 bg-aula-orange/10 px-4 text-xs font-extrabold text-aula-orange-soft transition hover:-translate-y-0.5 hover:bg-aula-orange hover:text-aula-bg"
          >
            Registrarse
          </RouterLink>
        </template>
      </div>

      <button
        type="button"
        class="ml-auto grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-transparent text-white md:hidden"
        :aria-expanded="menuOpen"
        aria-label="Abrir menú"
        @click="menuOpen = !menuOpen"
      >
        <span class="text-lg">{{ menuOpen ? '×' : '☰' }}</span>
      </button>
    </div>

    <div v-if="menuOpen" class="border-t border-white/10 bg-aula-bg px-6 py-5 md:hidden">
      <nav class="mx-auto grid max-w-7xl gap-4" aria-label="Navegación móvil">
        <RouterLink to="/" class="text-sm font-bold text-white/80" @click="menuOpen = false">Inicio</RouterLink>
        <RouterLink to="/cursos" class="text-sm font-bold text-white/80" @click="menuOpen = false">Cursos</RouterLink>
        <RouterLink to="/categorias" class="text-sm font-bold text-white/80" @click="menuOpen = false">Categorías</RouterLink>
        <RouterLink v-if="authenticated" :to="session.homeRoute" class="text-sm font-bold text-aula-orange-soft" @click="menuOpen = false">Mi espacio</RouterLink>
        <RouterLink v-else to="/login" class="text-sm font-bold text-aula-orange-soft" @click="menuOpen = false">Iniciar sesión</RouterLink>
      </nav>
    </div>
  </header>
</template>
