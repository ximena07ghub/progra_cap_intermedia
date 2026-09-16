<template>
  <section class="experience-explorer" aria-labelledby="experience-title">
    <div class="container experience-layout">
      <div class="experience-preview">
        <div class="experience-preview-set is-active">
          <figure
            v-for="(image, index) in activePreview.images"
            :key="`${activeKey}-${index}`"
            :class="['experience-image', index === 1 ? 'experience-image--large' : 'experience-image--small', activeKey === 'mi-cuenta' && index === 1 ? 'experience-image--account' : '']"
          >
            <div v-if="activeKey === 'mi-cuenta' && index === 1" class="account-preview">
              <span class="account-preview__avatar">{{ initials }}</span>
              <span>Mi perfil</span>
            </div>
            <img v-else :class="['course-visual', image.className]" :src="image.src" :alt="image.alt" />
          </figure>
        </div>
      </div>

      <div class="experience-navigation">
        <div class="experience-heading">
          <p class="eyebrow eyebrow--light">Explora tu espacio</p>
          <h2 id="experience-title">Todo lo que aprendes,<br />en un mismo lugar.</h2>
        </div>

        <div class="experience-options" role="navigation" aria-label="Secciones personales">
          <button
            v-for="option in options"
            :key="option.key"
            type="button"
            :class="{ 'is-active': option.key === activeKey }"
            @click="handleOption(option)"
          >
            {{ option.label }}
          </button>
        </div>

        <p class="experience-hint">{{ activePreview.description }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import imageOne from '../../assets/images/AULA (1).png'
import imageTwo from '../../assets/images/AULA (3).png'
import { useSessionStore } from '../../stores/session.js'

const router = useRouter()
const session = useSessionStore()
const activeKey = ref('popular')

const options = [
  { key: 'mis-cursos', label: 'Mis cursos' },
  { key: 'guardados', label: 'Guardados' },
  { key: 'popular', label: 'Popular', route: '/cursos?filtro=popular' },
  { key: 'nuevos', label: 'Nuevos', route: '/cursos?filtro=nuevos' },
  { key: 'mi-cuenta', label: 'Mi cuenta' },
]

const previews = {
  'mis-cursos': {
    description: 'Consulta tus cursos en progreso, finalizados y pendientes.',
    images: [
      { src: imageOne, className: 'course-visual--three', alt: 'Curso en progreso' },
      { src: imageOne, className: 'course-visual--two', alt: 'Cursos actuales' },
      { src: imageOne, className: 'course-visual--four', alt: 'Otro curso en progreso' },
    ],
  },
  guardados: {
    description: 'Encuentra los cursos que guardaste para revisarlos después.',
    images: [
      { src: imageOne, className: 'course-visual--five', alt: 'Curso guardado' },
      { src: imageOne, className: 'course-visual--one', alt: 'Cursos guardados' },
      { src: imageOne, className: 'course-visual--three', alt: 'Otro curso guardado' },
    ],
  },
  popular: {
    description: 'Descubre los cursos con mayor interés y participación dentro de AulaGo.',
    images: [
      { src: imageTwo, className: 'course-visual--two', alt: 'Curso popular de diseño' },
      { src: imageOne, className: 'course-visual--three', alt: 'Curso popular Neuro-Hábitos' },
      { src: imageOne, className: 'course-visual--five', alt: 'Curso popular de bienestar' },
    ],
  },
  nuevos: {
    description: 'Explora los cursos publicados recientemente por los instructores.',
    images: [
      { src: imageOne, className: 'course-visual--four', alt: 'Curso nuevo' },
      { src: imageOne, className: 'course-visual--five', alt: 'Curso nuevo destacado' },
      { src: imageOne, className: 'course-visual--one', alt: 'Otro curso nuevo' },
    ],
  },
  'mi-cuenta': {
    description: 'Accede a tu perfil y administra la información de tu cuenta.',
    images: [
      { src: imageOne, className: 'course-visual--one', alt: 'Actividad reciente del usuario' },
      { src: imageOne, className: 'course-visual--one', alt: 'Perfil del usuario' },
      { src: imageOne, className: 'course-visual--three', alt: 'Curso reciente del usuario' },
    ],
  },
}

const activePreview = computed(() => previews[activeKey.value])
const initials = computed(() => {
  const name = session.user?.name || 'AulaGo'
  return name.split(' ').slice(0, 2).map((part) => part[0]).join('').toUpperCase()
})

function handleOption(option) {
  if (option.key !== activeKey.value) {
    activeKey.value = option.key
    return
  }

  if (option.route) {
    router.push(option.route)
    return
  }

  if (option.key === 'mis-cursos') {
    if (session.authenticated) router.push(session.role === 'instructor' ? '/instructor/cursos' : session.role === 'administrador' ? '/admin' : '/estudiante?tab=progreso')
    else router.push({ name: 'login', query: { redirect: '/estudiante?tab=progreso' } })
    return
  }

  if (option.key === 'guardados') {
    if (session.authenticated) router.push(session.role === 'instructor' ? '/instructor/cursos' : session.role === 'administrador' ? '/admin' : '/estudiante?tab=guardados')
    else router.push({ name: 'login', query: { redirect: '/estudiante?tab=guardados' } })
    return
  }

  if (option.key === 'mi-cuenta') {
    router.push(session.authenticated ? session.accountRoute : '/login')
  }
}
</script>

