export function formatCurrency(amount) {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(Number(amount || 0))
}

export function formatDate(date) {
  const parsed = new Date(`${date}T12:00:00`)
  if (Number.isNaN(parsed.getTime())) return '—'

  const day = String(parsed.getDate()).padStart(2, '0')
  const month = parsed
    .toLocaleDateString('es-MX', { month: 'short' })
    .replace('.', '')
    .toUpperCase()
  const year = parsed.getFullYear()

  return `${day} ${month} ${year}`
}

