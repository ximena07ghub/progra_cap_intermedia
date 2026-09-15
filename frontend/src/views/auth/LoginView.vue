<template>
  <div class="view-shell">
<main class="auth-shell auth-shell--login">


        <!-- ==================================================
             PANEL VISUAL

             La imagen ya NO vive en el HTML.
             forms.css la utilizará como fondo integrado.
             ================================================== -->

        <section class="auth-art-panel auth-art-panel--login">


            <div class="auth-art-panel__top">

                <!-- Logo real -->
                <RouterLink
                    to="/"
                    class="auth-logo"
                    aria-label="Volver a AulaGo"
                >
                    <img
                        src="../../assets/images/aula-logo.svg"
                        alt="AulaGo"
                    >
                </RouterLink>


                <RouterLink
                    to="/"
                    class="auth-back"
                >
                    ← Volver al inicio
                </RouterLink>

            </div>


                <div class="auth-art-panel__content">

                    <p class="eyebrow">
                        Continúa aprendiendo
                    </p>

                    <h1>
                        Regresa a lo que

                        <em>
                            despertó tu curiosidad.
                        </em>
                    </h1>

                    <!-- Texto secundario del hero -->
                    <p class="auth-hero-description">
                        Retoma tus cursos, continúa tu progreso
                        y descubre qué puedes aprender después.
                    </p>

                    <p class ="auth-hero-description">
                    Tu próxima idea puede empezar justo donde te quedaste.                   
                 </p>

                </div>



          

        </section>



        <!-- ==================================================
             FORMULARIO
             ================================================== -->

        <section
            class="auth-form-panel"
            aria-labelledby="login-title"
        >

            <div class="auth-form-wrapper">


                <div class="auth-form-heading">

                    <p class="eyebrow">
                        Acceso
                    </p>

                    <h2 id="login-title">
                        Iniciar sesión
                    </h2>

                    <p>
                        Ingresa tus datos para continuar.
                    </p>

                </div>



                <form
                    class="auth-form"
                    id="login-form"
                    novalidate
                >


                    <!-- Correo -->

                    <label class="form-field">

                        <span class="form-label">
                            Correo electrónico
                        </span>

                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            placeholder="nombre@correo.com"
                            autocomplete="email"
                            required
                        >

                        <small
                            class="form-error"
                            data-error-for="login-email"
                        ></small>

                    </label>



                    <!-- Contraseña -->

                    <label class="form-field">

                        <span class="form-label">
                            Contraseña
                        </span>


                        <div class="password-input">

                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="Tu contraseña"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="login-password"
                                aria-label="Mostrar contraseña"
                            >
                                Ver
                            </button>

                        </div>


                        <small
                            class="form-error"
                            data-error-for="login-password"
                        ></small>

                    </label>



                    <div class="login-options">

                        <label class="checkbox-option">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>
                                Recordarme
                            </span>

                        </label>


                        <a href="#">
                            ¿Olvidaste tu contraseña?
                        </a>

                    </div>



                    <button
                        class="auth-submit"
                        type="submit"
                    >
                        Iniciar sesión

                        <span>
                            →
                        </span>
                    </button>

                </form>



                <div class="auth-switch">

                    <span>
                        ¿Todavía no tienes una cuenta?
                    </span>

                    <RouterLink to="/registro">
                        Crear cuenta
                    </RouterLink>

                </div>

            </div>

        </section>

    </main>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { initAuthInteractions } from '../../scripts/authInteraction.js'
import { startDemoSession } from '../../utils/demoSession.js'

const route = useRoute()
const router = useRouter()

function handleLoginSuccess() {
  startDemoSession()
  const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : '/estudiante'
  router.push(redirect)
}

onMounted(() => {
  document.body.className = "auth-page"
  window.addEventListener('aulago:login-success', handleLoginSuccess)
  initAuthInteractions()
})

onBeforeUnmount(() => {
  window.removeEventListener('aulago:login-success', handleLoginSuccess)
  document.body.className = ''
})
</script>
