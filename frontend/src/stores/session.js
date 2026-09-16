import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

const SESSION_KEY = 'aulago_session'

function readStoredSession() {
  try {
    const stored = JSON.parse(localStorage.getItem(SESSION_KEY) || 'null')
    if (stored?.authenticated && stored?.role) return stored
  } catch {
    // Si el JSON quedó corrupto, simplemente iniciamos sin sesión.
  }

  return {
    authenticated: false,
    role: null,
    user: null,
  }
}

export const useSessionStore = defineStore('session', () => {
  const initial = readStoredSession()

  const authenticated = ref(initial.authenticated)
  const role = ref(initial.role)
  const user = ref(initial.user)

  const homeRoute = computed(() => {
    if (role.value === 'instructor') return '/instructor'
    if (role.value === 'administrador') return '/admin'
    return '/estudiante'
  })

  const accountRoute = computed(() => {
    if (role.value === 'instructor') return '/instructor/cuenta'
    if (role.value === 'administrador') return '/admin'
    return '/estudiante/cuenta'
  })

  function persist() {
    localStorage.setItem(
      SESSION_KEY,
      JSON.stringify({
        authenticated: authenticated.value,
        role: role.value,
        user: user.value,
      }),
    )
  }

  function login({ name = 'Usuario AulaGo', email = '', role: nextRole = 'estudiante' }) {
    authenticated.value = true
    role.value = nextRole
    user.value = {
      id: 1,
      name,
      email,
      role: nextRole,
    }
    persist()
  }

  function logout() {
    authenticated.value = false
    role.value = null
    user.value = null
    localStorage.removeItem(SESSION_KEY)
  }

  function hasRole(allowedRoles = []) {
    return allowedRoles.includes(role.value)
  }

  return {
    authenticated,
    role,
    user,
    homeRoute,
    accountRoute,
    login,
    logout,
    hasRole,
  }
})

