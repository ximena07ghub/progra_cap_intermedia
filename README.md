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
4. **No ejecutes `npm run dev`**. No existe servidor Vite en esta versión.

## Tailwind

`assets/css/tailwind.css` ya viene compilado. El sitio funciona sin Node en la entrega.

Si modificas clases Tailwind y quieres recompilar durante desarrollo:

```bash
npm install
npm run css:watch
```

Ese comando solo recompila CSS. La URL sigue siendo la de Apache/XAMPP.

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

La navegación interna usa una sola pantalla por rol y JavaScript normal para cambiar de sección mediante `#hash`. No necesita Vue Router ni APIs.

- `estudiante/`: Dashboard, Todos los cursos, Mensajes, Kardex, Certificados y Mi cuenta. La columna derecha permanece visible con progreso y actividad.
- `instructor/`: Dashboard, Mis cursos, Crear curso, Mensajes, Ventas y Mi cuenta. La columna derecha resume alumnos, rating e ingresos.
- `admin/`: Dashboard, Categorías, Usuarios, Comentarios, Reportes y Mi cuenta. La columna derecha resume salud operativa y pendientes.

### Accesos de demostración

Mientras conectamos MySQL, el login infiere el rol por el correo:

- estudiante: `estudiante@demo.com`
- instructor: `instructor@demo.com`
- administrador: `admin@demo.com`

Puedes usar `Clave123!` como contraseña de prueba. En este checkpoint el login solo simula sesión PHP; la autenticación real se conectará a la base de datos después.

## Librerías

Swiper está guardado localmente en `assets/vendor/swiper/`, por lo que el carrusel no depende de Vue ni de un CDN. Pinia, Vue Router y Vite ya no participan en esta versión.

## Siguiente etapa

Conectar MySQL y reemplazar `data/catalog.php` y `data/portal.php` por consultas PHP reales manteniendo exactamente estas vistas.

## Checkpoint de correcciones: workspace + interacciones sin Vue

Este paquete ya no usa Vue, Vue Router, Pinia ni `swiper/vue` en tiempo de ejecución.

- El carrusel público usa **Swiper JavaScript local** (`assets/vendor/swiper`) y `assets/js/home-carousel.js`.
- El escenario del carrusel ahora recorta correctamente las diapositivas; una slide lateral ya no puede invadir la ficha del curso seleccionado.
- El texto sobre las imágenes tiene un degradado de contraste más fuerte y la ficha lateral se actualiza con el curso centrado.
- El dashboard del estudiante simula un curso abierto: cursos en progreso arriba, sesión actual al centro y siguientes lecciones a la derecha. Al seleccionar otro curso activo la vista se actualiza con JavaScript normal.
- Mensajes usa PHP para entregar los datos mock y JavaScript normal para cambiar de conversación y enviar mensajes temporales en la sesión del navegador.
- La columna derecha de estudiante, instructor y administrador queda fija y sin scroll en escritorio.
- El área central conserva scroll cuando la cantidad de información lo requiere; el hilo de mensajes y la lista de conversaciones tienen scroll interno independiente.

Para regenerar Tailwind durante desarrollo:

```bash
npm install
npm run css:watch
```

Esto solo recompila CSS; el servidor de la aplicación sigue siendo Apache/XAMPP.
>>>>>>> b51e18d (migracion vue a php puro)
