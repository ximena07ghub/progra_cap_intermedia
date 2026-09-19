<script setup>
import PublicHeader from '../components/layout/PublicHeader.vue'
import PublicFooter from '../components/layout/PublicFooter.vue'
import HomeHero from '../components/home/HomeHero.vue'
import HomeCourseCarousel from '../components/home/HomeCourseCarousel.vue'
import HomeHowItWorks from '../components/home/HomeHowItWorks.vue'
import HomeLearningModes from '../components/home/HomeLearningModes.vue'
import HomeVideoShowcase from '../components/home/HomeVideoShowcase.vue'
import HomeTestimonials from '../components/home/HomeTestimonials.vue'
</script>

<template>
  <div class="min-h-screen overflow-x-hidden bg-aula-bg text-aula-cream">
    <PublicHeader />

    <main>
      <HomeHero />
      <HomeCourseCarousel />
      <HomeHowItWorks />
      <HomeLearningModes />
      <HomeVideoShowcase />
      <HomeTestimonials />
    </main>

    <PublicFooter />
  </div>
</template>
