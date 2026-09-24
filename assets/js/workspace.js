document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-workspace]')
  if (!root) return

  const links = Array.from(root.querySelectorAll('[data-workspace-link]'))
  const panels = Array.from(root.querySelectorAll('[data-workspace-panel]'))
  const title = root.querySelector('[data-workspace-title]')
  const eyebrow = root.querySelector('[data-workspace-eyebrow]')
  const nav = root.querySelector('[data-workspace-nav]')
  const menuToggle = root.querySelector('[data-workspace-menu-toggle]')
  const main = root.querySelector('.workspace-main')

  const first = panels[0]?.dataset.workspacePanel || 'dashboard'
  const validIds = new Set(panels.map((panel) => panel.dataset.workspacePanel))

  const activate = (requested) => {
    const id = validIds.has(requested) ? requested : first
    panels.forEach((panel) => { panel.hidden = panel.dataset.workspacePanel !== id })
    links.forEach((link) => {
      const active = link.dataset.workspaceLink === id
      link.classList.toggle('is-active', active)
      link.setAttribute('aria-current', active ? 'page' : 'false')
    })

    const activePanel = panels.find((panel) => panel.dataset.workspacePanel === id)
    if (title && activePanel?.dataset.title) title.textContent = activePanel.dataset.title
    if (eyebrow && activePanel?.dataset.eyebrow) eyebrow.textContent = activePanel.dataset.eyebrow

    if (main) main.scrollTop = 0
    if (window.innerWidth < 1024 && nav) nav.classList.add('hidden')
  }

  links.forEach((link) => link.addEventListener('click', () => {
    const id = link.dataset.workspaceLink
    if (!id) return
    activate(id)
  }))

  menuToggle?.addEventListener('click', () => nav?.classList.toggle('hidden'))

  window.addEventListener('hashchange', () => activate(location.hash.replace('#', '')))
  activate(location.hash.replace('#', '') || first)

  /* Dashboard del estudiante: simula el último curso abierto hasta conectar MySQL. */
  const dashboard = root.querySelector('[data-student-dashboard]')
  if (dashboard) {
    const courseButtons = Array.from(dashboard.querySelectorAll('[data-dashboard-course]'))
    const search = dashboard.querySelector('[data-dashboard-search]')
    const empty = dashboard.querySelector('[data-dashboard-empty]')
    const focus = (key) => dashboard.querySelector(`[data-dashboard-focus-${key}]`)

    const selectCourse = (button) => {
      if (!button) return
      courseButtons.forEach((item) => item.classList.toggle('is-current', item === button))
      const data = button.dataset

      if (focus('title')) focus('title').textContent = data.courseTitle || ''
      if (focus('category')) focus('category').textContent = data.courseCategory || ''
      if (focus('level')) focus('level').textContent = data.courseLevel || ''
      if (focus('next')) focus('next').textContent = data.courseNext || ''
      if (focus('description')) focus('description').textContent = data.courseDescription || ''
      if (focus('progress')) focus('progress').textContent = `${data.courseProgress || 0}%`
      if (focus('progress-bar')) focus('progress-bar').style.width = `${data.courseProgress || 0}%`
      if (focus('link')) focus('link').setAttribute('href', data.courseHref || '#')

      const image = focus('image')
      if (image) {
        image.src = data.courseImage || image.src
        image.alt = data.courseTitle || ''
      }

      const firstLessonTitle = dashboard.querySelector('[data-dashboard-lesson-list] strong')
      if (firstLessonTitle) firstLessonTitle.textContent = data.courseNext || 'Continúa donde te quedaste'
    }

    courseButtons.forEach((button) => button.addEventListener('click', () => selectCourse(button)))

    search?.addEventListener('input', () => {
      const term = search.value.trim().toLocaleLowerCase('es')
      let visible = 0
      courseButtons.forEach((button) => {
        const haystack = `${button.dataset.courseTitle || ''} ${button.dataset.courseCategory || ''} ${button.dataset.courseLevel || ''}`.toLocaleLowerCase('es')
        const match = term === '' || haystack.includes(term)
        button.hidden = !match
        if (match) visible += 1
      })
      if (empty) empty.classList.toggle('hidden', visible !== 0)
    })
  }

  /* Mensajes: los datos PHP se renderizan con JS normal, sin Vue ni API. */
  root.querySelectorAll('[data-messages-panel]').forEach((panel) => {
    const thread = panel.querySelector('[data-message-thread]')
    const buttons = Array.from(panel.querySelectorAll('[data-conversation-button]'))
    const dataNode = panel.querySelector('[data-conversation-data]')
    if (!thread || !dataNode) return

    let conversations = []
    try {
      conversations = JSON.parse(dataNode.textContent || '[]')
    } catch {
      conversations = []
    }

    const state = new Map(conversations.map((conversation) => [String(conversation.id), {
      ...conversation,
      messages: Array.isArray(conversation.messages) ? [...conversation.messages] : [],
    }]))

    let activeId = String(buttons.find((button) => button.classList.contains('is-selected'))?.dataset.conversationId || conversations[0]?.id || '')
    const stream = thread.querySelector('[data-message-stream]')
    const nameNode = thread.querySelector('[data-thread-name]')
    const courseNode = thread.querySelector('[data-thread-course]')
    const avatarNode = thread.querySelector('[data-thread-avatar]')

    const formatDate = (date) => {
      if (!date) return ''
      const parsed = new Date(`${date}T12:00:00`)
      if (Number.isNaN(parsed.getTime())) return date
      return new Intl.DateTimeFormat('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }).format(parsed)
    }

    const renderMessages = (conversation) => {
      if (!stream || !conversation) return
      stream.replaceChildren()

      conversation.messages.forEach((message) => {
        const article = document.createElement('article')
        article.className = `max-w-[82%] p-4 text-sm leading-6 ${message.from === 'me' ? 'ml-auto bg-aula-green-dark text-white' : 'bg-white/[0.05] text-white/75'}`

        const text = document.createElement('p')
        text.textContent = message.text || ''
        const meta = document.createElement('span')
        meta.className = 'mt-2 block text-[10px] uppercase tracking-[0.08em] text-white/35'
        meta.textContent = message.time === 'Ahora' ? 'Ahora' : `${formatDate(message.date)} · ${message.time || ''}`

        article.append(text, meta)
        stream.appendChild(article)
      })

      requestAnimationFrame(() => { stream.scrollTop = stream.scrollHeight })
    }

    const selectConversation = (id) => {
      const conversation = state.get(String(id))
      if (!conversation) return
      activeId = String(id)
      buttons.forEach((button) => button.classList.toggle('is-selected', button.dataset.conversationId === activeId))
      if (nameNode) nameNode.textContent = conversation.participant || 'Conversación'
      if (courseNode) courseNode.textContent = conversation.course || ''
      if (avatarNode) avatarNode.textContent = conversation.avatar || 'AG'
      renderMessages(conversation)
    }

    buttons.forEach((button) => button.addEventListener('click', () => selectConversation(button.dataset.conversationId)))

    const form = thread.querySelector('[data-message-form]')
    form?.addEventListener('submit', (event) => {
      event.preventDefault()
      const input = form.querySelector('input')
      const value = input?.value.trim()
      const conversation = state.get(activeId)
      if (!value || !conversation) return

      conversation.messages.push({
        id: `local-${Date.now()}`,
        from: 'me',
        date: new Date().toISOString().slice(0, 10),
        time: 'Ahora',
        text: value,
      })
      input.value = ''
      renderMessages(conversation)
    })

    if (activeId) selectConversation(activeId)
  })
})
