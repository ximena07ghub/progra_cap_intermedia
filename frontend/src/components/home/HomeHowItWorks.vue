<script setup>
const steps = [
  { number: '01', title: 'Encuentra un curso', text: 'Explora categorías, cursos populares, novedades o utiliza el buscador.', dot: 'bg-aula-orange', numberClass: 'text-aula-orange-soft' },
  { number: '02', title: 'Inscríbete', text: 'Regístrate en un curso gratuito o selecciona una forma de pago cuando corresponda.', dot: 'bg-[#79b8c7]', numberClass: 'text-[#79b8c7]' },
  { number: '03', title: 'Avanza por niveles', text: 'Consulta videos, lecturas y actividades mientras el portal registra tu progreso.', dot: 'bg-aula-green', numberClass: 'text-aula-green' },
  { number: '04', title: 'Completa el curso', text: 'Finaliza los niveles y revisa el curso terminado desde tu espacio de aprendizaje.', dot: 'bg-[#a66f91]', numberClass: 'text-[#c291af]' },
  { number: '05', title: 'Obtén tu certificado', text: 'Cuando completes la ruta, tendrás disponible el certificado correspondiente.', dot: 'bg-[#c8756f]', numberClass: 'text-[#d99a94]' },
]
</script>

<template>
  <section id="como-funciona" class="relative overflow-hidden border-b border-white/10 bg-aula-bg px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="how-it-works-title">
    <div class="pointer-events-none absolute left-0 top-1/4 h-72 w-72 rounded-full bg-aula-green/[0.035] blur-3xl"></div>

    <div class="relative mx-auto grid max-w-aula gap-14 lg:grid-cols-[0.78fr_1.22fr] lg:gap-20">
      <div class="lg:sticky lg:top-28 lg:self-start">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-green">Tu ruta de aprendizaje</p>
        <h2 id="how-it-works-title" class="mt-4 max-w-xl font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">
          Aprende a tu ritmo,
          <span class="block text-aula-orange">nivel por nivel.</span>
        </h2>
        <p class="mt-6 max-w-lg text-sm leading-7 text-white/45 sm:text-base sm:leading-8">
          Un recorrido continuo, no una fila de tarjetas. Cada etapa aparece sobre la misma línea para que el progreso se sienta claro y natural.
        </p>
        <RouterLink to="/cursos" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-aula-orange-soft transition hover:text-aula-cream">
          Ver todas las rutas <span aria-hidden="true">↗</span>
        </RouterLink>
      </div>

      <div class="relative pl-10 sm:pl-14" aria-label="Proceso para completar un curso">
        <div class="absolute bottom-5 left-[15px] top-5 w-px bg-gradient-to-b from-aula-orange/50 via-white/15 to-aula-green/45 sm:left-[19px]"></div>

        <article
          v-for="step in steps"
          :key="step.number"
          class="relative border-b border-white/10 py-8 first:pt-0 last:border-b-0 last:pb-0"
        >
          <span
            :class="['absolute -left-10 top-9 h-3.5 w-3.5 rounded-full ring-[7px] ring-aula-bg sm:-left-14 sm:h-4 sm:w-4', step.dot]"
            aria-hidden="true"
          ></span>

          <div class="grid gap-3 sm:grid-cols-[58px_1fr] sm:gap-6">
            <span :class="['text-[11px] font-bold tracking-[0.16em]', step.numberClass]">{{ step.number }}</span>
            <div>
              <h3 class="text-lg font-bold text-aula-cream sm:text-xl">{{ step.title }}</h3>
              <p class="mt-2 max-w-xl text-sm leading-7 text-white/42">{{ step.text }}</p>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
