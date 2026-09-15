<template>
  <div class="view-shell">
<!-- 
         HEADER 
          -->

    <header
        class="site-header"
        data-header
    >

        <div class="container header-inner">


            <!-- Logo AulaGo -->
            <RouterLink
                class="brand brand--logo"
                to="/"
                aria-label="Ir al inicio de AulaGo"
            >
                <img
                    class="brand-logo"
                    src="../assets/images/aula-logo.svg"
                    alt="AulaGo"
                >
            </RouterLink>


            <!-- Navegación principal -->

            <nav
                class="main-navigation"
                aria-label="Navegación principal"
            >

                <RouterLink
                    to="/"
                    aria-current="page"
                >
                    Inicio
                </RouterLink>

                <RouterLink to="/categorias">
                    Categorías
                </RouterLink>

            </nav>



            <!-- Búsqueda pública -->

            <form
                class="header-search"
                action="/buscar"
                method="get"
                role="search"
            >

                <label
                    class="sr-only"
                    for="main-search"
                >
                    Buscar cursos
                </label>

                <input
                    id="main-search"
                    name="q"
                    type="search"
                    placeholder="Buscar un curso"
                >

                <button
                    type="submit"
                    aria-label="Buscar"
                >
                    <span aria-hidden="true">
                        ↗
                    </span>
                </button>

            </form>



            <!-- Acciones de autenticación -->

            <div class="header-actions">

                <RouterLink
                    class="header-login"
                    to="/login"
                >
                    Iniciar sesión
                </RouterLink>

                <RouterLink
                    class="button button--dark button--small"
                    to="/registro"
                >
                    Registrarse
                </RouterLink>

            </div>



            <!-- Menú para dispositivos pequeños -->

            <button
                class="mobile-menu-button"
                type="button"
                aria-label="Abrir menú"
                aria-expanded="false"
                data-menu-button
            >

                <span></span>
                <span></span>

            </button>

        </div>

    </header>



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



                <!-- 
                     GALERÍA DE CURSOS DESTACADOS

                      -->

                <div
                    class="course-showcase"
                    data-course-carousel
                >


                    <!-- Encabezado del carrusel -->

                    <div class="showcase-heading">

                        <div>

                            <p class="eyebrow eyebrow--light">
                                Selección de la semana
                            </p>

                            <h2>
                                Cursos para descubrir
                                <br>
                                algo nuevo.
                            </h2>

                        </div>



                        <!-- Información del curso seleccionado -->

                        <div class="selected-course-info">

                            <span
                                class="selected-course-category"
                                data-selected-category
                            >
                                Bienestar
                            </span>

                            <h3 data-selected-title>
                                Neuro-Hábitos
                            </h3>

                            <p data-selected-description>
                                Construye rutinas sostenibles y aprende
                                a reducir ciclos de procrastinación
                                mediante estrategias prácticas.
                            </p>

                            <div class="selected-course-meta">

                                <span data-selected-instructor>
                                    Psic. Ana Solís
                                </span>

                                <strong data-selected-price>
                                    $39 USD
                                </strong>

                            </div>

                        </div>

                    </div>



                    <!-- =============================================
                         CARDS FLOTANTES

                         data-position:
                         -2  izquierda exterior
                         -1  izquierda cercana
                          0  seleccionada
                          1  derecha cercana
                          2  derecha exterior

                         main.js actualizará estas posiciones.
                         ============================================= -->

                    <div class="floating-course-track">


                        <!-- CURSO 1 -->

                        <article
                            class="floating-course-card"
                            data-course-card
                            data-position="-2"

                            data-course-title="Reset Sensorial"
                            data-course-category="Bienestar"
                            data-course-instructor="Dra. Elena Varela"
                            data-course-price="Gratis"

                            data-course-description="Técnicas breves para reducir la sobrecarga sensorial y recuperar concentración durante el día."

                            data-course-url="/cursos/neuro-habitos"
                        >

                            <button
                                class="floating-course-card__button"
                                type="button"
                                aria-label="Seleccionar curso Reset Sensorial"
                            >

                                <img
                                    class="course-visual course-visual--one"
                                    src="../assets/images/aula4.jpg"
                                    alt="Ilustración relacionada con Reset Sensorial"
                                >

                                <div class="floating-course-card__overlay">

                                    <span>
                                        01 · Bienestar
                                    </span>

                                    <h3>
                                        Reset Sensorial
                                    </h3>

                                </div>

                            </button>

                        </article>



                        <!-- CURSO 2 -->

                        <article
                            class="floating-course-card"
                            data-course-card
                            data-position="-1"

                            data-course-title="Psicofísica del Entorno"
                            data-course-category="Diseño y bienestar"
                            data-course-instructor="Arq. Marco Ibarra"
                            data-course-price="$29 USD"

                            data-course-description="Descubre cómo la luz, el sonido y la organización del espacio influyen en atención, estrés y descanso."

                            data-course-url="/cursos/neuro-habitos"
                        >

                            <button
                                class="floating-course-card__button"
                                type="button"
                                aria-label="Seleccionar curso Psicofísica del Entorno"
                            >

                                <img
                                    class="course-visual course-visual--two"
                                    src="../assets/images/AULA (3).png"
                                    alt="Ilustración relacionada con Psicofísica del Entorno"
                                >

                                <div class="floating-course-card__overlay">

                                    <span>
                                        02 · Diseño
                                    </span>

                                    <h3>
                                        Psicofísica
                                        <br>
                                        del Entorno
                                    </h3>

                                </div>

                            </button>

                        </article>



                        <!-- CURSO 3 / SELECCIONADO INICIALMENTE -->

                        <article
                            class="floating-course-card is-selected"
                            data-course-card
                            data-position="0"

                            data-course-title="Neuro-Hábitos"
                            data-course-category="Bienestar"
                            data-course-instructor="Psic. Ana Solís"
                            data-course-price="$39 USD"

                            data-course-description="Construye rutinas sostenibles y aprende a reducir ciclos de procrastinación mediante estrategias prácticas."

                            data-course-url="/cursos/neuro-habitos"
                        >

                            <button
                                class="floating-course-card__button"
                                type="button"
                                aria-label="Seleccionar curso Neuro-Hábitos"
                            >

                                <img
                                    class="course-visual course-visual--three"
                                    src="../assets/images/aula3.jpg"
                                    alt="Ilustración relacionada con Neuro-Hábitos"
                                >

                                <div class="floating-course-card__overlay">

                                    <span>
                                        03 · Bienestar
                                    </span>

                                    <h3>
                                        Neuro-Hábitos
                                    </h3>

                                </div>

                            </button>

                        </article>



                        <!-- CURSO 4 -->

                        <article
                            class="floating-course-card"
                            data-course-card
                            data-position="1"

                            data-course-title="Flexibilidad Cognitiva"
                            data-course-category="Desarrollo personal"
                            data-course-instructor="Mtra. Julia Ríos"
                            data-course-price="$45 USD"

                            data-course-description="Entrena nuevas formas de interpretar problemas y desarrolla respuestas más flexibles ante situaciones complejas."

                            data-course-url="/cursos/neuro-habitos"
                        >

                            <button
                                class="floating-course-card__button"
                                type="button"
                                aria-label="Seleccionar curso Flexibilidad Cognitiva"
                            >

                                <img
                                    class="course-visual course-visual--four"
                                    src="../assets/images/AULA (1).png"
                                    alt="Ilustración relacionada con Flexibilidad Cognitiva"
                                >

                                <div class="floating-course-card__overlay">

                                    <span>
                                        04 · Desarrollo
                                    </span>

                                    <h3>
                                        Flexibilidad
                                        <br>
                                        Cognitiva
                                    </h3>

                                </div>

                            </button>

                        </article>



                        <!-- CURSO 5 -->

                        <article
                            class="floating-course-card"
                            data-course-card
                            data-position="2"

                            data-course-title="Arquitectura Antifrágil"
                            data-course-category="Bienestar"
                            data-course-instructor="Dr. Bruno Medina"
                            data-course-price="$49 USD"

                            data-course-description="Diseña protocolos personales de descanso, recuperación y respuesta para enfrentar periodos de mayor exigencia."

                            data-course-url="/cursos/neuro-habitos"
                        >

                            <button
                                class="floating-course-card__button"
                                type="button"
                                aria-label="Seleccionar curso Arquitectura Antifrágil"
                            >

                                <img
                                    class="course-visual course-visual--five"
                                    src="../assets/images/AULA (2).png"
                                    alt="Ilustración relacionada con Arquitectura Antifrágil"
                                >

                                <div class="floating-course-card__overlay">

                                    <span>
                                        05 · Bienestar
                                    </span>

                                    <h3>
                                        Arquitectura
                                        <br>
                                        Antifrágil
                                    </h3>

                                </div>

                            </button>

                        </article>

                    </div>



                    <!-- Controles del carrusel -->

                    <div class="showcase-controls">

                        <button
                            class="carousel-arrow"
                            type="button"
                            aria-label="Curso anterior"
                            data-course-prev
                        >
                            ←
                        </button>


                        <div class="showcase-counter">

                            <strong data-course-current>
                                03
                            </strong>

                            <span>
                                / 05
                            </span>

                        </div>


                        <button
                            class="carousel-arrow"
                            type="button"
                            aria-label="Curso siguiente"
                            data-course-next
                        >
                            →
                        </button>

                    </div>


                    <!-- CTA del curso actualmente seleccionado -->

                    <div class="selected-course-action">

                        <RouterLink
                            class="text-link text-link--light"
                            to="/cursos/neuro-habitos"
                            data-selected-link
                        >
                            Conocer este curso
                            <span aria-hidden="true">
                                ↗
                            </span>
                        </RouterLink>

                    </div>

                </div>

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

             La palabra seleccionada será más grande.

             Las imágenes visibles también cambiarán según
             la selección.

             main.js implementará esta interacción.

             IMPORTANTE:
             Las pantallas de perfil todavía NO se construyen.
             Dejamos las rutas futuras documentadas mediante
             data-route / data-future-route.
             ===================================================== -->

        <section
            class="experience-explorer"
            aria-labelledby="experience-title"
        >

            <div class="container experience-layout">


                <!-- Galería que cambiará según la selección -->

                <div class="experience-preview">


                    <!-- PREVIEW: MIS CURSOS -->

                    <div
                        class="experience-preview-set"
                        data-preview-set="mis-cursos"
                        hidden
                    >

                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--three"
                                src="../assets/images/AULA (1).png"
                                alt="Curso en progreso"
                            >

                        </figure>


                        <figure class="experience-image experience-image--large">

                            <img
                                class="course-visual course-visual--two"
                                src="../assets/images/AULA (1).png"
                                alt="Cursos actuales"
                            >

                        </figure>


                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--four"
                                src="../assets/images/AULA (1).png"
                                alt="Otro curso en progreso"
                            >

                        </figure>

                    </div>



                    <!-- PREVIEW: GUARDADOS -->

                    <div
                        class="experience-preview-set"
                        data-preview-set="guardados"
                        hidden
                    >

                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--five"
                                src="../assets/images/AULA (1).png"
                                alt="Curso guardado"
                            >

                        </figure>


                        <figure class="experience-image experience-image--large">

                            <img
                                class="course-visual course-visual--one"
                                src="../assets/images/AULA (1).png"
                                alt="Cursos guardados"
                            >

                        </figure>


                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--three"
                                src="../assets/images/AULA (1).png"
                                alt="Otro curso guardado"
                            >

                        </figure>

                    </div>



                    <!-- PREVIEW: POPULAR / SELECCIÓN INICIAL -->

                    <div
                        class="experience-preview-set is-active"
                        data-preview-set="popular"
                    >

                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--two"
                                src="../assets/images/AULA (3).png"
                                alt="Curso popular de diseño"
                            >

                        </figure>


                        <figure class="experience-image experience-image--large">

                            <img
                                class="course-visual course-visual--three"
                                src="../assets/images/AULA (1).png"
                                alt="Curso popular Neuro-Hábitos"
                            >

                        </figure>


                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--five"
                                src="../assets/images/AULA (1).png"
                                alt="Curso popular de bienestar"
                            >

                        </figure>

                    </div>



                    <!-- PREVIEW: NUEVOS -->

                    <div
                        class="experience-preview-set"
                        data-preview-set="nuevos"
                        hidden
                    >

                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--four"
                                src="../assets/images/AULA (1).png"
                                alt="Curso nuevo"
                            >

                        </figure>


                        <figure class="experience-image experience-image--large">

                            <img
                                class="course-visual course-visual--five"
                                src="../assets/images/AULA (1).png"
                                alt="Curso nuevo destacado"
                            >

                        </figure>


                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--one"
                                src="../assets/images/AULA (1).png"
                                alt="Otro curso nuevo"
                            >

                        </figure>

                    </div>



                    <!-- PREVIEW: MI CUENTA -->

                    <div
                        class="experience-preview-set"
                        data-preview-set="mi-cuenta"
                        hidden
                    >

                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--one"
                                src="../assets/images/AULA (1).png"
                                alt="Actividad reciente del usuario"
                            >

                        </figure>


                        <figure class="experience-image experience-image--large experience-image--account">

                            <!-- Avatar temporal.
                                 Después vendrá del usuario autenticado. -->

                            <div class="account-preview">

                                <span class="account-preview__avatar">
                                    AS
                                </span>

                                <span>
                                    Mi perfil
                                </span>

                            </div>

                        </figure>


                        <figure class="experience-image experience-image--small">

                            <img
                                class="course-visual course-visual--three"
                                src="../assets/images/AULA (1).png"
                                alt="Curso reciente del usuario"
                            >

                        </figure>

                    </div>

                </div>



                <!-- Selector grande -->

                <div class="experience-navigation">


                    <div class="experience-heading">

                        <p class="eyebrow eyebrow--light">
                            Explora tu espacio
                        </p>

                        <h2 id="experience-title">
                            Todo lo que aprendes,
                            en un mismo lugar.
                        </h2>

                    </div>



                    <div
                        class="experience-options"
                        role="navigation"
                        aria-label="Secciones personales"
                    >


                        <!-- Estas rutas se implementarán cuando
                             construyamos el perfil del estudiante. -->

                        <button
                            type="button"
                            data-experience-option="mis-cursos"
                            data-route="#"
                            data-future-route="/estudiante?tab=progreso"
                        >
                            Mis cursos
                        </button>



                        <button
                            type="button"
                            data-experience-option="guardados"
                            data-route="#"
                            data-future-route="/estudiante?tab=guardados"
                        >
                            Guardados
                        </button>



                        <!-- Esta opción empieza seleccionada -->

                        <button
                            class="is-active"
                            type="button"
                            data-experience-option="popular"
                            data-route="/cursos?filtro=popular"
                        >
                            Popular
                        </button>



                        <button
                            type="button"
                            data-experience-option="nuevos"
                            data-route="/cursos?filtro=nuevos"
                        >
                            Nuevos
                        </button>



                        <!-- PHP decidirá más adelante si debe
                             abrir login, perfil estudiante o
                             perfil instructor. -->

                        <button
                            type="button"
                            data-experience-option="mi-cuenta"
                            data-route="#"
                            data-future-route="perfil-segun-sesion"
                        >
                            Mi cuenta
                        </button>

                    </div>



                    <p class="experience-hint">
                        Selecciona una opción para explorar
                        una parte diferente de AulaGo.
                    </p>

                </div>

            </div>

        </section>

    </main>



    <!-- 
         FOOTER
          -->

    <footer class="site-footer">

        <div class="container footer-layout">


            <div class="footer-brand">

                <!-- Logo principal de AulaGo -->
                <RouterLink
                    class="brand brand--logo"
                    to="/"
                    aria-label="Ir al inicio de AulaGo"
                >
                    <img
                        class="brand-logo"
                        src="../assets/images/aula-logo.svg"
                        alt="AulaGo"
                    >
                </RouterLink>

                <p>
                    Aprende a tu ritmo.
                </p>

            </div>



            <nav
                class="footer-navigation"
                aria-label="Navegación secundaria"
            >

                <div>

                    <span>
                        Explorar
                    </span>

                    <RouterLink to="/cursos">
                        Cursos
                    </RouterLink>

                    <RouterLink to="/categorias">
                        Categorías
                    </RouterLink>

                </div>


                <div>

                    <span>
                        Cuenta
                    </span>

                    <RouterLink to="/login">
                        Iniciar sesión
                    </RouterLink>

                    <RouterLink to="/registro">
                        Registrarse
                    </RouterLink>

                </div>

            </nav>



            <p class="footer-copy">
                © 2026 AulaGo
            </p>

        </div>

    </footer>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import { initMainInteractions } from '../scripts/mainInteraction.js'

onMounted(() => {
  document.body.className = "home-page"
  initMainInteractions()
})

onBeforeUnmount(() => {
  document.body.className = ''
})
</script>
