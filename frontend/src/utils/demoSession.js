const SESSION_KEY = 'aulago_demo_session'

export function isDemoAuthenticated() {
  return localStorage.getItem(SESSION_KEY) === 'true'
}

export function startDemoSession() {
  localStorage.setItem(SESSION_KEY, 'true')
}

export function endDemoSession() {
  localStorage.removeItem(SESSION_KEY)
}
