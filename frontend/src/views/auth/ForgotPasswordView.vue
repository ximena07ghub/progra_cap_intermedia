<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <main class="mx-auto grid min-h-screen max-w-aula place-items-center px-6 py-12 lg:px-8">
      <section class="w-full max-w-xl border border-white/10 bg-aula-surface/70 p-7 shadow-aula sm:p-10">
        <RouterLink to="/login" class="text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">← Volver al login</RouterLink>

        <p class="mt-10 font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Recuperación de acceso</p>
        <h1 class="mt-3 font-editorial text-5xl leading-none">Recupera tu contraseña.</h1>
        <p class="mt-5 text-sm leading-7 text-white/45">
          Escribe el correo de tu cuenta. En el prototipo mostraremos la confirmación; después este formulario llamará a tu API PHP.
        </p>

        <form class="mt-8 grid gap-5" novalidate @submit.prevent="submit">
          <label class="grid gap-2">
            <span class="text-xs font-bold uppercase tracking-[0.08em] text-white/60">Correo electrónico</span>
            <input
              v-model="email"
              type="email"
              autocomplete="email"
              class="min-h-12 border border-white/15 bg-aula-bg px-4 text-sm text-white outline-none focus:border-aula-orange"
              placeholder="nombre@correo.com"
              @input="error = ''"
            />
            <small v-if="error" class="text-xs text-[#f0907b]">{{ error }}</small>
          </label>

          <p v-if="sent" class="border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-sm text-aula-green" role="status">
            Solicitud registrada. En producción, aquí se enviará el enlace de recuperación al correo.
          </p>

          <button type="submit" class="min-h-12 bg-aula-orange px-5 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">
            Enviar enlace
          </button>
        </form>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useFormValidation } from '../../utils/validation.js'

const { validateEmail } = useFormValidation()
const email = ref('')
const error = ref('')
const sent = ref(false)

function submit() {
  error.value = validateEmail(email.value)
  sent.value = !error.value
}
</script>

