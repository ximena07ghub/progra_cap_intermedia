<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSessionStore } from '../../stores/session.js'
import { useFormValidation } from '../../utils/validation.js'
import logoUrl from '../../assets/images/aula-logo.svg'
import loginImage from '../../assets/images/login-bg.png'

const route = useRoute()
const router = useRouter()
const session = useSessionStore()
const { isRequired, isEmail } = useFormValidation()

const email = ref('')
const password = ref('')
const remember = ref(false)
const showPassword = ref(false)
const formStatus = ref('')
const errors = ref({
  email: '',
  password: '',
})

const passwordInputType = computed(() => (showPassword.value ? 'text' : 'password'))
const loginFormIsValid = computed(() => isEmail(email.value) && isRequired(password.value))

function clearError(field) {
  errors.value[field] = ''
  formStatus.value = ''
}

function validateEmailField() {
  if (!isRequired(email.value)) {
    errors.value.email = 'El correo electrónico es obligatorio.'
  } else if (!isEmail(email.value)) {
    errors.value.email = 'Ingresa un correo electrónico válido.'
  } else {
    errors.value.email = ''
  }

  return !errors.value.email
}

function validatePasswordField() {
  errors.value.password = isRequired(password.value) ? '' : 'La contraseña es obligatoria.'
  return !errors.value.password
}

function submitLogin() {
  const emailValid = validateEmailField()
  const passwordValid = validatePasswordField()

  if (!emailValid || !passwordValid || !loginFormIsValid.value) {
    formStatus.value = 'Revisa los campos marcados antes de continuar.'
    return
  }

  const allowedRoles = ['estudiante', 'instructor', 'administrador']
  const requestedRole = typeof route.query.role === 'string' && allowedRoles.includes(route.query.role)
    ? route.query.role
    : null

  const normalizedEmail = email.value.trim().toLowerCase()
  const inferredRole = normalizedEmail.startsWith('admin@')
    ? 'administrador'
    : normalizedEmail.startsWith('instructor@')
      ? 'instructor'
      : 'estudiante'

  const selectedRole = requestedRole || inferredRole

  session.login({
    name: selectedRole === 'instructor'
      ? 'Instructor AulaGo'
      : selectedRole === 'administrador'
        ? 'Administrador AulaGo'
        : 'Estudiante AulaGo',
    email: email.value.trim(),
    role: selectedRole,
  })

  const redirect = typeof route.query.redirect === 'string'
    ? route.query.redirect
    : session.homeRoute

  router.push(redirect)
}
</script>

