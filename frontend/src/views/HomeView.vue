<template>
  <div class="view-shell">
<!-- 
         HEADER 
          -->

    <PublicHeader />



    <main>


        <!-- =====================================================
             HERO

          -->

        <section
            class="hero"
            aria-labelledby="hero-title"
        >

            <div class="container">


                <!-- Introducción editorial -->

                <div class="hero-intro">


                    <div class="hero-heading">

                        <p class="eyebrow">
                            Aprendizaje autodidacta
                        </p>

                        <h1 id="hero-title">
                            Aprende algo
                            <br>
                            que despierte

                            <em>
                                tu curiosidad.
                            </em>
                        </h1>

                    </div>



                    <div class="hero-copy">

                        <p>
                            Explora cursos diseñados para avanzar
                            a tu ritmo, combinando contenido visual,
                            niveles claros y actividades que hacen
                            del aprendizaje una experiencia más
                            personal.
                        </p>

                        <RouterLink
                            class="button button--accent"
                            to="/cursos"
                        >
                            VER MAS
                        </RouterLink>

                    </div>

                </div>



                <!-- Carrusel migrado a Vue reactivo -->
                <HomeCourseCarousel />

            </div>

        </section>



        <!-- =====================================================
             SECCIÓN 2 — CÓMO FUNCIONAN LOS CURSOS

             Izquierda:
             explicación general.

             Derecha:
             línea de tiempo del recorrido.

             Abajo:
             contenidos que puede incluir cada curso.
             ===================================================== -->

        <section
            class="course-process section-space"
            aria-labelledby="process-title"
        >

            <div class="container">


                <div class="process-layout">


                    <!-- Introducción -->

                    <div class="process-intro">

                        <p class="eyebrow">
                            Tu ruta de aprendizaje
                        </p>

                        <h2 id="process-title">
                            Aprende a tu ritmo,
                            <em>
                                nivel por nivel.
                            </em>
                        </h2>

                        <p>
                            Cada curso está diseñado para realizarse
                            de forma autodidacta. Puedes avanzar a
                            través de distintos niveles, consultar
                            contenido complementario y registrar tu
                            progreso hasta finalizar el recorrido.
                        </p>


                        <RouterLink
                            class="text-link"
                            to="/cursos"
                        >
                            SDSFGF
                            <span aria-hidden="true">
                                ↗
                            </span>
                        </RouterLink>

                    </div>



                    <!-- Línea del tiempo -->

                    <div
                        class="learning-timeline"
                        aria-label="Proceso para completar un curso"
                    >


                        <article class="timeline-step">

                            <span class="timeline-step__number">
                                01
                            </span>

                            <div>

                                <h3>
                                    Encuentra un curso
                                </h3>

                                <p>
                                    Explora categorías, cursos populares,
                                    novedades o utiliza el buscador.
                                </p>

                            </div>

                        </article>



                        <article class="timeline-step">

                            <span class="timeline-step__number">
                                02
                            </span>

                            <div>

                                <h3>
                                    Inscríbete
                                </h3>

                                <p>
                                    Regístrate en un curso gratuito o
                                    selecciona una forma de pago cuando
                                    tenga costo.
                                </p>

                            </div>

                        </article>



                        <article class="timeline-step">

                            <span class="timeline-step__number">
                                03
                            </span>

                            <div>

                                <h3>
                                    Avanza por niveles
                                </h3>

                                <p>
                                    Consulta videos, material y actividades
                                    mientras el portal registra tu progreso.
                                </p>

                            </div>

                        </article>



                        <article class="timeline-step">

                            <span class="timeline-step__number">
                                04
                            </span>

                            <div>

                                <h3>
                                    Completa el curso
                                </h3>

                                <p>
                                    Finaliza todos los niveles y consulta
                                    el curso terminado desde tu Kardex.
                                </p>

                            </div>

                        </article>



                        <article class="timeline-step">

                            <span class="timeline-step__number">
                                05
                            </span>

                            <div>

                                <h3>
                                    Obtén tu certificado
                                </h3>

                                <p>
                                    Al finalizar satisfactoriamente,
                                    el sistema generará tu certificado.
                                </p>

                            </div>

                        </article>

                    </div>

                </div>



                <!-- =============================================
                     QUÉ PUEDE INCLUIR UN CURSO

                     Estos elementos corresponden al tipo de
                     contenido contemplado por el proyecto.
                     ============================================= -->

                <div class="course-content-heading">

                    <p class="eyebrow">
                        Dentro de cada curso
                    </p>

                    <h2>
                        Diferentes formas de aprender.
                    </h2>

                </div>


                <div class="course-content-grid">


                    <article class="content-feature">

                        <span class="content-feature__icon">
                            ▶
                        </span>

                        <div>

                            <span class="content-feature__number">
                                01
                            </span>

                            <h3>
                                Videos por nivel
                            </h3>

                            <p>
                                Cada nivel contará con contenido audiovisual
                                que acompañe el tema principal.
                            </p>

                        </div>

                    </article>



                    <article class="content-feature">

                        <span class="content-feature__icon">
                            Aa
                        </span>

                        <div>

                            <span class="content-feature__number">
                                02
                            </span>

                            <h3>
                                Lecturas y recursos
                            </h3>

                            <p>
                                Texto, imágenes, documentos PDF y enlaces
                                que permiten ampliar cada tema.
                            </p>

                        </div>

                    </article>



                    <article class="content-feature">

                        <span class="content-feature__icon">
                            +
                        </span>

                        <div>

                            <span class="content-feature__number">
                                03
                            </span>

                            <h3>
                                Actividades
                            </h3>

                            <p>
                                Ejercicios y propuestas relacionadas con
                                el tema para llevar el aprendizaje a la práctica.
                            </p>

                        </div>

                    </article>



                    <article class="content-feature">

                        <span class="content-feature__icon">
                            ◫
                        </span>

                        <div>

                            <span class="content-feature__number">
                                04
                            </span>

                            <h3>
                                Progreso personal
                            </h3>

                            <p>
                                Consulta tus niveles completados y continúa
                                exactamente desde donde te quedaste.
                            </p>

                        </div>

                    </article>

                </div>

            </div>

        </section>



        <!-- =====================================================
             SECCIÓN 3 — NAVEGACIÓN VISUAL

             Interacción migrada a Vue reactivo dentro de
             HomeExperienceExplorer.vue.
             ===================================================== -->

        <HomeExperienceExplorer />

    </main>



    <!-- 
         FOOTER
          -->

    <PublicFooter />
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import PublicHeader from '../components/layout/PublicHeader.vue'
import PublicFooter from '../components/layout/PublicFooter.vue'
import HomeCourseCarousel from '../components/home/HomeCourseCarousel.vue'
import HomeExperienceExplorer from '../components/home/HomeExperienceExplorer.vue'

onMounted(() => {
  document.body.className = 'home-page'
})

onBeforeUnmount(() => {
  document.body.className = ''
})
</script>
