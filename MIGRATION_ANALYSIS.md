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

#=======
# AulaGo — migración PHP/XAMPP

Este checkpoint reemplaza la capa Vue/Vite por páginas PHP servidas directamente por Apache/XAMPP, conservando Tailwind, JavaScript y Swiper.

## Cómo abrir el proyecto

1. Extrae la carpeta como `C:\xampp\htdocs\prograCapaInter_php`.
2. En XAMPP enciende **Apache**. MySQL puede quedar apagado en este checkpoint porque los datos siguen siendo mock.
3. Abre `http://localhost/prograCapaInter_php/`.


## Páginas públicas migradas

- `index.php`: Home completo, incluido el carrusel infinito con Swiper JS.
- `cursos.php`: catálogo completo de 24 cursos.
- `categorias.php`: buscador, categorías y filtro por nivel en la misma pantalla.
- `buscar.php`: búsqueda pública.
- `curso.php?slug=...`: ficha completa de un curso.
- `login.php`: acceso.
- `registro.php`: registro estudiante/instructor.
- `recuperar-contrasena.php`: recuperación simulada.

## Workspaces por rol

- `estudiante/`: Dashboard, Todos los cursos, Mensajes, Kardex, Certificados y Mi cuenta. La columna derecha permanece visible con progreso y actividad.
- `instructor/`: Dashboard, Mis cursos, Crear curso, Mensajes, Ventas y Mi cuenta. La columna derecha resume alumnos, rating e ingresos.
- `admin/`: Dashboard, Categorías, Usuarios, Comentarios, Reportes y Mi cuenta. La columna derecha resume salud operativa y pendientes.

### Accesos de demostración

Mientras conectamos MySQL, el login infiere el rol por el correo:

- estudiante: `estudiante@demo.com`
- instructor: `instructor@demo.com`
- administrador: `admin@demo.com`


## Siguiente etapa

Conectar MySQL y reemplazar `data/catalog.php` y `data/portal.php` por consultas PHP reales manteniendo exactamente estas vistas.

1. Definir esquema MySQL.
2. Conectar registro/login con usuarios reales y `password_hash`/`password_verify`.
3. Sustituir datos mock del catálogo y portal por consultas.
4. Implementar compra/inscripción y contenido del curso.
5. Persistir progreso, mensajes, kardex, certificados, ventas y moderación.