<template>
  <main class="grid min-h-screen bg-aula-bg text-white lg:grid-cols-[1.1fr_0.9fr]">
    <section class="relative hidden min-h-screen overflow-hidden border-r border-white/10 lg:flex lg:flex-col">
      <img :src="loginImage" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30" />
      <div class="absolute inset-0 bg-gradient-to-b from-aula-bg/25 via-aula-bg/65 to-aula-bg"></div>
      <div class="absolute -bottom-40 left-1/4 h-[520px] w-[520px] rounded-full bg-aula-orange/12 blur-3xl"></div>

      <div class="relative z-10 flex items-center justify-between px-10 py-9 xl:px-14">
        <RouterLink to="/" class="inline-flex" aria-label="Volver a AulaGo">
          <img :src="logoUrl" alt="AulaGo" class="h-10 w-auto" />
        </RouterLink>
        <RouterLink to="/" class="text-xs font-bold text-white/50 transition hover:text-aula-orange-soft">
          ← Volver al inicio
        </RouterLink>
      </div>

      <div class="relative z-10 mt-auto max-w-3xl px-10 pb-16 xl:px-14 xl:pb-20">
        <p class="font-mono text-[11px] font-bold uppercase tracking-[0.18em] text-aula-green">Continúa aprendiendo</p>
        <h1 class="mt-5 font-editorial text-6xl leading-[0.95] text-white xl:text-7xl">
          Regresa a lo que
          <span class="block text-aula-orange-soft">despertó tu curiosidad.</span>
        </h1>
        <p class="mt-7 max-w-xl text-sm leading-7 text-white/55">
          Retoma tus cursos, continúa tu progreso y descubre qué puedes aprender después sin perder de vista tu ritmo y tu bienestar.
        </p>
      </div>
    </section>

    <section class="grid min-h-screen place-items-center px-6 py-12 sm:px-10 lg:px-12" aria-labelledby="login-title">
      <div class="w-full max-w-md">
        <div class="mb-10 flex items-center justify-between lg:hidden">
          <RouterLink to="/" class="inline-flex" aria-label="Volver a AulaGo">
            <img :src="logoUrl" alt="AulaGo" class="h-9 w-auto" />
          </RouterLink>
          <RouterLink to="/" class="text-xs font-bold text-white/45 transition hover:text-aula-orange-soft">← Volver</RouterLink>
        </div>

        <div>
          <p class="font-mono text-[11px] font-bold uppercase tracking-[0.18em] text-[#9fcbd5]">Acceso</p>
          <h2 id="login-title" class="mt-4 font-editorial text-5xl leading-none text-white">Iniciar sesión</h2>
          <p class="mt-3 text-sm text-white/45">Ingresa tus datos para continuar.</p>
        </div>

        <form class="mt-9 grid gap-5" novalidate @submit.prevent="submitLogin">
          <label class="grid gap-2">
            <span class="text-xs font-bold text-white/70">Correo electrónico</span>
            <input
              v-model="email"
              type="email"
              name="email"
              placeholder="nombre@correo.com"
              autocomplete="email"
              required
              class="min-h-12 rounded-xl border bg-white/[0.04] px-4 text-sm text-white outline-none transition placeholder:text-white/25"
              :class="errors.email ? 'border-brand-coral focus:border-brand-coral' : 'border-white/15 focus:border-aula-orange'"
              @blur="validateEmailField"
              @input="clearError('email')"
            />
            <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.email }}</small>
          </label>

          <label class="grid gap-2">
            <span class="text-xs font-bold text-white/70">Contraseña</span>
            <div class="relative">
              <input
                v-model="password"
                :type="passwordInputType"
                name="password"
                placeholder="Tu contraseña"
                autocomplete="current-password"
                required
                class="min-h-12 w-full rounded-xl border bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25"
                :class="errors.password ? 'border-brand-coral focus:border-brand-coral' : 'border-white/15 focus:border-aula-orange'"
                @blur="validatePasswordField"
                @input="clearError('password')"
              />

              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-xs font-bold text-aula-orange-soft transition hover:bg-white/5"
                :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? 'Ocultar' : 'Ver' }}
              </button>
            </div>
            <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.password }}</small>
          </label>

          <div class="flex flex-col gap-3 text-xs sm:flex-row sm:items-center sm:justify-between">
            <label class="inline-flex items-center gap-2 text-white/60">
              <input v-model="remember" type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-transparent accent-aula-orange" />
              <span>Recordarme</span>
            </label>
            <RouterLink to="/recuperar-contrasena" class="font-bold text-[#9fcbd5] transition hover:text-aula-orange-soft">
              ¿Olvidaste tu contraseña?
            </RouterLink>
          </div>

          <p v-if="formStatus" class="rounded-xl border border-brand-coral/40 bg-brand-coral/10 px-4 py-3 text-xs leading-5 text-red-200" role="alert">
            {{ formStatus }}
          </p>

          <button
            type="submit"
            class="mt-2 inline-flex min-h-12 items-center justify-center gap-3 rounded-full bg-aula-orange px-6 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-aula-orange/10 focus:outline-none focus:ring-2 focus:ring-aula-orange focus:ring-offset-2 focus:ring-offset-aula-bg"
          >
            Iniciar sesión
            <span aria-hidden="true">→</span>
          </button>
        </form>

        <div class="mt-8 flex flex-wrap items-center gap-2 border-t border-white/10 pt-6 text-sm text-white/45">
          <span>¿Todavía no tienes una cuenta?</span>
          <RouterLink to="/registro" class="font-bold text-white transition hover:text-[#9fcbd5]">Crear cuenta</RouterLink>
        </div>
      </div>
    </section>
  </main>
</template>
