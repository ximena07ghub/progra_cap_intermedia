/*
 * ============================================================
 * AULAGO — LOGIN Y REGISTRO
 *
 * Este archivo maneja únicamente validaciones y comportamiento
 * visual del lado del cliente.
 *
 * IMPORTANTE:
 * Esto NO sustituye la validación PHP.
 *
 * Cuando construyamos el backend PHP volverá a validar todos
 * los datos antes de guardarlos en MySQL.
 * ============================================================
 */


document.addEventListener(
    "DOMContentLoaded",
    () => {

        initPasswordToggles();

        initLoginForm();

        initRegisterForm();

        initPasswordRules();

        initAvatarPreview();

        initRoleSelector();

    }
);



/* ============================================================
   1. MOSTRAR / OCULTAR CONTRASEÑA
   ============================================================ */

function initPasswordToggles() {

    const buttons =
        document.querySelectorAll(
            "[data-password-toggle]"
        );


    buttons.forEach((button) => {

        button.addEventListener(
            "click",
            () => {

                const inputId =
                    button.dataset.passwordToggle;


                const input =
                    document.getElementById(
                        inputId
                    );


                if (!input) {
                    return;
                }


                const isPassword =
                    input.type === "password";


                input.type =
                    isPassword
                        ? "text"
                        : "password";


                button.textContent =
                    isPassword
                        ? "Ocultar"
                        : "Ver";


                button.setAttribute(
                    "aria-label",
                    isPassword
                        ? "Ocultar contraseña"
                        : "Mostrar contraseña"
                );

            }
        );

    });

}



/* ============================================================
   2. FUNCIONES GENERALES DE VALIDACIÓN
   ============================================================ */

function isEmpty(value) {

    return (
        String(value || "")
            .trim()
            .length
        ===
        0
    );

}



function isValidEmail(email) {

    const expression =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


    return expression.test(
        String(email || "")
            .trim()
    );

}



function getPasswordValidation(password) {

    const value =
        String(password || "");


    return {

        length:
            value.length >= 8,

        uppercase:
            /[A-Z]/.test(value),

        number:
            /[0-9]/.test(value),

        special:
            /[^A-Za-z0-9]/.test(value)

    };

}



/* ============================================================
   3. MOSTRAR ERRORES
   ============================================================ */

function showError(
    input,
    message
) {

    if (!input) {
        return;
    }


    input.classList.add(
        "is-invalid"
    );


    const error =
        document.querySelector(
            `[data-error-for="${input.id}"]`
        );


    if (error) {

        error.textContent =
            message;

    }

}



function clearError(input) {

    if (!input) {
        return;
    }


    input.classList.remove(
        "is-invalid"
    );


    const error =
        document.querySelector(
            `[data-error-for="${input.id}"]`
        );


    if (error) {

        error.textContent =
            "";

    }

}



/* ============================================================
   4. MENSAJE GENERAL DEL FORMULARIO
   ============================================================ */

function showFormStatus(
    form,
    message,
    type = "success"
) {

    let status =
        form.querySelector(
            ".form-status"
        );


    if (!status) {

        status =
            document.createElement(
                "div"
            );


        status.className =
            "form-status";


        form.appendChild(
            status
        );

    }


    status.className =
        `form-status form-status--${type}`;


    status.textContent =
        message;

}



/* ============================================================
   5. LOGIN
   ============================================================ */

function initLoginForm() {

    const form =
        document.getElementById(
            "login-form"
        );


    if (!form) {
        return;
    }


    const email =
        document.getElementById(
            "login-email"
        );


    const password =
        document.getElementById(
            "login-password"
        );



    /*
     * Validación mientras el usuario abandona un campo.
     */

    email?.addEventListener(
        "blur",
        () => {

            validateLoginEmail(
                email
            );

        }
    );


    password?.addEventListener(
        "blur",
        () => {

            validateLoginPassword(
                password
            );

        }
    );



    /*
     * Al escribir nuevamente quitamos el error
     * para no dejar el formulario visualmente bloqueado.
     */

    email?.addEventListener(
        "input",
        () => {

            clearError(email);

        }
    );


    password?.addEventListener(
        "input",
        () => {

            clearError(password);

        }
    );



    form.addEventListener(
        "submit",
        (event) => {

            /*
             * Todavía no existe PHP,
             * así que impedimos el envío real.
             */

            event.preventDefault();


            const emailValid =
                validateLoginEmail(
                    email
                );


            const passwordValid =
                validateLoginPassword(
                    password
                );


            if (
                !emailValid
                ||
                !passwordValid
            ) {

                showFormStatus(
                    form,
                    "Revisa los campos marcados antes de continuar.",
                    "error"
                );


                return;

            }



            /*
             * Esto solo confirma que la interfaz está lista.
             * PHP realizará la autenticación real más adelante.
             */

            showFormStatus(
                form,
                "Datos válidos. La autenticación real se conectará cuando implementemos PHP.",
                "success"
            );

        }
    );

}



