<template>
  <div class="view-shell">
    <main class="auth-shell auth-shell--register">
      <section class="auth-form-panel auth-form-panel--register" aria-labelledby="register-title">
        <div class="register-form-wrapper">
          <div class="register-topbar">
            <RouterLink to="/" class="auth-logo" aria-label="Volver a AulaGo">
              <img src="../../assets/images/aula-logo.svg" alt="AulaGo" />
            </RouterLink>

            <RouterLink to="/" class="auth-back">← Volver</RouterLink>
          </div>

          <div class="auth-form-heading">
            <p class="eyebrow">Únete a AulaGo</p>
            <h1 id="register-title">Crea tu cuenta.</h1>
            <p>Elige cómo participarás dentro del portal y completa tus datos básicos.</p>
          </div>

          <form id="register-form" class="register-form" novalidate @submit.prevent="submitRegister">
            <div class="form-section">
              <div class="form-section-heading">
                <span>01</span>
                <h2>Información personal</h2>
              </div>

              <div class="form-grid">
                <label class="form-field">
                  <span class="form-label">Nombre completo</span>
                  <input
                    id="register-name"
                    v-model="name"
                    type="text"
                    name="nombre"
                    placeholder="Tu nombre completo"
                    autocomplete="name"
                    required
                    :class="{ 'is-invalid': errors.name }"
                    @blur="validateName"
                    @input="clearError('name')"
                  />
                  <small class="form-error">{{ errors.name }}</small>
                </label>

                <label class="form-field">
                  <span class="form-label">Correo electrónico</span>
                  <input
                    id="register-email"
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
                  <span class="form-label">Fecha de nacimiento</span>
                  <input
                    id="register-birthdate"
                    v-model="birthdate"
                    type="date"
                    name="fecha_nacimiento"
                    required
                    :class="{ 'is-invalid': errors.birthdate }"
                    @blur="validateBirthdateField"
                    @input="clearError('birthdate')"
                  />
                  <small class="form-error">{{ errors.birthdate }}</small>
                </label>

                <label class="form-field">
                  <span class="form-label">Género</span>
                  <select
                    id="register-gender"
                    v-model="gender"
                    name="genero"
                    required
                    :class="{ 'is-invalid': errors.gender }"
                    @blur="validateGender"
                    @change="clearError('gender')"
                  >
                    <option value="">Selecciona</option>
                    <option value="mujer">Mujer</option>
                    <option value="hombre">Hombre</option>
                    <option value="otro">Otro</option>
                    <option value="prefiero_no_decir">Prefiero no decirlo</option>
                  </select>
                  <small class="form-error">{{ errors.gender }}</small>
                </label>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-heading">
                <span>02</span>
                <h2>¿Cómo usarás AulaGo?</h2>
              </div>

              <div class="role-selector" role="radiogroup" aria-label="Selecciona tipo de cuenta">
                <label class="role-option">
                  <input v-model="role" type="radio" name="rol" value="estudiante" />
                  <span class="role-option__content">
                    <span class="role-option__number">01</span>
                    <strong>Estudiante</strong>
                    <small>Quiero tomar cursos, avanzar por niveles y obtener certificados.</small>
                  </span>
                </label>

                <label class="role-option">
                  <input v-model="role" type="radio" name="rol" value="instructor" />
                  <span class="role-option__content">
                    <span class="role-option__number">02</span>
                    <strong>Instructor</strong>
                    <small>Quiero crear cursos, niveles y consultar mis ventas.</small>
                  </span>
                </label>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-heading">
                <span>03</span>
                <h2>Imagen de perfil</h2>
              </div>

              <label class="avatar-upload">
                <span class="avatar-preview">
                  <img v-if="avatarPreviewUrl" :src="avatarPreviewUrl" alt="Vista previa de fotografía" />
                  <span v-else>+</span>
                </span>

                <span class="avatar-upload__text">
                  <strong>Selecciona una fotografía</strong>
                  <small>JPG o PNG. Verás una vista previa antes de crear tu cuenta.</small>
                </span>

                <input
                  id="register-avatar"
                  type="file"
                  name="avatar"
                  accept="image/png,image/jpeg"
                  required
                  @change="handleAvatarChange"
                />
              </label>

              <small class="form-error">{{ errors.avatar }}</small>
            </div>

            <div class="form-section">
              <div class="form-section-heading">
                <span>04</span>
                <h2>Seguridad</h2>
              </div>

              <div class="security-grid">
                <div class="security-inputs">
                  <label class="form-field">
                    <span class="form-label">Contraseña</span>

                    <div class="password-input">
                      <input
                        id="register-password"
                        v-model="password"
                        :type="passwordInputType"
                        name="password"
                        placeholder="Crea una contraseña"
                        autocomplete="new-password"
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

                  <label class="form-field">
                    <span class="form-label">Confirmar contraseña</span>

                    <div class="password-input">
                      <input
                        id="register-password-confirmation"
                        v-model="passwordConfirmation"
                        :type="confirmationInputType"
                        name="password_confirmation"
                        placeholder="Repite la contraseña"
                        autocomplete="new-password"
                        required
                        :class="{ 'is-invalid': errors.passwordConfirmation }"
                        @blur="validatePasswordConfirmationField"
                        @input="clearError('passwordConfirmation')"
                      />

                      <button
                        type="button"
                        class="password-toggle"
                        :aria-label="showConfirmation ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                        @click="showConfirmation = !showConfirmation"
                      >
                        {{ showConfirmation ? 'Ocultar' : 'Ver' }}
                      </button>
                    </div>

                    <small class="form-error">{{ errors.passwordConfirmation }}</small>
                  </label>
                </div>

                <div class="password-rules" aria-label="Requisitos de contraseña">
                  <span>Tu contraseña necesita:</span>
                  <ul>
                    <li :class="{ 'is-valid': passwordChecks.length }">
                      <span>{{ passwordChecks.length ? '✓' : '○' }}</span>
                      mínimo 8 caracteres
                    </li>
                    <li :class="{ 'is-valid': passwordChecks.uppercase }">
                      <span>{{ passwordChecks.uppercase ? '✓' : '○' }}</span>
                      una letra mayúscula
                    </li>
                    <li :class="{ 'is-valid': passwordChecks.number }">
                      <span>{{ passwordChecks.number ? '✓' : '○' }}</span>
                      un número
                    </li>
                    <li :class="{ 'is-valid': passwordChecks.special }">
                      <span>{{ passwordChecks.special ? '✓' : '○' }}</span>
                      un carácter especial
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <p
              v-if="formStatus.message"
              class="border px-4 py-3 text-xs leading-5"
              :class="formStatus.type === 'error'
                ? 'border-[#d76854]/40 bg-[#d76854]/10 text-[#f0907b]'
                : 'border-aula-green/40 bg-aula-green/10 text-aula-green'"
              :role="formStatus.type === 'error' ? 'alert' : 'status'"
            >
              {{ formStatus.message }}
            </p>

            <div class="register-actions">
              <button type="submit" class="auth-submit">
                Crear cuenta
                <span>→</span>
              </button>

              <p>
                ¿Ya tienes una cuenta?
                <RouterLink to="/login">Iniciar sesión</RouterLink>
              </p>
            </div>
          </form>
        </div>
      </section>

      <aside class="register-visual">
        <div class="register-visual__glow"></div>

        <div class="register-visual__header">
          <p class="eyebrow eyebrow--light">Tu espacio</p>
          <h2>
            Aprende.<br />
            Comparte.<br />
            <em>Sigue creciendo.</em>
          </h2>
        </div>

        <figure class="register-visual__image">
          <img src="../../assets/images/registro-bg.png" alt="Espacio de aprendizaje AulaGo" />
        </figure>

        <div class="register-visual__card">
          <span>AulaGo / 2026</span>
          <p>{{ roleDescription }}</p>
        </div>
      </aside>
    </main>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useFormValidation } from '../../utils/validation.js'

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
  errors.value.passwordConfirmation = validatePasswordConfirmation(
    password.value,
    passwordConfirmation.value,
  )
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

onMounted(() => {
  document.body.className = 'auth-page'
})

onBeforeUnmount(() => {
  if (avatarPreviewUrl.value) URL.revokeObjectURL(avatarPreviewUrl.value)
  document.body.className = ''
})
</script>
