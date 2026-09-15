/*
 * Utilidades de validacion para formularios futuros.
 * No se conectan a autenticacion real en esta primera fase.
 */
export const AulaGoValidations = (() => {
    const passwordRules = [
        {
            key: "minLength",
            message: "La contrasena debe tener al menos 8 caracteres.",
            test: (value) => value.length >= 8,
        },
        {
            key: "uppercase",
            message: "La contrasena debe incluir al menos una mayuscula.",
            test: (value) => /[A-Z]/.test(value),
        },
        {
            key: "number",
            message: "La contrasena debe incluir al menos un numero.",
            test: (value) => /\d/.test(value),
        },
        {
            key: "special",
            message: "La contrasena debe incluir al menos un caracter especial.",
            test: (value) => /[^A-Za-z0-9]/.test(value),
        },
    ];

    const isRequired = (value) => String(value || "").trim().length > 0;

    const isEmail = (value) => {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        return emailPattern.test(String(value || "").trim());
    };

    const validatePassword = (value) => {
        const password = String(value || "");
        const failedRules = passwordRules.filter((rule) => !rule.test(password));

        return {
            isValid: failedRules.length === 0,
            failedRules,
        };
    };

    const validateField = (field) => {
        if (!field) {
            return { isValid: true, errors: [] };
        }

        const errors = [];
        const value = field.value;

        if (field.required && !isRequired(value)) {
            errors.push("Este campo es obligatorio.");
        }

        if (field.type === "email" && isRequired(value) && !isEmail(value)) {
            errors.push("Ingresa un correo electronico valido.");
        }

        if (field.dataset.validation === "password" && isRequired(value)) {
            validatePassword(value).failedRules.forEach((rule) => errors.push(rule.message));
        }

        return {
            isValid: errors.length === 0,
            errors,
        };
    };

    const bindFormValidation = (form) => {
        if (!form) {
            return;
        }

        const fields = Array.from(form.querySelectorAll("input, textarea, select"));

        fields.forEach((field) => {
            field.addEventListener("blur", () => validateField(field));
        });

        form.addEventListener("submit", (event) => {
            const hasErrors = fields.some((field) => !validateField(field).isValid);

            if (hasErrors) {
                event.preventDefault();
            }
        });
    };

    return {
        bindFormValidation,
        isEmail,
        isRequired,
        passwordRules,
        validateField,
        validatePassword,
    };
})();
