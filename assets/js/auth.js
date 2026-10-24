document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const wrapper = button.closest('.relative')
    const input = wrapper?.querySelector('[data-password-input]')
    if (!input) return
    button.addEventListener('click', () => {
      const visible = input.type === 'text'
      input.type = visible ? 'password' : 'text'
      button.textContent = visible ? 'Ver' : 'Ocultar'
    })
  })

  const registerForm = document.querySelector('[data-auth-form="register"]')
  const loginForm = document.querySelector('[data-auth-form="login"]')

  if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
      const email = loginForm.elements.email
      const password = loginForm.elements.password
      const status = loginForm.querySelector('[data-form-status]')
      let valid = true

      const setError = (name, message) => {
        const node = loginForm.querySelector(`[data-error-for="${name}"]`)
        if (node) node.textContent = message
      }

      setError('email', '')
      setError('password', '')
      if (!email.value.trim() || !/^\S+@\S+\.\S+$/.test(email.value.trim())) {
        setError('email', 'Ingresa un correo electrónico válido.')
        valid = false
      }
      if (!password.value.trim()) {
        setError('password', 'La contraseña es obligatoria.')
        valid = false
      }
      if (!valid) {
        event.preventDefault()
        if (status) {
          status.textContent = 'Revisa los campos marcados antes de continuar.'
          status.classList.remove('hidden')
        }
      }
    })
  }

  if (registerForm) {
    const roleCards = Array.from(registerForm.querySelectorAll('[data-role-card]'))
    const roleDescription = document.querySelector('[data-role-description]')
    const primaryPassword = registerForm.querySelector('[data-password-primary]')
    const rules = registerForm.querySelector('[data-password-rules]')
    const avatarInput = registerForm.querySelector('[data-avatar-input]')
    const avatarPreview = registerForm.querySelector('[data-avatar-preview]')
    const allowedRoles = ['estudiante', 'instructor']
    const allowedAvatarTypes = ['image/png', 'image/jpeg']

    const setError = (name, message) => {
      const node = registerForm.querySelector(`[data-error-for="${name}"]`)
      if (node) node.textContent = message
      registerForm.querySelectorAll(`[name="${name}"]`).forEach((input) => {
        input.setAttribute('aria-invalid', message ? 'true' : 'false')
      })
    }

    const validateAvatar = () => {
      const file = avatarInput?.files?.[0]
      const valid = !file || (
        allowedAvatarTypes.includes(file.type) && /\.(png|jpe?g)$/i.test(file.name)
      )
      setError('avatar', valid ? '' : 'Selecciona una imagen en formato PNG o JPG.')
      return valid
    }

    const updateRole = () => {
      const selected = registerForm.querySelector('input[name="rol"]:checked')?.value || ''
      roleCards.forEach((card) => {
        const active = card.dataset.roleCard === selected
        card.classList.toggle('border-brand-blue', active && selected === 'estudiante')
        card.classList.toggle('bg-brand-blue/10', active && selected === 'estudiante')
        card.classList.toggle('border-brand-orange', active && selected === 'instructor')
        card.classList.toggle('bg-brand-orange/10', active && selected === 'instructor')
        card.classList.toggle('border-white/10', !active)
        card.classList.toggle('bg-white/[0.02]', !active)
      })
      if (roleDescription) {
        roleDescription.textContent = selected === 'instructor'
          ? 'Comparte lo que sabes y construye experiencias de aprendizaje para otros.'
          : 'Explora nuevas ideas y avanza por cursos diseñados para aprender a tu ritmo.'
      }
    }

    registerForm.querySelectorAll('input[name="rol"]').forEach((input) => {
      input.addEventListener('change', () => {
        updateRole()
        const selected = registerForm.querySelector('input[name="rol"]:checked')?.value
        setError('rol', allowedRoles.includes(selected) ? '' : 'Selecciona Estudiante o Instructor.')
      })
    })
    updateRole()

    const updateRules = () => {
      if (!primaryPassword || !rules) return
      const value = primaryPassword.value
      const checks = {
        length: value.length >= 8,
        uppercase: /[A-Z]/.test(value),
        number: /\d/.test(value),
        special: /[^A-Za-z0-9]/.test(value),
      }
      Object.entries(checks).forEach(([key, valid]) => {
        const item = rules.querySelector(`[data-rule="${key}"]`)
        if (!item) return
        item.classList.toggle('text-aula-green', valid)
        item.classList.toggle('text-white/35', !valid)
        const icon = item.querySelector('span')
        if (icon) icon.textContent = valid ? '✓' : '○'
      })
    }
    primaryPassword?.addEventListener('input', updateRules)
    updateRules()

    avatarInput?.addEventListener('change', () => {
      const file = avatarInput.files?.[0]
      if (avatarPreview) avatarPreview.textContent = '+'
      if (!validateAvatar() || !file || !avatarPreview) return
      const url = URL.createObjectURL(file)
      avatarPreview.innerHTML = ''
      const img = document.createElement('img')
      img.src = url
      img.alt = 'Vista previa de fotografía'
      img.className = 'h-full w-full object-cover'
      img.addEventListener('load', () => URL.revokeObjectURL(url), { once: true })
      avatarPreview.appendChild(img)
    })

    registerForm.addEventListener('submit', (event) => {
      const status = registerForm.querySelector('[data-form-status]')
      const requiredNames = ['nombre', 'email', 'fecha_nacimiento', 'genero', 'password', 'password_confirmation']
      let valid = true

      if (status) {
        status.textContent = ''
        status.classList.add('hidden')
      }

      const fieldNames = [...requiredNames, 'rol', 'avatar']
      fieldNames.forEach((name) => setError(name, ''))

      requiredNames.forEach((name) => {
        const input = registerForm.elements[name]
        if (!input?.value?.trim()) {
          setError(name, 'Este campo es obligatorio.')
          valid = false
        }
      })

      const email = registerForm.elements.email
      if (email?.value && !/^\S+@\S+\.\S+$/.test(email.value.trim())) {
        setError('email', 'Ingresa un correo electrónico válido.')
        valid = false
      }

      const role = registerForm.querySelector('input[name="rol"]:checked')?.value
      if (!allowedRoles.includes(role)) {
        setError('rol', 'Selecciona Estudiante o Instructor.')
        valid = false
      }

      if (!validateAvatar()) valid = false

      const password = registerForm.elements.password?.value || ''
      const confirmation = registerForm.elements.password_confirmation?.value || ''
      const passwordOk = password.length >= 8 && /[A-Z]/.test(password) && /\d/.test(password) && /[^A-Za-z0-9]/.test(password)
      if (!passwordOk) {
        setError('password', 'Cumple los cuatro requisitos de contraseña.')
        valid = false
      }
      if (password !== confirmation) {
        setError('password_confirmation', 'Las contraseñas no coinciden.')
        valid = false
      }

      if (!valid) {
        event.preventDefault()
        if (status) {
          status.textContent = 'Hay información pendiente o inválida. Revisa los campos señalados.'
          status.classList.remove('hidden')
        }
      }
    })
  }
})
