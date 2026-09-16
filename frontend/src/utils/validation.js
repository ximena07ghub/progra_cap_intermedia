const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

const passwordRules = [
  {
    key: 'length',
    message: 'Debe tener al menos 8 caracteres.',
    test: (value) => value.length >= 8,
  },
  {
    key: 'uppercase',
    message: 'Debe incluir al menos una letra mayúscula.',
    test: (value) => /[A-Z]/.test(value),
  },
  {
    key: 'number',
    message: 'Debe incluir al menos un número.',
    test: (value) => /\d/.test(value),
  },
  {
    key: 'special',
    message: 'Debe incluir al menos un carácter especial.',
    test: (value) => /[^A-Za-z0-9]/.test(value),
  },
]

export function useFormValidation() {
  function isRequired(value) {
    return String(value ?? '').trim().length > 0
  }

  function isEmail(value) {
    return emailPattern.test(String(value ?? '').trim())
  }

  function validateRequired(value, message = 'Este campo es obligatorio.') {
    return isRequired(value) ? '' : message
  }

  function validateEmail(value) {
    if (!isRequired(value)) return 'El correo electrónico es obligatorio.'
    if (!isEmail(value)) return 'Ingresa un correo electrónico válido.'
    return ''
  }

  function getPasswordChecks(value) {
    const password = String(value ?? '')

    return passwordRules.reduce((checks, rule) => {
      checks[rule.key] = rule.test(password)
      return checks
    }, {})
  }

  function validatePassword(value) {
    if (!isRequired(value)) return 'La contraseña es obligatoria.'

    const password = String(value ?? '')
    const failedRule = passwordRules.find((rule) => !rule.test(password))
    return failedRule?.message ?? ''
  }

  function validatePasswordConfirmation(password, confirmation) {
    if (!isRequired(confirmation)) return 'Confirma tu contraseña.'
    if (password !== confirmation) return 'Las contraseñas no coinciden.'
    return ''
  }

  function validateBirthdate(value) {
    if (!isRequired(value)) return 'La fecha de nacimiento es obligatoria.'

    const selectedDate = new Date(`${value}T00:00:00`)
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    if (Number.isNaN(selectedDate.getTime())) return 'Selecciona una fecha válida.'
    if (selectedDate > today) return 'La fecha de nacimiento no puede estar en el futuro.'
    return ''
  }

  function validateAvatar(file) {
    if (!file) return 'Selecciona una imagen de perfil.'

    const allowedTypes = ['image/jpeg', 'image/png']
    if (!allowedTypes.includes(file.type)) return 'La fotografía debe ser JPG o PNG.'

    return ''
  }

  return {
    isRequired,
    isEmail,
    passwordRules,
    validateRequired,
    validateEmail,
    getPasswordChecks,
    validatePassword,
    validatePasswordConfirmation,
    validateBirthdate,
    validateAvatar,
  }
}