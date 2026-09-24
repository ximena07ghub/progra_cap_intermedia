document.addEventListener('DOMContentLoaded', () => {
  const toggles = document.querySelectorAll('[data-menu-toggle]')

  toggles.forEach((toggle) => {
    const panelId = toggle.dataset.menuToggle
    const panel = document.getElementById(panelId)
    const icon = toggle.querySelector('[data-menu-icon]')

    if (!panel) return

    const setOpen = (open) => {
      panel.hidden = !open
      toggle.setAttribute('aria-expanded', String(open))
      if (icon) icon.textContent = open ? '×' : '☰'
    }

    toggle.addEventListener('click', () => {
      setOpen(toggle.getAttribute('aria-expanded') !== 'true')
    })

    panel.querySelectorAll('[data-menu-close]').forEach((link) => {
      link.addEventListener('click', () => setOpen(false))
    })
  })
})
