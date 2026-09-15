# AulaGo — organización propuesta

## Vistas públicas

- `/` — Home existente.
- `/cursos` — catálogo existente.
- `/cursos/:slug` — descripción de curso.
- `/categorias` — categorías y filtrado.
- `/buscar?q=` — resultados de búsqueda.
- `/login` y `/registro` — autenticación visual existente.

## Vistas de estudiante

- `/estudiante` — dashboard posterior al login.
- `/estudiante/curso/:slug` — reproducción, lecciones y progreso.
- `/estudiante/cuenta` — perfil y preferencias.

## Componentes reutilizables agregados

- `PublicHeader.vue`
- `StudentHeader.vue`
- `CourseCard.vue`
- `ProgressCourseCard.vue`

## Siguiente fase recomendada

1. Extraer el header antiguo de Home/Cursos/Descripción a un componente compartido.
2. Reemplazar las interacciones DOM de `mainInteraction.js` por estado de Vue en componentes concretos.
3. Sustituir `demoSession.js` por autenticación real del backend/API.
4. Mover cursos/categorías de `src/data/courses.js` a una API.
5. Guardar progreso real del estudiante por curso y lección.
6. Añadir roles y rutas separadas si se implementa panel de instructor.
