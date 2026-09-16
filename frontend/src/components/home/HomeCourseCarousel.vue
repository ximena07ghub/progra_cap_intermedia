<template>
  <div
    class="course-showcase"
    tabindex="0"
    aria-label="Cursos destacados"
    @keydown.left.prevent="goPrevious"
    @keydown.right.prevent="goNext"
  >
    <div class="showcase-heading">
      <div>
        <p class="eyebrow eyebrow--light">Selección de la semana</p>
        <h2>Cursos para descubrir<br />algo nuevo.</h2>
      </div>

      <div class="selected-course-info">
        <span class="selected-course-category">{{ activeCourse.category }}</span>
        <h3>{{ activeCourse.title }}</h3>
        <p>{{ activeCourse.description }}</p>
        <div class="selected-course-meta">
          <span>{{ activeCourse.instructor }}</span>
          <strong>{{ activeCourse.price }}</strong>
        </div>
      </div>
    </div>

    <div
      class="floating-course-track"
      @pointerdown="onPointerDown"
      @pointermove="onPointerMove"
      @pointerup="onPointerUp"
      @pointercancel="resetPointer"
    >
      <article
        v-for="(course, index) in featuredCourses"
        :key="course.id"
        class="floating-course-card"
        :class="{ 'is-selected': index === activeIndex }"
        :data-position="getRelativePosition(index)"
      >
        <button
          class="floating-course-card__button"
          type="button"
          :aria-label="`Seleccionar curso ${course.title}`"
          @click="selectCourse(index)"
        >
          <img :class="['course-visual', course.imageClass]" :src="course.image" :alt="`Ilustración relacionada con ${course.title}`" />
          <div class="floating-course-card__overlay">
            <span>{{ String(index + 1).padStart(2, '0') }} · {{ course.shortCategory }}</span>
            <h3>{{ course.title }}</h3>
          </div>
        </button>
      </article>
    </div>

    <div class="showcase-controls">
      <button class="carousel-arrow" type="button" aria-label="Curso anterior" @click="goPrevious">←</button>
      <div class="showcase-counter">
        <strong>{{ String(activeIndex + 1).padStart(2, '0') }}</strong>
        <span>/ {{ String(featuredCourses.length).padStart(2, '0') }}</span>
      </div>
      <button class="carousel-arrow" type="button" aria-label="Curso siguiente" @click="goNext">→</button>
    </div>

    <div class="selected-course-action">
      <RouterLink class="text-link text-link--light" :to="activeCourse.url">
        Conocer este curso <span aria-hidden="true">↗</span>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import imageOne from '../../assets/images/aula4.jpg'
import imageTwo from '../../assets/images/AULA (3).png'
import imageThree from '../../assets/images/aula3.jpg'
import imageFour from '../../assets/images/AULA (1).png'
import imageFive from '../../assets/images/AULA (2).png'

const router = useRouter()
const activeIndex = ref(2)
const pointerStartX = ref(null)
const pointerMoved = ref(false)

const featuredCourses = [
  { id: 1, title: 'Reset Sensorial', category: 'Bienestar', shortCategory: 'Bienestar', instructor: 'Dra. Elena Varela', price: 'Gratis', description: 'Técnicas breves para reducir la sobrecarga sensorial y recuperar concentración durante el día.', url: '/cursos/neuro-habitos', image: imageOne, imageClass: 'course-visual--one' },
  { id: 2, title: 'Psicofísica del Entorno', category: 'Diseño y bienestar', shortCategory: 'Diseño', instructor: 'Arq. Marco Ibarra', price: '$29 USD', description: 'Descubre cómo la luz, el sonido y la organización del espacio influyen en atención, estrés y descanso.', url: '/cursos/neuro-habitos', image: imageTwo, imageClass: 'course-visual--two' },
  { id: 3, title: 'Neuro-Hábitos', category: 'Bienestar', shortCategory: 'Bienestar', instructor: 'Psic. Ana Solís', price: '$39 USD', description: 'Construye rutinas sostenibles y aprende a reducir ciclos de procrastinación mediante estrategias prácticas.', url: '/cursos/neuro-habitos', image: imageThree, imageClass: 'course-visual--three' },
  { id: 4, title: 'Flexibilidad Cognitiva', category: 'Desarrollo personal', shortCategory: 'Desarrollo', instructor: 'Mtra. Julia Ríos', price: '$45 USD', description: 'Entrena nuevas formas de interpretar problemas y desarrolla respuestas más flexibles ante situaciones complejas.', url: '/cursos/neuro-habitos', image: imageFour, imageClass: 'course-visual--four' },
  { id: 5, title: 'Arquitectura Antifrágil', category: 'Bienestar', shortCategory: 'Bienestar', instructor: 'Dr. Bruno Medina', price: '$49 USD', description: 'Diseña protocolos personales de descanso, recuperación y respuesta para enfrentar periodos de mayor exigencia.', url: '/cursos/neuro-habitos', image: imageFive, imageClass: 'course-visual--five' },
]

const activeCourse = computed(() => featuredCourses[activeIndex.value])

function getRelativePosition(index) {
  const total = featuredCourses.length
  const half = Math.floor(total / 2)
  let difference = index - activeIndex.value
  if (difference > half) difference -= total
  if (difference < -half) difference += total
  return difference
}

function goTo(index) {
  activeIndex.value = (index + featuredCourses.length) % featuredCourses.length
}
function goPrevious() { goTo(activeIndex.value - 1) }
function goNext() { goTo(activeIndex.value + 1) }
function selectCourse(index) {
  if (pointerMoved.value) return
  if (index === activeIndex.value) router.push(featuredCourses[index].url)
  else goTo(index)
}
function onPointerDown(event) {
  pointerStartX.value = event.clientX
  pointerMoved.value = false
}
function onPointerMove(event) {
  if (pointerStartX.value === null) return
  if (Math.abs(event.clientX - pointerStartX.value) > 10) pointerMoved.value = true
}
function onPointerUp(event) {
  if (pointerStartX.value === null) return
  const distance = event.clientX - pointerStartX.value
  if (Math.abs(distance) > 55) distance > 0 ? goPrevious() : goNext()
  window.setTimeout(() => { pointerMoved.value = false }, 120)
  pointerStartX.value = null
}
function resetPointer() {
  pointerStartX.value = null
  pointerMoved.value = false
}
</script>

