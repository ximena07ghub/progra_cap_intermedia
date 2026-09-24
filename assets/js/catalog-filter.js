document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-catalog-filter]')
  if (!root) return

  const queryInput = root.querySelector('[data-filter-query]')
  const levelSelect = root.querySelector('[data-filter-level]')
  const clearButton = root.querySelector('[data-filter-clear]')
  const cards = Array.from(root.querySelectorAll('[data-course-card]'))
  const categoryButtons = Array.from(root.querySelectorAll('[data-category]'))
  const count = root.querySelector('[data-filter-count]')
  const title = root.querySelector('[data-filter-title]')
  const empty = root.querySelector('[data-filter-empty]')
  const grid = root.querySelector('[data-course-grid]')

  const params = new URLSearchParams(window.location.search)
  let category = params.get('categoria') || ''

  const normalized = (value) => String(value || '').trim().toLocaleLowerCase('es')

  const updateButtons = () => {
    categoryButtons.forEach((button) => {
      const active = (button.dataset.category || '') === category
      button.classList.toggle('border-aula-orange/40', active)
      button.classList.toggle('bg-aula-orange/10', active)
      button.classList.toggle('text-aula-orange-soft', active)
      button.classList.toggle('border-white/10', !active)
      button.classList.toggle('bg-aula-surface/50', !active)
      button.classList.toggle('text-white/40', !active)
    })
  }

  const updateUrl = () => {
    const next = new URLSearchParams()
    const q = queryInput?.value.trim() || ''
    const level = levelSelect?.value || ''
    if (q) next.set('q', q)
    if (category) next.set('categoria', category)
    if (level) next.set('nivel', level)
    const query = next.toString()
    history.replaceState({}, '', `${window.location.pathname}${query ? `?${query}` : ''}`)
  }

  const apply = () => {
    const q = normalized(queryInput?.value)
    const level = levelSelect?.value || ''
    let visible = 0

    cards.forEach((card) => {
      const matchesQuery = !q || normalized(card.dataset.title).includes(q)
      const matchesCategory = !category || card.dataset.category === category
      const matchesLevel = !level || card.dataset.level === level
      const show = matchesQuery && matchesCategory && matchesLevel
      card.hidden = !show
      if (show) visible += 1
    })

    const selectedButton = categoryButtons.find((button) => (button.dataset.category || '') === category)
    const categoryName = selectedButton && category ? selectedButton.textContent.replace(/\d+\s*$/, '').trim() : ''
    if (title) {
      if (categoryName && q) title.textContent = `${categoryName}: “${queryInput.value.trim()}”`
      else if (categoryName) title.textContent = categoryName
      else if (q) title.textContent = `Resultados para “${queryInput.value.trim()}”`
      else if (level) title.textContent = `Nivel ${level}`
      else title.textContent = 'Todos los cursos'
    }
    if (count) count.textContent = `${visible} curso${visible === 1 ? '' : 's'}`
    if (grid) grid.classList.toggle('hidden', visible === 0)
    if (empty) empty.classList.toggle('hidden', visible !== 0)

    updateButtons()
    updateUrl()
  }

  queryInput?.addEventListener('input', apply)
  levelSelect?.addEventListener('change', apply)
  categoryButtons.forEach((button) => button.addEventListener('click', () => {
    category = button.dataset.category || ''
    apply()
  }))
  clearButton?.addEventListener('click', () => {
    if (queryInput) queryInput.value = ''
    if (levelSelect) levelSelect.value = ''
    category = ''
    apply()
  })
  root.querySelector('[data-filter-form]')?.addEventListener('submit', (event) => event.preventDefault())

  apply()
})
