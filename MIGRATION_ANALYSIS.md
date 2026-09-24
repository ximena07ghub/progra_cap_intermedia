# Análisis y decisiones de migración — AulaGo

## Punto de partida

La versión fuente tenía Vue 3 + Vite + Vue Router + Pinia + Tailwind + Swiper. El diseño y los datos mock ya estaban definidos, pero los archivos PHP de backend todavía no contenían lógica que fuera necesario preservar.

## Problema visual detectado en el primer checkpoint

El primer ZIP PHP reutilizaba un CSS compilado antiguo. Ese archivo todavía contenía colores y tipografías de una configuración anterior (`DM Serif Display`, fondo verde oscuro, etc.) y no conocía varias clases arbitrarias nuevas escritas en PHP. Por eso faltaban parte de los difuminados, la imagen derecha del Hero y la tipografía actual.

En este checkpoint `tailwind.css` se recompiló contra los archivos `.php` actuales y contra la configuración vigente:

- Sora para interfaz.
- Bricolage Grotesque para titulares/editorial.
- fondo `#100d10`.
- crema `#f5f0e8`.
- verde `#8eb67b`.
- naranja `#e88950`.
- plum, mist y demás colores existentes.

Además `custom.css` añade solo un ambiente radial muy sutil y estilos propios del workspace.

## Arquitectura definitiva de esta etapa

```text
Apache / XAMPP
      │
      ├── PHP: páginas, sesión y navegación
      ├── Tailwind CSS: sistema visual
      ├── JavaScript vanilla: filtros, workspace y formularios
      ├── Swiper JS local: carrusel
      └── MySQL: siguiente etapa
```

No hay `localhost:5173`, Vue Router, Pinia ni servidor frontend independiente.

## Conversión pública

| Vue | PHP |
|---|---|
| `HomeView.vue` + componentes Home | `index.php` + `includes/home/*` |
| `CoursesView.vue` | `cursos.php` |
| `CategoriesView.vue` | `categorias.php` + `catalog-filter.js` |
| `SearchView.vue` | `buscar.php` |
| `CourseDescriptionView.vue` | `curso.php` |
| `LoginView.vue` | `login.php` + `actions/login.php` |
| `RegisterView.vue` | `registro.php` + `actions/register.php` |
| `ForgotPasswordView.vue` | `recuperar-contrasena.php` |
| `CourseCard.vue` | `includes/components/course-card.php` |
| `swiper/vue` | Swiper JS en `assets/vendor/swiper` |

Los 24 cursos y las 12 categorías de la versión Vue se conservaron en `data/catalog.php`. Los datos de kardex, mensajes, ventas y administración se migraron a `data/portal.php` como fuente temporal antes de MySQL.

## Nuevo workspace

Se reemplaza la idea de múltiples pantallas internas independientes por una carcasa de trabajo consistente:

```text
┌──────────────┬──────────────────────────────┬──────────────────┐
│ barra lateral│ contenido de la sección      │ resumen persistente│
│ izquierda    │ seleccionada                 │ / progreso         │
└──────────────┴──────────────────────────────┴──────────────────┘
```

La barra lateral cambia secciones dentro de la misma página usando `workspace.js` y hashes. El contenido principal puede desplazarse sin perder la navegación o la columna de resumen.

### Estudiante

Sidebar: Dashboard, Todos los cursos, Mensajes, Kardex, Certificados, Mi cuenta. La derecha muestra progreso promedio, cursos activos, completados, actividad semanal y curso actual.

### Instructor

Sidebar: Dashboard, Mis cursos, Crear curso, Mensajes, Ventas, Mi cuenta. La derecha reemplaza el progreso académico por indicadores útiles para el instructor: alumnos, rating, ingresos, mensajes y última venta.

### Administrador

Sidebar: Dashboard, Categorías, Usuarios, Comentarios, Reportes, Mi cuenta. La derecha muestra salud operativa, pendientes y moderación, no métricas académicas.

Usar el mismo patrón para los tres roles es conveniente porque mantiene la aplicación coherente, pero cada rol conserva información y prioridades distintas.

## Archivos que ya no son necesarios

Se retiraron los headers internos anteriores (`student-header.php`, `workspace-header.php`) y el placeholder de migración. El workspace nuevo incluye su propia navegación lateral compartida en `includes/workspace/sidebar.php`.

## Próximo trabajo

1. Definir esquema MySQL.
2. Conectar registro/login con usuarios reales y `password_hash`/`password_verify`.
3. Sustituir datos mock del catálogo y portal por consultas.
4. Implementar compra/inscripción y contenido del curso.
5. Persistir progreso, mensajes, kardex, certificados, ventas y moderación.

## Corrección posterior: carrusel y workspace

- Se retiró el efecto `coverflow` del carrusel público porque junto con `overflow: visible` permitía que las slides laterales invadieran visualmente la ficha de información. Swiper sigue siendo infinito (`loop: true`), centrado y navegable por botones/clic/teclado, pero queda contenido en su columna.
- La interacción de la ficha del curso se implementa con `home-carousel.js`, sin componentes Vue.
- El dashboard de estudiante adopta el patrón de "curso abierto" solicitado: selector de cursos activos, bloque de sesión actual y lista de siguientes lecciones. Es una simulación alimentada por los mocks actuales y está preparada para sustituirse por progreso MySQL.
- Mensajes ya no cambia solamente el nombre del participante: cada conversación se serializa desde PHP a JSON local y `workspace.js` renderiza el hilo correcto al seleccionarla.
- La columna contextual derecha se hizo no desplazable en escritorio. El contenido central y las subáreas que sí necesitan desplazamiento conservan scroll.
