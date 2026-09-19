<script setup>
import { storeToRefs } from 'pinia'
import logoUrl from '../../assets/images/aula-logo.svg'
import { useSessionStore } from '../../stores/session.js'

const session = useSessionStore()
const { authenticated } = storeToRefs(session)
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="mt-auto border-t border-white/10 bg-aula-bg-soft text-aula-cream">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-12 md:grid-cols-[1fr_auto_auto] md:items-start lg:px-8">
      <div>
        <RouterLink to="/" class="inline-flex" aria-label="Ir al inicio de AulaGo">
          <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
        </RouterLink>
        <p class="mt-4 max-w-xs text-sm leading-6 text-white/42">
          Aprende a tu ritmo y construye una ruta de aprendizaje que cuide tu curiosidad, tu energía y tu bienestar.
        </p>
      </div>

      <nav class="grid gap-3 text-sm" aria-label="Navegación secundaria">
        <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-aula-green">Explorar</span>
        <RouterLink to="/cursos" class="font-semibold text-white/60 transition hover:text-white">Cursos</RouterLink>
        <RouterLink to="/categorias" class="font-semibold text-white/60 transition hover:text-white">Categorías</RouterLink>
        <RouterLink to="/buscar" class="font-semibold text-white/60 transition hover:text-white">Buscar</RouterLink>
      </nav>

      <nav class="grid gap-3 text-sm" aria-label="Navegación de cuenta">
        <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-aula-green">Cuenta</span>
        <template v-if="authenticated">
          <RouterLink :to="session.homeRoute" class="font-semibold text-white/60 transition hover:text-white">Mi espacio</RouterLink>
          <RouterLink :to="session.accountRoute" class="font-semibold text-white/60 transition hover:text-white">Mi cuenta</RouterLink>
        </template>
        <template v-else>
          <RouterLink to="/login" class="font-semibold text-white/60 transition hover:text-white">Iniciar sesión</RouterLink>
          <RouterLink to="/registro" class="font-semibold text-white/60 transition hover:text-white">Registrarse</RouterLink>
        </template>
      </nav>
    </div>

    <div class="border-t border-white/10">
      <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-5 text-[11px] text-white/28 sm:flex-row sm:items-center sm:justify-between lg:px-8">
        <span>© {{ currentYear }} AulaGo</span>
        <span>Aprendizaje autodidacta y bienestar cotidiano</span>
      </div>
    </div>
  </footer>
</template>
