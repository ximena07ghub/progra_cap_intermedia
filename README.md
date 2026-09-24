EQUIPO
KEVIN YAHIR VILLARREAL HIRACHETA 19547994
DANIELA XIMENA OROZCO RAMIREZ 2173444
=======
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

