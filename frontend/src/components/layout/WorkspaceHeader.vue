<template>
  <header class="sticky top-0 z-50 border-b border-white/10 bg-aula-bg/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-aula items-center gap-5 px-6 py-4 lg:px-8">
      <RouterLink :to="config.home" class="shrink-0" aria-label="Ir al panel de AulaGo">
        <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
      </RouterLink>

      <nav class="hidden items-center gap-5 lg:flex" :aria-label="`Navegación de ${config.label}`">
        <RouterLink
          v-for="item in config.links"
          :key="item.to"
          :to="item.to"
          class="text-sm font-bold text-white/65 transition hover:text-white"
        >
          {{ item.label }}
        </RouterLink>
      </nav>

      <div class="ml-auto flex items-center gap-3">
        <RouterLink
          v-if="config.account"
          :to="config.account"
          class="hidden text-sm font-bold text-white/65 transition hover:text-white sm:block"
        >
          Mi cuenta
        </RouterLink>

        <span class="hidden border-l border-white/10 pl-4 text-xs font-bold uppercase tracking-[0.08em] text-aula-green sm:block">
          {{ config.label }}
        </span>

        <button
          type="button"
          class="hidden text-xs font-bold text-white/45 transition hover:text-aula-orange sm:block"
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
        <RouterLink
          v-for="item in config.links"
          :key="item.to"
          :to="item.to"
          class="text-sm font-bold text-white/80"
          @click="menuOpen = false"
        >
          {{ item.label }}
        </RouterLink>
        <RouterLink
          v-if="config.account"
          :to="config.account"
          class="text-sm font-bold text-white/80"
          @click="menuOpen = false"
        >
          Mi cuenta
        </RouterLink>
        <button type="button" class="w-fit text-sm font-bold text-aula-orange" @click="logout">
          Cerrar sesión
        </button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import logoUrl from '../../assets/images/aula-logo.svg'
import { useSessionStore } from '../../stores/session.js'

const props = defineProps({
  mode: {
    type: String,
    required: true,
    validator: (value) => ['instructor', 'administrador'].includes(value),
  },
})

const router = useRouter()
const session = useSessionStore()
const menuOpen = ref(false)

const configs = {
  instructor: {
    label: 'Instructor',
    home: '/instructor',
    account: '/instructor/cuenta',
    links: [
      { label: 'Resumen', to: '/instructor' },
      { label: 'Mis cursos', to: '/instructor/cursos' },
      { label: 'Ventas', to: '/instructor/ventas' },
      { label: 'Mensajes', to: '/instructor/mensajes' },
    ],
  },
  administrador: {
    label: 'Administrador',
    home: '/admin',
    account: null,
    links: [
      { label: 'Resumen', to: '/admin' },
      { label: 'Categorías', to: '/admin/categorias' },
      { label: 'Usuarios', to: '/admin/usuarios' },
      { label: 'Comentarios', to: '/admin/comentarios' },
    ],
  },
}

const config = computed(() => configs[props.mode])

function logout() {
  session.logout()
  router.push('/')
}
</script>

