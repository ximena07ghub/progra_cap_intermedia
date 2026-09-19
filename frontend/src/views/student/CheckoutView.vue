<template>
  <div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main class="mx-auto grid w-full max-w-aula flex-1 gap-8 px-6 py-12 lg:grid-cols-[1fr_380px] lg:px-8">
      <section>
        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Compra de curso</p>
        <h1 class="mt-3 font-editorial text-5xl">Finaliza tu inscripción.</h1>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-white/45">Pantalla mock para dejar lista la navegación. No procesa pagos reales.</p>

        <form class="mt-8 grid gap-6" @submit.prevent="submitPurchase">
          <fieldset class="border border-white/10 p-5">
            <legend class="px-2 text-xs font-bold uppercase tracking-[0.1em] text-white/60">Forma de pago</legend>
            <div class="grid gap-3 sm:grid-cols-2">
              <label class="flex cursor-pointer gap-3 border border-white/10 p-4" :class="method === 'card' ? 'border-aula-orange bg-aula-orange/5' : ''">
                <input v-model="method" type="radio" value="card" />
                <span><strong class="block text-sm">Tarjeta</strong><small class="mt-1 block text-white/35">Crédito o débito</small></span>
              </label>
              <label class="flex cursor-pointer gap-3 border border-white/10 p-4" :class="method === 'transfer' ? 'border-aula-orange bg-aula-orange/5' : ''">
                <input v-model="method" type="radio" value="transfer" />
                <span><strong class="block text-sm">Transferencia</strong><small class="mt-1 block text-white/35">Referencia bancaria</small></span>
              </label>
            </div>
          </fieldset>

          <div v-if="method === 'card'" class="grid gap-4 border border-white/10 p-5 sm:grid-cols-2">
            <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Nombre en la tarjeta</span><input v-model="card.name" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" /></label>
            <label class="grid gap-2 sm:col-span-2"><span class="text-xs text-white/45">Número de tarjeta</span><input v-model="card.number" inputmode="numeric" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="0000 0000 0000 0000" /></label>
            <label class="grid gap-2"><span class="text-xs text-white/45">Vencimiento</span><input v-model="card.expiry" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="MM/AA" /></label>
            <label class="grid gap-2"><span class="text-xs text-white/45">CVV</span><input v-model="card.cvv" inputmode="numeric" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white" placeholder="000" /></label>
          </div>

          <div v-else class="border border-white/10 p-5 text-sm leading-7 text-white/55">
            <strong class="block text-aula-cream">Transferencia SPEI</strong>
            <p class="mt-2">Banco AulaGo Demo · CLABE 000 000 0000000000 0</p>
            <p>Usa como referencia: <span class="font-bold text-aula-orange">{{ course.slug.toUpperCase() }}</span></p>
          </div>

          <p v-if="status" class="border border-aula-green/30 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">{{ status }}</p>

          <button type="submit" class="min-h-12 bg-aula-orange px-6 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Confirmar compra</button>
        </form>
      </section>

      <aside class="h-fit border border-white/10 bg-white/[0.02] p-5 lg:sticky lg:top-24">
        <img :src="course.image" :alt="course.title" class="aspect-[16/10] w-full object-cover opacity-80" />
        <p class="mt-5 text-[10px] uppercase tracking-[0.12em] text-aula-green">Resumen</p>
        <h2 class="mt-2 font-editorial text-3xl">{{ course.title }}</h2>
        <div class="mt-5 flex items-center justify-between border-t border-white/10 pt-5">
          <span class="text-sm text-white/45">Total</span>
          <strong class="text-xl text-aula-orange">{{ course.price }}</strong>
        </div>
      </aside>
    </main>

    <PublicFooter />
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import StudentHeader from '../../components/layout/StudentHeader.vue'
import PublicFooter from '../../components/layout/PublicFooter.vue'
import { courses } from '../../data/courses.js'

const route = useRoute()
const router = useRouter()
const course = computed(() => courses.find((item) => item.slug === route.params.slug) || courses[0])
const method = ref('card')
const status = ref('')
const card = reactive({ name: '', number: '', expiry: '', cvv: '' })

function submitPurchase() {
  status.value = 'Compra realizada correctamente. El curso ya puede abrirse desde tu espacio.'
  window.setTimeout(() => router.push(`/estudiante/curso/${course.value.slug}`), 700)
}
</script>