function validateLoginEmail(input) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            "El correo electrónico es obligatorio."
        );


        return false;

    }


    if (
        !isValidEmail(
            input.value
        )
    ) {

        showError(
            input,
            "Ingresa un correo electrónico válido."
        );


        return false;

    }


    clearError(input);

    return true;

}



function validateLoginPassword(input) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            "La contraseña es obligatoria."
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   6. REGISTRO
   ============================================================ */

function initRegisterForm() {

    const form =
        document.getElementById(
            "register-form"
        );


    if (!form) {
        return;
    }



    const name =
        document.getElementById(
            "register-name"
        );


    const email =
        document.getElementById(
            "register-email"
        );


    const birthdate =
        document.getElementById(
            "register-birthdate"
        );


    const gender =
        document.getElementById(
            "register-gender"
        );


    const avatar =
        document.getElementById(
            "register-avatar"
        );


    const password =
        document.getElementById(
            "register-password"
        );


    const confirmation =
        document.getElementById(
            "register-password-confirmation"
        );



    /*
     * Quitamos mensajes de error al corregir.
     */

    [
        name,
        email,
        birthdate,
        gender,
        password,
        confirmation
    ]
    .forEach((input) => {

        input?.addEventListener(
            "input",
            () => {

                clearError(input);

            }
        );


        input?.addEventListener(
            "change",
            () => {

                clearError(input);

            }
        );

    });



    avatar?.addEventListener(
        "change",
        () => {

            clearError(avatar);

        }
    );



    form.addEventListener(
        "submit",
        (event) => {

            event.preventDefault();



            const validations = [

                validateRequiredText(
                    name,
                    "El nombre completo es obligatorio."
                ),

                validateRegistrationEmail(
                    email
                ),

                validateBirthdate(
                    birthdate
                ),

                validateSelect(
                    gender,
                    "Selecciona una opción de género."
                ),

                validateAvatar(
                    avatar
                ),

                validateRegistrationPassword(
                    password
                ),

                validatePasswordConfirmation(
                    password,
                    confirmation
                )

            ];



            const valid =
                validations.every(
                    Boolean
                );



            if (!valid) {

                showFormStatus(
                    form,
                    "Hay información pendiente o inválida. Revisa los campos señalados.",
                    "error"
                );


                /*
                 * Llevamos al primer campo inválido.
                 */

                const firstInvalid =
                    form.querySelector(
                        ".is-invalid"
                    );


                firstInvalid?.focus();


                return;

            }



            const selectedRole =
                form.querySelector(
                    'input[name="rol"]:checked'
                )?.value;



            /*
             * Hasta aquí únicamente validamos el frontend.
             */

            showFormStatus(
                form,
                `Formulario válido para registro como ${selectedRole || "usuario"}. PHP guardará la cuenta cuando implementemos el backend.`,
                "success"
            );

        }
    );

}



/* ============================================================
   7. VALIDAR TEXTO OBLIGATORIO
   ============================================================ */

function validateRequiredText(
    input,
    message
) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            message
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   8. EMAIL DE REGISTRO
   ============================================================ */

