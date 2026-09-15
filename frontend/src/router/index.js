import { createRouter, createWebHistory } from 'vue-router'
import { isDemoAuthenticated } from '../utils/demoSession.js'

const routes = [
  { path: '/', name: 'home', component: () => import('../views/HomeView.vue'), meta: { title: 'AulaGo | Aprende a tu ritmo' } },
  { path: '/login', name: 'login', component: () => import('../views/auth/LoginView.vue'), meta: { title: 'Iniciar sesión | AulaGo' } },
  { path: '/registro', name: 'register', component: () => import('../views/auth/RegisterView.vue'), meta: { title: 'Crear cuenta | AulaGo' } },
  { path: '/cursos', name: 'courses', component: () => import('../views/public/CoursesView.vue'), meta: { title: 'Cursos | AulaGo' } },
  { path: '/cursos/:slug', name: 'course-description', component: () => import('../views/public/CourseDescriptionView.vue'), meta: { title: 'Curso | AulaGo' } },
  { path: '/curso', redirect: '/cursos/neuro-habitos' },
  { path: '/categorias', name: 'categories', component: () => import('../views/public/CategoriesView.vue'), meta: { title: 'Categorías | AulaGo' } },
  { path: '/buscar', name: 'search', component: () => import('../views/public/SearchView.vue'), meta: { title: 'Búsqueda | AulaGo' } },
  {
    path: '/estudiante',
    name: 'student-dashboard',
    component: () => import('../views/student/StudentDashboardView.vue'),
    meta: { title: 'Mi espacio | AulaGo', requiresAuth: true },
  },
  {
    path: '/estudiante/cuenta',
    name: 'student-account',
    component: () => import('../views/student/StudentAccountView.vue'),
    meta: { title: 'Mi cuenta | AulaGo', requiresAuth: true },
  },
  {
    path: '/estudiante/curso/:slug?',
    name: 'student-course',
    component: () => import('../views/student/StudentCourseView.vue'),
    meta: { title: 'Mi curso | AulaGo', requiresAuth: true },
  },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0 }
  },
})

router.beforeEach((to) => {
  if (to.meta.requiresAuth && !isDemoAuthenticated()) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
})

router.afterEach((to) => {
  document.title = to.meta.title || 'AulaGo'
})

export default router
