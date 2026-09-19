<script setup>
import { computed, ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { A11y, EffectCoverflow, Keyboard } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/effect-coverflow'

import { courses } from '../../data/courses.js'

const modules = [EffectCoverflow, Keyboard, A11y]
const swiperInstance = ref(null)
const activeIndex = ref(1)

const featuredCourses = computed(() => courses.slice(0, 7))
const activeCourse = computed(() => featuredCourses.value[activeIndex.value] || featuredCourses.value[0])

function registerSwiper(swiper) {
  swiperInstance.value = swiper
  activeIndex.value = swiper.realIndex
}

function handleSlideChange(swiper) {
  activeIndex.value = swiper.realIndex
}

function selectSlide(index) {
  swiperInstance.value?.slideTo(index)
}

function goPrevious() {
  swiperInstance.value?.slidePrev()
}

function goNext() {
  swiperInstance.value?.slideNext()
}
</script>

<template>
  <section id="cursos-destacados" class="relative overflow-hidden border-b border-white/10 bg-aula-bg-soft px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="courses-title">
    <div class="pointer-events-none absolute -right-32 top-20 h-72 w-72 rounded-full bg-brand-blue/[0.045] blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 left-[20%] h-80 w-80 rounded-full bg-brand-orange/[0.035] blur-3xl"></div>

    <div class="relative mx-auto max-w-aula">
      <div class="grid gap-7 lg:grid-cols-[1fr_.58fr] lg:items-end">
        <div class="max-w-3xl">
          <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-orange-soft">Selección para explorar</p>
          <h2 id="courses-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">
            Cursos para descubrir algo nuevo.
          </h2>
        </div>
        <p class="max-w-md text-sm leading-7 text-white/45 lg:justify-self-end">
          Selecciona una tarjeta lateral para llevarla al centro. El curso activo muestra sus detalles sin salir de la sección.
        </p>
      </div>

      <div class="mt-12 grid items-center gap-9 lg:grid-cols-[minmax(0,1.7fr)_minmax(290px,0.72fr)]">
        <div class="min-w-0">
          <Swiper
            :modules="modules"
            effect="coverflow"
            :centered-slides="true"
            :slide-to-clicked-slide="true"
            :grab-cursor="true"
            :initial-slide="1"
            :slides-per-view="1.14"
            :space-between="16"
            :keyboard="{ enabled: true }"
            :coverflow-effect="{
              rotate: 0,
              stretch: 0,
              depth: 155,
              modifier: 1.28,
              scale: 0.84,
              slideShadows: false,
            }"
            :breakpoints="{
              640: { slidesPerView: 1.72, spaceBetween: 20 },
              1024: { slidesPerView: 2.32, spaceBetween: 24 },
            }"
            class="pb-7"
            @swiper="registerSwiper"
            @slideChange="handleSlideChange"
          >
            <SwiperSlide v-for="(course, index) in featuredCourses" :key="course.id">
              <button
                type="button"
                class="group block w-full overflow-hidden rounded-[1.8rem] border border-white/10 bg-aula-surface text-left shadow-aula transition duration-300 hover:border-white/20"
                :aria-label="`Seleccionar ${course.title}`"
                @click="selectSlide(index)"
              >
                <div class="relative aspect-[4/3] overflow-hidden bg-aula-green-dark">
                  <img
                    :src="course.image"
                    :alt="course.title"
                    class="h-full w-full object-cover opacity-80 transition duration-500 group-hover:scale-105 group-hover:opacity-95"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-aula-bg via-aula-bg/15 to-transparent"></div>
                  <div class="absolute inset-0 bg-aula-plum/5 mix-blend-multiply"></div>

                  <span class="absolute left-5 top-5 rounded-full border border-white/10 bg-aula-bg/65 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-aula-cream/80 backdrop-blur">
                    {{ course.category }}
                  </span>

                  <div class="absolute inset-x-0 bottom-0 p-6">
                    <p class="text-[11px] font-semibold text-aula-orange-soft">{{ course.accent }}</p>
                    <h3 class="mt-2 font-editorial text-3xl font-semibold leading-[1.02] text-aula-cream">{{ course.title }}</h3>
                  </div>
                </div>

                <div class="flex items-center justify-between gap-4 p-5">
                  <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-aula-yellow" aria-hidden="true">
                      <path d="m12 2.8 2.75 5.57 6.15.9-4.45 4.33 1.05 6.12L12 16.83l-5.5 2.89 1.05-6.12L3.1 9.27l6.15-.9L12 2.8Z" />
                    </svg>
                    <span class="text-sm font-bold text-aula-cream">{{ course.rating }}</span>
                  </div>
                  <span class="text-xs text-white/35">{{ course.duration }}</span>
                </div>
              </button>
            </SwiperSlide>
          </Swiper>

          <div class="mt-2 flex items-center justify-center gap-4">
            <button
              type="button"
              class="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.02] text-aula-cream transition hover:border-aula-orange/60 hover:bg-white/[0.04]"
              aria-label="Curso anterior"
              @click="goPrevious"
            >
              ←
            </button>

            <span class="min-w-16 text-center text-xs font-bold text-white/35">
              {{ String(activeIndex + 1).padStart(2, '0') }} / {{ String(featuredCourses.length).padStart(2, '0') }}
            </span>

            <button
              type="button"
              class="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.02] text-aula-cream transition hover:border-aula-orange/60 hover:bg-white/[0.04]"
              aria-label="Curso siguiente"
              @click="goNext"
            >
              →
            </button>
          </div>
        </div>

        <aside class="rounded-[1.8rem] border border-white/10 bg-white/[0.025] p-7 backdrop-blur lg:p-8">
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#79b8c7]">Curso seleccionado</p>
          <h3 class="mt-4 font-editorial text-3xl font-semibold leading-tight text-aula-cream">{{ activeCourse.title }}</h3>
          <p class="mt-5 text-sm leading-7 text-white/48">{{ activeCourse.description }}</p>

          <div class="mt-7 space-y-3 border-t border-white/10 pt-6 text-sm">
            <div class="flex justify-between gap-5">
              <span class="text-white/35">Nivel</span>
              <span class="font-semibold text-white/75">{{ activeCourse.level }}</span>
            </div>
            <div class="flex justify-between gap-5">
              <span class="text-white/35">Instructor</span>
              <span class="text-right font-semibold text-white/75">{{ activeCourse.instructor }}</span>
            </div>
            <div class="flex justify-between gap-5">
              <span class="text-white/35">Bienestar relacionado</span>
              <span class="text-right font-semibold text-white/75">{{ activeCourse.wellbeingFocus }}</span>
            </div>
          </div>

          <RouterLink
            :to="`/cursos/${activeCourse.slug}`"
            class="mt-8 inline-flex min-h-12 w-full items-center justify-center rounded-full border border-aula-orange/60 bg-aula-orange/10 px-5 text-sm font-bold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg focus:outline-none focus:ring-2 focus:ring-aula-orange focus:ring-offset-2 focus:ring-offset-aula-bg-soft"
          >
            Conocer el curso
          </RouterLink>
        </aside>
      </div>
    </div>
  </section>
</template>
