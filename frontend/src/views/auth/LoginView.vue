<template>
  <div class="view-shell">
    <main class="auth-shell auth-shell--login">
      <section class="auth-art-panel auth-art-panel--login">
        <div class="auth-art-panel__top">
          <RouterLink to="/" class="auth-logo" aria-label="Volver a AulaGo">
            <img src="../../assets/images/aula-logo.svg" alt="AulaGo" />
          </RouterLink>

          <RouterLink to="/" class="auth-back">
            ← Volver al inicio
          </RouterLink>
        </div>

        <div class="auth-art-panel__content">
          <p class="eyebrow">Continúa aprendiendo</p>

          <h1>
            Regresa a lo que
            <em>despertó tu curiosidad.</em>
          </h1>

          <p class="auth-hero-description">
            Retoma tus cursos, continúa tu progreso y descubre qué puedes aprender después.
          </p>

          <p class="auth-hero-description">
            Tu próxima idea puede empezar justo donde te quedaste.
          </p>
        </div>
      </section>

      <section class="auth-form-panel" aria-labelledby="login-title">
        <div class="auth-form-wrapper">
          <div class="auth-form-heading">
            <p class="eyebrow">Acceso</p>
            <h2 id="login-title">Iniciar sesión</h2>
            <p>Ingresa tus datos para continuar.</p>
          </div>

          <form id="login-form" class="auth-form" novalidate @submit.prevent="submitLogin">
            <label class="form-field">
              <span class="form-label">Correo electrónico</span>

              <input
                id="login-email"
                v-model="email"
                type="email"
                name="email"
                placeholder="nombre@correo.com"
                autocomplete="email"
                required
                :class="{ 'is-invalid': errors.email }"
                @blur="validateEmailField"
                @input="clearError('email')"
              />

              <small class="form-error">{{ errors.email }}</small>
            </label>

            <label class="form-field">
              <span class="form-label">Contraseña</span>

              <div class="password-input">
                <input
                  id="login-password"
                  v-model="password"
                  :type="passwordInputType"
                  name="password"
                  placeholder="Tu contraseña"
                  autocomplete="current-password"
                  required
                  :class="{ 'is-invalid': errors.password }"
                  @blur="validatePasswordField"
                  @input="clearError('password')"
                />

                <button
                  type="button"
                  class="password-toggle"
                  :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                  @click="showPassword = !showPassword"
                >
                  {{ showPassword ? 'Ocultar' : 'Ver' }}
                </button>
              </div>

              <small class="form-error">{{ errors.password }}</small>
            </label>

            <div class="login-options">
              <label class="checkbox-option">
                <input v-model="remember" type="checkbox" name="remember" />
                <span>Recordarme</span>
              </label>

              <RouterLink to="/recuperar-contrasena">¿Olvidaste tu contraseña?</RouterLink>
            </div>

            <p
              v-if="formStatus"
              class="border border-[#d76854]/40 bg-[#d76854]/10 px-4 py-3 text-xs leading-5 text-[#f0907b]"
              role="alert"
            >
              {{ formStatus }}
            </p>

            <button class="auth-submit" type="submit">
              Iniciar sesión
              <span>→</span>
            </button>
          </form>

          <div class="auth-switch">
            <span>¿Todavía no tienes una cuenta?</span>
            <RouterLink to="/registro">Crear cuenta</RouterLink>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSessionStore } from '../../stores/session.js'
import { useFormValidation } from '../../utils/validation.js'

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

onMounted(() => {
  document.body.className = 'auth-page'
})

onBeforeUnmount(() => {
  document.body.className = ''
})
</script>