function validateRegistrationEmail(input) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            "El correo electrónico es obligatorio."
        );


        return false;

    }


    if (
        !isValidEmail(
            input.value
        )
    ) {

        showError(
            input,
            "Ingresa un correo electrónico válido."
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   9. FECHA DE NACIMIENTO

   Por ahora comprobamos:
   - que exista
   - que sea una fecha válida
   - que no esté en el futuro

   La rúbrica no establece una edad mínima.
   ============================================================ */

function validateBirthdate(input) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            "La fecha de nacimiento es obligatoria."
        );


        return false;

    }


    const selectedDate =
        new Date(
            `${input.value}T00:00:00`
        );


    const today =
        new Date();


    today.setHours(
        0,
        0,
        0,
        0
    );



    if (
        Number.isNaN(
            selectedDate.getTime()
        )
    ) {

        showError(
            input,
            "Selecciona una fecha válida."
        );


        return false;

    }



    if (
        selectedDate > today
    ) {

        showError(
            input,
            "La fecha de nacimiento no puede estar en el futuro."
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   10. SELECT
   ============================================================ */

function validateSelect(
    input,
    message
) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            message
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   11. AVATAR
   ============================================================ */

function validateAvatar(input) {

    if (!input) {
        return false;
    }


    const file =
        input.files?.[0];


    if (!file) {

        showError(
            input,
            "Selecciona una imagen de perfil."
        );


        return false;

    }



    const allowedTypes = [

        "image/jpeg",
        "image/png"

    ];


    if (
        !allowedTypes.includes(
            file.type
        )
    ) {

        showError(
            input,
            "La fotografía debe ser JPG o PNG."
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   12. CONTRASEÑA DEL REGISTRO
   ============================================================ */

function validateRegistrationPassword(input) {

    if (!input) {
        return false;
    }


    if (
        isEmpty(input.value)
    ) {

        showError(
            input,
            "La contraseña es obligatoria."
        );


        return false;

    }



    const validation =
        getPasswordValidation(
            input.value
        );



    if (!validation.length) {

        showError(
            input,
            "Debe tener al menos 8 caracteres."
        );


        return false;

    }



    if (!validation.uppercase) {

        showError(
            input,
            "Debe incluir al menos una letra mayúscula."
        );


        return false;

    }



    if (!validation.number) {

        showError(
            input,
            "Debe incluir al menos un número."
        );


        return false;

    }



    if (!validation.special) {

        showError(
            input,
            "Debe incluir al menos un carácter especial."
        );


        return false;

    }


    clearError(input);

    return true;

}



/* ============================================================
   13. CONFIRMACIÓN DE CONTRASEÑA
   ============================================================ */

function validatePasswordConfirmation(
    password,
    confirmation
) {

    if (!confirmation) {
        return false;
    }


    if (
        isEmpty(
            confirmation.value
        )
    ) {

        showError(
            confirmation,
            "Confirma tu contraseña."
        );


        return false;

    }



    if (
        password?.value
        !==
        confirmation.value
    ) {

        showError(
            confirmation,
            "Las contraseñas no coinciden."
        );


        return false;

    }


    clearError(
        confirmation
    );


    return true;

}



/* ============================================================
   14. CHECKLIST DE CONTRASEÑA EN VIVO
   ============================================================ */

function initPasswordRules() {

    const password =
        document.getElementById(
            "register-password"
        );


    if (!password) {
        return;
    }



    const rules = {

        length:
            document.querySelector(
                '[data-rule="length"]'
            ),

        uppercase:
            document.querySelector(
                '[data-rule="uppercase"]'
            ),

        number:
            document.querySelector(
                '[data-rule="number"]'
            ),

        special:
            document.querySelector(
                '[data-rule="special"]'
            )

    };



    const updateRules = () => {

        const validation =
            getPasswordValidation(
                password.value
            );


        Object.entries(
            validation
        )
        .forEach(
            ([rule, valid]) => {

                const element =
                    rules[rule];


                if (!element) {
                    return;
                }


                element.classList.toggle(
                    "is-valid",
                    valid
                );


                const symbol =
                    element.querySelector(
                        "span"
                    );


                if (symbol) {

                    symbol.textContent =
                        valid
                            ? "✓"
                            : "○";

                }

            }
        );

    };


    password.addEventListener(
        "input",
        updateRules
    );


    updateRules();

}



/* ============================================================
   15. PREVIEW DEL AVATAR
   ============================================================ */

function initAvatarPreview() {

    const input =
        document.getElementById(
            "register-avatar"
        );


    const preview =
        document.querySelector(
            ".avatar-preview"
        );


    if (
        !input
        ||
        !preview
    ) {

        return;
    }



    input.addEventListener(
        "change",
        () => {

            const file =
                input.files?.[0];


            if (!file) {

                preview.textContent =
                    "+";

                return;

            }



            if (
                !file.type.startsWith(
                    "image/"
                )
            ) {

                return;

            }



            const reader =
                new FileReader();



            reader.addEventListener(
                "load",
                () => {

                    preview.innerHTML =
                        "";


                    const image =
                        document.createElement(
                            "img"
                        );


                    image.src =
                        reader.result;


                    image.alt =
                        "Vista previa de fotografía";


                    preview.appendChild(
                        image
                    );

                }
            );


            reader.readAsDataURL(
                file
            );

        }
    );

}



/* ============================================================
   16. ESTUDIANTE / INSTRUCTOR
   ============================================================ */

function initRoleSelector() {

    const roleInputs =
        document.querySelectorAll(
            'input[name="rol"]'
        );


    if (!roleInputs.length) {
        return;
    }



    const registerHeading =
        document.querySelector(
            ".register-visual__card p"
        );



    roleInputs.forEach((input) => {

        input.addEventListener(
            "change",
            () => {

                if (!registerHeading) {
                    return;
                }



                if (
                    input.value
                    ===
                    "instructor"
                    &&
                    input.checked
                ) {

                    registerHeading.textContent =
                        "Comparte lo que sabes y construye experiencias de aprendizaje para otros.";

                }



                if (
                    input.value
                    ===
                    "estudiante"
                    &&
                    input.checked
                ) {

                    registerHeading.textContent =
                        "Explora nuevas ideas y avanza por cursos diseñados para aprender a tu ritmo.";

                }

            }
        );

    });

}