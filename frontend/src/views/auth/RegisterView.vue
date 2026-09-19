<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useFormValidation } from '../../utils/validation.js'
import logoUrl from '../../assets/images/aula-logo.svg'
import registerImage from '../../assets/images/registro-bg.png'

const router = useRouter()
const {
  isRequired,
  validateEmail,
  validateRequired,
  validateBirthdate,
  validateAvatar,
  getPasswordChecks,
  validatePassword,
  validatePasswordConfirmation,
} = useFormValidation()

const name = ref('')
const email = ref('')
const birthdate = ref('')
const gender = ref('')
const role = ref('estudiante')
const avatarFile = ref(null)
const avatarPreviewUrl = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)
const showConfirmation = ref(false)
const errors = ref({
  name: '',
  email: '',
  birthdate: '',
  gender: '',
  avatar: '',
  password: '',
  passwordConfirmation: '',
})
const formStatus = ref({ message: '', type: 'error' })

const passwordChecks = computed(() => getPasswordChecks(password.value))
const passwordInputType = computed(() => (showPassword.value ? 'text' : 'password'))
const confirmationInputType = computed(() => (showConfirmation.value ? 'text' : 'password'))
const roleDescription = computed(() => {
  return role.value === 'instructor'
    ? 'Comparte lo que sabes y construye experiencias de aprendizaje para otros.'
    : 'Explora nuevas ideas y avanza por cursos diseñados para aprender a tu ritmo.'
})

const registerFormIsValid = computed(() => {
  return (
    isRequired(name.value) &&
    !validateEmail(email.value) &&
    !validateBirthdate(birthdate.value) &&
    isRequired(gender.value) &&
    !validateAvatar(avatarFile.value) &&
    !validatePassword(password.value) &&
    !validatePasswordConfirmation(password.value, passwordConfirmation.value)
  )
})

function clearError(field) {
  errors.value[field] = ''
  formStatus.value = { message: '', type: 'error' }
}

function validateName() {
  errors.value.name = validateRequired(name.value, 'El nombre completo es obligatorio.')
  return !errors.value.name
}

function validateEmailField() {
  errors.value.email = validateEmail(email.value)
  return !errors.value.email
}

function validateBirthdateField() {
  errors.value.birthdate = validateBirthdate(birthdate.value)
  return !errors.value.birthdate
}

function validateGender() {
  errors.value.gender = validateRequired(gender.value, 'Selecciona una opción de género.')
  return !errors.value.gender
}

function validateAvatarField() {
  errors.value.avatar = validateAvatar(avatarFile.value)
  return !errors.value.avatar
}

function validatePasswordField() {
  errors.value.password = validatePassword(password.value)
  return !errors.value.password
}

function validatePasswordConfirmationField() {
  errors.value.passwordConfirmation = validatePasswordConfirmation(password.value, passwordConfirmation.value)
  return !errors.value.passwordConfirmation
}

function handleAvatarChange(event) {
  const file = event.target.files?.[0] ?? null

  if (avatarPreviewUrl.value) {
    URL.revokeObjectURL(avatarPreviewUrl.value)
    avatarPreviewUrl.value = ''
  }

  avatarFile.value = file
  errors.value.avatar = validateAvatar(file)

  if (file?.type?.startsWith('image/')) {
    avatarPreviewUrl.value = URL.createObjectURL(file)
  }
}

function submitRegister() {
  const validations = [
    validateName(),
    validateEmailField(),
    validateBirthdateField(),
    validateGender(),
    validateAvatarField(),
    validatePasswordField(),
    validatePasswordConfirmationField(),
  ]

  if (!validations.every(Boolean) || !registerFormIsValid.value) {
    formStatus.value = {
      message: 'Hay información pendiente o inválida. Revisa los campos señalados.',
      type: 'error',
    }
    return
  }

  formStatus.value = {
    message: `Formulario válido para registro como ${role.value}.`,
    type: 'success',
  }

  router.push({ name: 'login', query: { role: role.value, registered: '1' } })
}

