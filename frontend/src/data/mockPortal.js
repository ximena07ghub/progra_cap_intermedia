export const kardexRecords = [
  {
    id: 1,
    slug: 'neuro-habitos',
    course: 'Neuro-Hábitos',
    category: 'Bienestar',
    instructor: 'Psic. Ana Solís',
    finishedAt: '2026-09-15',
    grade: 96,
    amount: 680,
    certificateId: 'AG-2026-0001',
  },
  {
    id: 2,
    slug: 'diseno-que-comunica',
    course: 'Diseño que comunica',
    category: 'Diseño',
    instructor: 'Lucía Rojas',
    finishedAt: '2026-08-22',
    grade: 91,
    amount: 790,
    certificateId: 'AG-2026-0002',
  },
  {
    id: 3,
    slug: 'fundamentos-web',
    course: 'Fundamentos Web',
    category: 'IT & Software',
    instructor: 'Diego Villarreal',
    finishedAt: '2025-12-11',
    grade: 88,
    amount: 850,
    certificateId: 'AG-2025-0018',
  },
]

export const courseReviews = {
  'neuro-habitos': [
    {
      id: 1,
      name: 'Mariana T.',
      avatar: 'MT',
      rating: 5,
      completedAt: '2026-09-10',
      comment: 'La estructura por niveles me ayudó a aplicar los ejercicios sin sentir el contenido pesado.',
    },
    {
      id: 2,
      name: 'Carlos R.',
      avatar: 'CR',
      rating: 4,
      completedAt: '2026-08-28',
      comment: 'Me gustaron las actividades prácticas. Agregaría un ejemplo extra en el último módulo.',
    },
    {
      id: 3,
      name: 'Fernanda G.',
      avatar: 'FG',
      rating: 5,
      completedAt: '2026-08-17',
      comment: 'Muy claro y directo. Sí sentí diferencia al organizar mis señales de inicio.',
    },
  ],
}

export const conversations = [
  {
    id: 1,
    participant: 'Psic. Ana Solís',
    participantRole: 'Instructor',
    avatar: 'AS',
    course: 'Neuro-Hábitos',
    messages: [
      { id: 1, from: 'other', date: '2026-09-15', time: '18:35', text: 'Hola, ¿cómo vas con el ejercicio del nivel 3?' },
      { id: 2, from: 'me', date: '2026-09-15', time: '18:42', text: 'Ya lo terminé. Solo tengo duda sobre cómo medir la señal de inicio.' },
      { id: 3, from: 'other', date: '2026-09-15', time: '18:48', text: 'Puedes usar una acción concreta y repetible. Si quieres, mañana revisamos tu ejemplo.' },
    ],
  },
  {
    id: 2,
    participant: 'Diego Villarreal',
    participantRole: 'Instructor',
    avatar: 'DV',
    course: 'Fundamentos Web',
    messages: [
      { id: 1, from: 'other', date: '2026-09-12', time: '11:05', text: 'Recuerda subir tu ejercicio antes de continuar con la siguiente lección.' },
    ],
  },
]

export const instructorCourses = [
  {
    id: 1,
    slug: 'neuro-habitos',
    title: 'Neuro-Hábitos',
    category: 'Bienestar',
    status: 'Publicado',
    students: 128,
    price: 680,
    rating: 4.7,
    updatedAt: '2026-09-14',
  },
  {
    id: 2,
    slug: 'enfoque-profundo',
    title: 'Enfoque profundo',
    category: 'Bienestar',
    status: 'Borrador',
    students: 0,
    price: 610,
    rating: 0,
    updatedAt: '2026-09-08',
  },
]

export const salesTransactions = [
  { id: 'V-1042', courseSlug: 'neuro-habitos', course: 'Neuro-Hábitos', student: 'Mariana Torres', date: '2026-09-15', amount: 680 },
  { id: 'V-1041', courseSlug: 'neuro-habitos', course: 'Neuro-Hábitos', student: 'Carlos Ramírez', date: '2026-09-14', amount: 680 },
  { id: 'V-1039', courseSlug: 'neuro-habitos', course: 'Neuro-Hábitos', student: 'Fernanda García', date: '2026-09-11', amount: 680 },
  { id: 'V-1033', courseSlug: 'enfoque-profundo', course: 'Enfoque profundo', student: 'Luis Peña', date: '2026-08-29', amount: 610 },
]

export const adminCategories = [
  { id: 1, name: 'Bienestar', slug: 'bienestar', courses: 12, active: true },
  { id: 2, name: 'Diseño', slug: 'diseno', courses: 8, active: true },
  { id: 3, name: 'IT & Software', slug: 'software', courses: 15, active: true },
  { id: 4, name: 'Marketing', slug: 'marketing', courses: 7, active: true },
]

export const adminUsers = [
  { id: 1, name: 'Mariana Torres', email: 'mariana@example.com', role: 'estudiante', status: 'Activo', reports: 0 },
  { id: 2, name: 'Ana Solís', email: 'ana@aulago.mx', role: 'instructor', status: 'Activo', reports: 1 },
  { id: 3, name: 'Usuario Reportado', email: 'reportado@example.com', role: 'estudiante', status: 'Activo', reports: 3 },
]

export const adminComments = [
  { id: 1, user: 'Mariana Torres', course: 'Neuro-Hábitos', rating: 5, date: '2026-09-15', status: 'Visible', text: 'Me ayudó mucho la estructura del curso.' },
  { id: 2, user: 'Carlos Ramírez', course: 'Neuro-Hábitos', rating: 4, date: '2026-09-14', status: 'Visible', text: 'Buen curso, agregaría más ejemplos al final.' },
  { id: 3, user: 'Usuario Reportado', course: 'Fundamentos Web', rating: 1, date: '2026-09-13', status: 'Reportado', text: 'Comentario pendiente de moderación por reporte.' },
]

export const instructorConversations = [
  {
    id: 101,
    participant: 'Mariana Torres',
    participantRole: 'Estudiante',
    avatar: 'MT',
    course: 'Neuro-Hábitos',
    messages: [
      { id: 1, from: 'other', date: '2026-09-15', time: '18:42', text: 'Ya terminé el ejercicio. ¿Cómo puedo medir mejor la señal de inicio?' },
      { id: 2, from: 'me', date: '2026-09-15', time: '18:48', text: 'Usa una acción concreta y repetible. Mañana podemos revisar tu ejemplo.' },
    ],
  },
  {
    id: 102,
    participant: 'Carlos Ramírez',
    participantRole: 'Estudiante',
    avatar: 'CR',
    course: 'Neuro-Hábitos',
    messages: [
      { id: 1, from: 'other', date: '2026-09-14', time: '09:15', text: '¿El material complementario del nivel 2 se entrega en PDF?' },
    ],
  },
]

