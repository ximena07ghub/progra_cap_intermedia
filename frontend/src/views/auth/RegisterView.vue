<template>
  <div class="view-shell">
<main class="auth-shell auth-shell--register">


        <!-- ==================================================
             FORMULARIO DE REGISTRO

             En escritorio usamos más espacio horizontal para
             evitar una página excesivamente larga.
             ================================================== -->

        <section
            class="auth-form-panel auth-form-panel--register"
            aria-labelledby="register-title"
        >

            <div class="register-form-wrapper">


                <div class="register-topbar">

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
                        ← Volver
                    </RouterLink>

                </div>



                <div class="auth-form-heading">

                    <p class="eyebrow">
                        Únete a AulaGo
                    </p>

                    <h1 id="register-title">
                        Crea tu cuenta.
                    </h1>

                    <p>
                        Elige cómo participarás dentro del portal
                        y completa tus datos básicos.
                    </p>

                </div>



                <form
                    class="register-form"
                    id="register-form"
                    novalidate
                >


                    <!-- =========================================
                         INFORMACIÓN PERSONAL
                         ========================================= -->

                    <div class="form-section">

                        <div class="form-section-heading">

                            <span>
                                01
                            </span>

                            <h2>
                                Información personal
                            </h2>

                        </div>


                        <div class="form-grid">


                            <!-- Nombre -->

                            <label class="form-field">

                                <span class="form-label">
                                    Nombre completo
                                </span>

                                <input
                                    type="text"
                                    id="register-name"
                                    name="nombre"
                                    placeholder="Tu nombre completo"
                                    autocomplete="name"
                                    required
                                >

                                <small
                                    class="form-error"
                                    data-error-for="register-name"
                                ></small>

                            </label>



                            <!-- Email -->

                            <label class="form-field">

                                <span class="form-label">
                                    Correo electrónico
                                </span>

                                <input
                                    type="email"
                                    id="register-email"
                                    name="email"
                                    placeholder="nombre@correo.com"
                                    autocomplete="email"
                                    required
                                >

                                <small
                                    class="form-error"
                                    data-error-for="register-email"
                                ></small>

                            </label>



                            <!-- Fecha de nacimiento -->

                            <label class="form-field">

                                <span class="form-label">
                                    Fecha de nacimiento
                                </span>

                                <input
                                    type="date"
                                    id="register-birthdate"
                                    name="fecha_nacimiento"
                                    required
                                >

                                <small
                                    class="form-error"
                                    data-error-for="register-birthdate"
                                ></small>

                            </label>



                            <!-- Género -->

                            <label class="form-field">

                                <span class="form-label">
                                    Género
                                </span>

                                <select
                                    id="register-gender"
                                    name="genero"
                                    required
                                >

                                    <option value="">
                                        Selecciona
                                    </option>

                                    <option value="mujer">
                                        Mujer
                                    </option>

                                    <option value="hombre">
                                        Hombre
                                    </option>

                                    <option value="otro">
                                        Otro
                                    </option>

                                    <option value="prefiero_no_decir">
                                        Prefiero no decirlo
                                    </option>

                                </select>

                                <small
                                    class="form-error"
                                    data-error-for="register-gender"
                                ></small>

                            </label>

                        </div>

                    </div>



                    <!-- =========================================
                         TIPO DE CUENTA

                         Administrador NO aparece como opción pública.
                         ========================================= -->

                    <div class="form-section">

                        <div class="form-section-heading">

                            <span>
                                02
                            </span>

                            <h2>
                                ¿Cómo usarás AulaGo?
                            </h2>

                        </div>


                        <div
                            class="role-selector"
                            role="radiogroup"
                            aria-label="Selecciona tipo de cuenta"
                        >


                            <!-- Estudiante -->

                            <label class="role-option">

                                <input
                                    type="radio"
                                    name="rol"
                                    value="estudiante"
                                    checked
                                >

                                <span class="role-option__content">

                                    <span class="role-option__number">
                                        01
                                    </span>

                                    <strong>
                                        Estudiante
                                    </strong>

                                    <small>
                                        Quiero tomar cursos,
                                        avanzar por niveles y obtener certificados.
                                    </small>

                                </span>

                            </label>



                            <!-- Instructor -->

                            <label class="role-option">

                                <input
                                    type="radio"
                                    name="rol"
                                    value="instructor"
                                >

                                <span class="role-option__content">

                                    <span class="role-option__number">
                                        02
                                    </span>

                                    <strong>
                                        Instructor
                                    </strong>

                                    <small>
                                        Quiero crear cursos,
                                        niveles y consultar mis ventas.
                                    </small>

                                </span>

                            </label>

                        </div>

                    </div>



                    <!-- =========================================
                         AVATAR
                         ========================================= -->

                    <div class="form-section">

                        <div class="form-section-heading">

                            <span>
                                03
                            </span>

                            <h2>
                                Imagen de perfil
                            </h2>

                        </div>


                        <label class="avatar-upload">

                            <span class="avatar-preview">
                                +
                            </span>


                            <span class="avatar-upload__text">

                                <strong>
                                    Selecciona una fotografía
                                </strong>

                                <small>
                                    JPG o PNG. La vista previa se
                                    implementará con JavaScript.
                                </small>

                            </span>


                            <input
                                type="file"
                                id="register-avatar"
                                name="avatar"
                                accept="image/png,image/jpeg"
                                required
                            >

                        </label>


                        <small
                            class="form-error"
                            data-error-for="register-avatar"
                        ></small>

                    </div>



                    <!-- =========================================
                         SEGURIDAD
                         ========================================= -->

                    <div class="form-section">

                        <div class="form-section-heading">

                            <span>
                                04
                            </span>

                            <h2>
                                Seguridad
                            </h2>

                        </div>


                        <div class="security-grid">


                            <div class="security-inputs">


                                <!-- Contraseña -->

                                <label class="form-field">

                                    <span class="form-label">
                                        Contraseña
                                    </span>


                                    <div class="password-input">

                                        <input
                                            type="password"
                                            id="register-password"
                                            name="password"
                                            placeholder="Crea una contraseña"
                                            autocomplete="new-password"
                                            required
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-password-toggle="register-password"
                                        >
                                            Ver
                                        </button>

                                    </div>


                                    <small
                                        class="form-error"
                                        data-error-for="register-password"
                                    ></small>

                                </label>



                                <!-- Confirmación -->

                                <label class="form-field">

                                    <span class="form-label">
                                        Confirmar contraseña
                                    </span>


                                    <div class="password-input">

                                        <input
                                            type="password"
                                            id="register-password-confirmation"
                                            name="password_confirmation"
                                            placeholder="Repite la contraseña"
                                            autocomplete="new-password"
                                            required
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-password-toggle="register-password-confirmation"
                                        >
                                            Ver
                                        </button>

                                    </div>


                                    <small
                                        class="form-error"
                                        data-error-for="register-password-confirmation"
                                    ></small>

                                </label>

                            </div>



                            <!-- Requisitos visuales -->

                            <div
                                class="password-rules"
                                aria-label="Requisitos de contraseña"
                            >

                                <span>
                                    Tu contraseña necesita:
                                </span>


                                <ul>

                                    <li data-rule="length">
                                        <span>○</span>
                                        mínimo 8 caracteres
                                    </li>

                                    <li data-rule="uppercase">
                                        <span>○</span>
                                        una letra mayúscula
                                    </li>

                                    <li data-rule="number">
                                        <span>○</span>
                                        un número
                                    </li>

                                    <li data-rule="special">
                                        <span>○</span>
                                        un carácter especial
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>



                    <!-- =========================================
                         FINAL
                         ========================================= -->

                    <div class="register-actions">

                        <button
                            type="submit"
                            class="auth-submit"
                        >
                            Crear cuenta
                            <span>→</span>
                        </button>


                        <p>
                            ¿Ya tienes una cuenta?

                            <RouterLink to="/login">
                                Iniciar sesión
                            </RouterLink>
                        </p>

                    </div>

                </form>

            </div>

        </section>



        <!-- ==================================================
             PANEL VISUAL DERECHO

             Inspirado en la composición azul que mandaste,
             pero usando la identidad de AulaGo.
             ================================================== -->

        <aside class="register-visual">


            <div class="register-visual__glow"></div>


            <div class="register-visual__header">

                <p class="eyebrow eyebrow--light">
                    Tu espacio
                </p>

                <h2>
                    Aprende.
                    <br>
                    Comparte.
                    <br>

                    <em>
                        Sigue creciendo.
                    </em>
                </h2>

            </div>



            <figure class="register-visual__image">

                <img
                    src="../../assets/images/registro-bg.png"
                   
                >

            </figure>



            <div class="register-visual__card">

                <span>
                    AulaGo / 2026
                </span>

                <p>
                    Una cuenta, diferentes formas
                    de formar parte del aprendizaje.
                </p>

            </div>

        </aside>

    </main>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { initAuthInteractions } from '../../scripts/authInteraction.js'

const router = useRouter()

function handleRegisterSuccess() {
  router.push('/login')
}

onMounted(() => {
  document.body.className = "auth-page"
  window.addEventListener('aulago:register-success', handleRegisterSuccess)
  initAuthInteractions()
})

onBeforeUnmount(() => {
  window.removeEventListener('aulago:register-success', handleRegisterSuccess)
  document.body.className = ''
})
</script>
