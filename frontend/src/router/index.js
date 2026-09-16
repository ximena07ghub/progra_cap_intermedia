import { createRouter, createWebHistory } from 'vue-router'
import { pinia } from '../stores'
import { useSessionStore } from '../stores/session.js'

const studentMeta = { requiresAuth: true, allowedRoles: ['estudiante'] }
const instructorMeta = { requiresAuth: true, allowedRoles: ['instructor'] }
const adminMeta = { requiresAuth: true, allowedRoles: ['administrador'] }

const routes = [
  { path: '/', name: 'home', component: () => import('../views/HomeView.vue'), meta: { title: 'AulaGo | Aprende a tu ritmo' } },
  { path: '/login', name: 'login', component: () => import('../views/auth/LoginView.vue'), meta: { title: 'Iniciar sesión | AulaGo' } },
  { path: '/registro', name: 'register', component: () => import('../views/auth/RegisterView.vue'), meta: { title: 'Crear cuenta | AulaGo' } },
  { path: '/recuperar-contrasena', name: 'forgot-password', component: () => import('../views/auth/ForgotPasswordView.vue'), meta: { title: 'Recuperar contraseña | AulaGo' } },

  { path: '/cursos', name: 'courses', component: () => import('../views/public/CoursesView.vue'), meta: { title: 'Cursos | AulaGo' } },
  { path: '/cursos/:slug', name: 'course-description', component: () => import('../views/public/CourseDescriptionView.vue'), meta: { title: 'Curso | AulaGo' } },
  { path: '/curso', redirect: '/cursos/neuro-habitos' },
  { path: '/categorias', name: 'categories', component: () => import('../views/public/CategoriesView.vue'), meta: { title: 'Categorías | AulaGo' } },
  { path: '/buscar', name: 'search', component: () => import('../views/public/SearchView.vue'), meta: { title: 'Búsqueda | AulaGo' } },

  {
    path: '/estudiante',
    name: 'student-dashboard',
    component: () => import('../views/student/StudentDashboardView.vue'),
    meta: { ...studentMeta, title: 'Mi espacio | AulaGo' },
  },
  {
    path: '/estudiante/cuenta',
    name: 'student-account',
    component: () => import('../views/student/StudentAccountView.vue'),
    meta: { ...studentMeta, title: 'Mi cuenta | AulaGo' },
  },
  {
    path: '/estudiante/curso/:slug?',
    name: 'student-course',
    component: () => import('../views/student/StudentCourseView.vue'),
    meta: { ...studentMeta, title: 'Mi curso | AulaGo' },
  },
  {
    path: '/estudiante/kardex',
    name: 'student-kardex',
    component: () => import('../views/student/StudentKardexView.vue'),
    meta: { ...studentMeta, title: 'Kardex | AulaGo' },
  },
  {
    path: '/estudiante/mensajes',
    name: 'student-messages',
    component: () => import('../views/shared/MessagesView.vue'),
    meta: { ...studentMeta, title: 'Mensajes | AulaGo' },
  },
  {
    path: '/estudiante/checkout/:slug',
    name: 'student-checkout',
    component: () => import('../views/student/CheckoutView.vue'),
    meta: { ...studentMeta, title: 'Finalizar compra | AulaGo' },
  },
  {
    path: '/estudiante/certificado/:slug',
    name: 'student-certificate',
    component: () => import('../views/student/CertificateView.vue'),
    meta: { ...studentMeta, title: 'Certificado | AulaGo' },
  },

  {
    path: '/instructor',
    name: 'instructor-dashboard',
    component: () => import('../views/instructor/InstructorDashboardView.vue'),
    meta: { ...instructorMeta, title: 'Panel de instructor | AulaGo' },
  },
  {
    path: '/instructor/cursos',
    name: 'instructor-courses',
    component: () => import('../views/instructor/InstructorCoursesView.vue'),
    meta: { ...instructorMeta, title: 'Mis cursos | AulaGo' },
  },
  {
    path: '/instructor/cursos/nuevo',
    name: 'instructor-course-new',
    component: () => import('../views/instructor/CourseEditorView.vue'),
    meta: { ...instructorMeta, title: 'Crear curso | AulaGo' },
  },
  {
    path: '/instructor/cursos/:slug/editar',
    name: 'instructor-course-edit',
    component: () => import('../views/instructor/CourseEditorView.vue'),
    meta: { ...instructorMeta, title: 'Editar curso | AulaGo' },
  },
  {
    path: '/instructor/cursos/:slug/contenido',
    name: 'instructor-course-content',
    component: () => import('../views/instructor/CourseContentView.vue'),
    meta: { ...instructorMeta, title: 'Contenido del curso | AulaGo' },
  },
  {
    path: '/instructor/ventas',
    name: 'instructor-sales',
    component: () => import('../views/instructor/InstructorSalesView.vue'),
    meta: { ...instructorMeta, title: 'Ventas | AulaGo' },
  },
  {
    path: '/instructor/ventas/:slug',
    name: 'instructor-sales-detail',
    component: () => import('../views/instructor/InstructorSalesDetailView.vue'),
    meta: { ...instructorMeta, title: 'Detalle de ventas | AulaGo' },
  },
  {
    path: '/instructor/mensajes',
    name: 'instructor-messages',
    component: () => import('../views/shared/MessagesView.vue'),
    meta: { ...instructorMeta, title: 'Mensajes | AulaGo' },
  },
  {
    path: '/instructor/cuenta',
    name: 'instructor-account',
    component: () => import('../views/instructor/InstructorAccountView.vue'),
    meta: { ...instructorMeta, title: 'Cuenta de instructor | AulaGo' },
  },

  {
    path: '/admin',
    name: 'admin-dashboard',
    component: () => import('../views/admin/AdminDashboardView.vue'),
    meta: { ...adminMeta, title: 'Administración | AulaGo' },
  },
  {
    path: '/admin/categorias',
    name: 'admin-categories',
    component: () => import('../views/admin/AdminCategoriesView.vue'),
    meta: { ...adminMeta, title: 'Administrar categorías | AulaGo' },
  },
  {
    path: '/admin/usuarios',
    name: 'admin-users',
    component: () => import('../views/admin/AdminUsersView.vue'),
    meta: { ...adminMeta, title: 'Administrar usuarios | AulaGo' },
  },
  {
    path: '/admin/comentarios',
    name: 'admin-comments',
    component: () => import('../views/admin/AdminCommentsView.vue'),
    meta: { ...adminMeta, title: 'Moderar comentarios | AulaGo' },
  },

  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to) {
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0 }
  },
})

router.beforeEach((to) => {
  const session = useSessionStore(pinia)

  if (to.meta.requiresAuth && !session.authenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.allowedRoles && !session.hasRole(to.meta.allowedRoles)) {
    return session.homeRoute
  }
})

router.afterEach((to) => {
  document.title = to.meta.title || 'AulaGo'
})

export default router