onBeforeUnmount(() => {
  if (avatarPreviewUrl.value) URL.revokeObjectURL(avatarPreviewUrl.value)
})
</script>

<template>
  <main class="grid min-h-screen bg-aula-bg text-white xl:grid-cols-[1.18fr_0.82fr]">
    <section class="px-6 py-8 sm:px-10 lg:px-14 xl:px-16">
      <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between">
          <RouterLink to="/" class="inline-flex" aria-label="Volver a AulaGo">
            <img :src="logoUrl" alt="AulaGo" class="h-10 w-auto" />
          </RouterLink>
          <RouterLink to="/" class="text-xs font-bold text-white/45 transition hover:text-aula-orange-soft">← Volver</RouterLink>
        </div>

        <div class="mt-12">
          <p class="font-mono text-[11px] font-bold uppercase tracking-[0.18em] text-aula-green">Únete a AulaGo</p>
          <h1 id="register-title" class="mt-4 font-editorial text-5xl leading-none text-white sm:text-6xl">Crea tu cuenta.</h1>
          <p class="mt-4 max-w-xl text-sm leading-7 text-white/45">
            Elige cómo participarás dentro del portal y completa tus datos básicos.
          </p>
        </div>

        <form class="mt-10 grid gap-10" novalidate @submit.prevent="submitRegister">
          <section class="border-t border-white/10 pt-7">
            <div class="mb-6 flex items-center gap-4">
              <span class="font-mono text-xs font-bold text-aula-orange-soft">01</span>
              <h2 class="text-lg font-extrabold text-white">Información personal</h2>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/70">Nombre completo</span>
                <input
                  v-model="name"
                  type="text"
                  name="nombre"
                  placeholder="Tu nombre completo"
                  autocomplete="name"
                  required
                  class="min-h-12 rounded-xl border bg-white/[0.04] px-4 text-sm text-white outline-none transition placeholder:text-white/25"
                  :class="errors.name ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
                  @blur="validateName"
                  @input="clearError('name')"
                />
                <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.name }}</small>
              </label>

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
                  :class="errors.email ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
                  @blur="validateEmailField"
                  @input="clearError('email')"
                />
                <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.email }}</small>
              </label>

              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/70">Fecha de nacimiento</span>
                <input
                  v-model="birthdate"
                  type="date"
                  name="fecha_nacimiento"
                  required
                  class="min-h-12 rounded-xl border bg-white/[0.04] px-4 text-sm text-white outline-none transition"
                  :class="errors.birthdate ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
                  @blur="validateBirthdateField"
                  @input="clearError('birthdate')"
                />
                <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.birthdate }}</small>
              </label>

              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/70">Género</span>
                <select
                  v-model="gender"
                  name="genero"
                  required
                  class="min-h-12 rounded-xl border bg-aula-surface px-4 text-sm text-white outline-none transition"
                  :class="errors.gender ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
                  @blur="validateGender"
                  @change="clearError('gender')"
                >
                  <option value="">Selecciona</option>
                  <option value="mujer">Mujer</option>
                  <option value="hombre">Hombre</option>
                  <option value="otro">Otro</option>
                  <option value="prefiero_no_decir">Prefiero no decirlo</option>
                </select>
                <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.gender }}</small>
              </label>
            </div>
          </section>

          <section class="border-t border-white/10 pt-7">
            <div class="mb-6 flex items-center gap-4">
              <span class="font-mono text-xs font-bold text-[#9fcbd5]">02</span>
              <h2 class="text-lg font-extrabold text-white">¿Cómo usarás AulaGo?</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2" role="radiogroup" aria-label="Selecciona tipo de cuenta">
              <label
                class="cursor-pointer rounded-2xl border p-5 transition"
                :class="role === 'estudiante' ? 'border-brand-blue bg-brand-blue/10' : 'border-white/10 bg-white/[0.02] hover:border-white/25'"
              >
                <input v-model="role" type="radio" name="rol" value="estudiante" class="sr-only" />
                <span class="font-mono text-[10px] font-bold text-[#9fcbd5]">01</span>
                <strong class="mt-5 block text-base text-white">Estudiante</strong>
                <small class="mt-2 block text-xs leading-6 text-white/45">
                  Quiero tomar cursos, avanzar por niveles y obtener certificados.
                </small>
              </label>

              <label
                class="cursor-pointer rounded-2xl border p-5 transition"
                :class="role === 'instructor' ? 'border-brand-orange bg-brand-orange/10' : 'border-white/10 bg-white/[0.02] hover:border-white/25'"
              >
                <input v-model="role" type="radio" name="rol" value="instructor" class="sr-only" />
                <span class="font-mono text-[10px] font-bold text-aula-orange-soft">02</span>
                <strong class="mt-5 block text-base text-white">Instructor</strong>
                <small class="mt-2 block text-xs leading-6 text-white/45">
                  Quiero crear cursos, niveles y consultar mis ventas.
                </small>
              </label>
            </div>
          </section>

          <section class="border-t border-white/10 pt-7">
            <div class="mb-6 flex items-center gap-4">
              <span class="font-mono text-xs font-bold text-aula-green">03</span>
              <h2 class="text-lg font-extrabold text-white">Imagen de perfil</h2>
            </div>

            <label class="flex cursor-pointer flex-col gap-5 rounded-2xl border border-dashed border-white/20 bg-white/[0.02] p-5 transition hover:border-brand-green sm:flex-row sm:items-center">
              <span class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-full border border-white/15 bg-white/[0.04] text-2xl text-aula-green">
                <img v-if="avatarPreviewUrl" :src="avatarPreviewUrl" alt="Vista previa de fotografía" class="h-full w-full object-cover" />
                <span v-else>+</span>
              </span>

              <span>
                <strong class="block text-sm text-white">Selecciona una fotografía</strong>
                <small class="mt-2 block text-xs leading-6 text-white/40">
                  JPG o PNG. Verás una vista previa antes de crear tu cuenta.
                </small>
              </span>

              <input
                type="file"
                name="avatar"
                accept="image/png,image/jpeg"
                required
                class="sr-only"
                @change="handleAvatarChange"
              />
            </label>

            <small class="mt-2 block min-h-4 text-[11px] text-brand-coral">{{ errors.avatar }}</small>
          </section>

          <section class="border-t border-white/10 pt-7">
            <div class="mb-6 flex items-center gap-4">
              <span class="font-mono text-xs font-bold text-brand-magenta">04</span>
              <h2 class="text-lg font-extrabold text-white">Seguridad</h2>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_0.75fr]">
              <div class="grid gap-5">
                <label class="grid gap-2">
                  <span class="text-xs font-bold text-white/70">Contraseña</span>
                  <div class="relative">
                    <input
                      v-model="password"
                      :type="passwordInputType"
                      name="password"
                      placeholder="Crea una contraseña"
                      autocomplete="new-password"
                      required
                      class="min-h-12 w-full rounded-xl border bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25"
                      :class="errors.password ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
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

                <label class="grid gap-2">
                  <span class="text-xs font-bold text-white/70">Confirmar contraseña</span>
                  <div class="relative">
                    <input
                      v-model="passwordConfirmation"
                      :type="confirmationInputType"
                      name="password_confirmation"
                      placeholder="Repite la contraseña"
                      autocomplete="new-password"
                      required
                      class="min-h-12 w-full rounded-xl border bg-white/[0.04] px-4 pr-20 text-sm text-white outline-none transition placeholder:text-white/25"
                      :class="errors.passwordConfirmation ? 'border-brand-coral' : 'border-white/15 focus:border-brand-orange'"
                      @blur="validatePasswordConfirmationField"
                      @input="clearError('passwordConfirmation')"
                    />
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-xs font-bold text-aula-orange-soft transition hover:bg-white/5"
                      :aria-label="showConfirmation ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                      @click="showConfirmation = !showConfirmation"
                    >
                      {{ showConfirmation ? 'Ocultar' : 'Ver' }}
                    </button>
                  </div>
                  <small class="min-h-4 text-[11px] text-brand-coral">{{ errors.passwordConfirmation }}</small>
                </label>
              </div>

              <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-5" aria-label="Requisitos de contraseña">
                <span class="text-xs font-bold text-white/60">Tu contraseña necesita:</span>
                <ul class="mt-4 grid gap-3 text-xs">
                  <li :class="passwordChecks.length ? 'text-aula-green' : 'text-white/35'">
                    <span class="mr-2">{{ passwordChecks.length ? '✓' : '○' }}</span> mínimo 8 caracteres
                  </li>
                  <li :class="passwordChecks.uppercase ? 'text-aula-green' : 'text-white/35'">
                    <span class="mr-2">{{ passwordChecks.uppercase ? '✓' : '○' }}</span> una letra mayúscula
                  </li>
                  <li :class="passwordChecks.number ? 'text-aula-green' : 'text-white/35'">
                    <span class="mr-2">{{ passwordChecks.number ? '✓' : '○' }}</span> un número
                  </li>
                  <li :class="passwordChecks.special ? 'text-aula-green' : 'text-white/35'">
                    <span class="mr-2">{{ passwordChecks.special ? '✓' : '○' }}</span> un carácter especial
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <p
            v-if="formStatus.message"
            class="rounded-xl border px-4 py-3 text-xs leading-5"
            :class="formStatus.type === 'error'
              ? 'border-brand-coral/40 bg-brand-coral/10 text-red-200'
              : 'border-brand-green/40 bg-brand-green/10 text-green-200'"
            :role="formStatus.type === 'error' ? 'alert' : 'status'"
          >
            {{ formStatus.message }}
          </p>

          <div class="flex flex-col gap-4 border-t border-white/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
            <button
              type="submit"
              class="inline-flex min-h-12 items-center justify-center gap-3 rounded-full bg-aula-orange px-7 text-sm font-extrabold text-aula-bg transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-aula-orange/10"
            >
              Crear cuenta
              <span aria-hidden="true">→</span>
            </button>
            <p class="text-sm text-white/45">
              ¿Ya tienes una cuenta?
              <RouterLink to="/login" class="font-bold text-white transition hover:text-[#9fcbd5]">Iniciar sesión</RouterLink>
            </p>
          </div>
        </form>
      </div>
    </section>

    <aside class="relative hidden min-h-screen overflow-hidden border-l border-white/10 xl:block">
      <img :src="registerImage" alt="Espacio de aprendizaje AulaGo" class="absolute inset-0 h-full w-full object-cover opacity-45" />
      <div class="absolute inset-0 bg-gradient-to-b from-aula-bg/20 via-aula-bg/58 to-aula-bg"></div>
      <div class="absolute -right-40 top-1/3 h-[520px] w-[520px] rounded-full bg-[#79b8c7]/10 blur-3xl"></div>

      <div class="relative z-10 flex h-full min-h-screen flex-col justify-between p-12">
        <div>
          <p class="font-mono text-[11px] font-bold uppercase tracking-[0.18em] text-[#9fcbd5]">Tu espacio</p>
          <h2 class="mt-5 font-editorial text-6xl leading-[0.94] text-white">
            Aprende.<br />
            Comparte.<br />
            <span class="text-aula-orange-soft">Sigue creciendo.</span>
          </h2>
        </div>

        <div class="rounded-2xl border border-white/10 bg-aula-bg/60 p-6 backdrop-blur">
          <span class="font-mono text-[10px] uppercase tracking-[0.15em] text-white/35">AulaGo / 2026</span>
          <p class="mt-3 text-sm leading-7 text-white/65">{{ roleDescription }}</p>
        </div>
      </div>
    </aside>
  </main>
</template>
