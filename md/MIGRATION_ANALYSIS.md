# Estado de migración — AulaGo

## Stack actual

- PHP servido por XAMPP/Apache o por el servidor integrado de PHP.
- Tailwind CSS + CSS personalizado.
- JavaScript vanilla.
- Swiper JS local.
- Sesiones PHP para la simulación de autenticación.
- `data/runtime/payment-state.json` como persistencia temporal de compras/pagos hasta MySQL.
- PayPal Orders v2 preparado para Sandbox.
- MySQL pendiente para persistencia real.

## Vistas PHP ya integradas

### Públicas

- `index.php`
- `cursos.php`
- `categorias.php`
- `buscar.php`
- `curso.php`
- `login.php`
- `registro.php`
- `recuperar-contrasena.php`

### Estudiante

- `estudiante/index.php`: Dashboard, Todos los cursos, Mensajes, Kardex, Certificados y Cuenta.
- `estudiante/checkout.php`: checkout previo a PayPal.

### Instructor

- `instructor/index.php`: Dashboard, Cursos, Crear, Mensajes, Ventas y Cuenta.
- La sección Ventas puede leer transacciones PayPal confirmadas en la sesión actual.

### Administrador

- `admin/index.php`: Dashboard, Categorías, Usuarios, Pagos, Comentarios, Reportes y Cuenta.

## Auditoría de la antigua migración Vue -> PHP

El ZIP PHP actual ya no contiene componentes `.vue` activos. La pantalla funcional que había quedado fuera del traslado era el checkout/compra. Esta actualización la recupera y reconecta el flujo completo.

Los archivos `includes/layout/student-header.php`, `includes/layout/workspace-header.php` y `includes/layout/migration-placeholder.php` no tenían referencias activas y se eliminan.

## Flujo de compra

`Catálogo -> Detalle del curso -> Login si hace falta -> Checkout -> PayPal -> Estudiante / Todos los cursos`

Para cursos gratuitos:

`Catálogo -> Detalle -> Login si hace falta -> Inscripción gratuita -> Estudiante / Todos los cursos`

## Estado temporal antes de MySQL

Tras una captura PayPal correcta, PHP registra temporalmente en `data/runtime/payment-state.json`:

- curso adquirido;
- proveedor;
- order ID;
- monto/moneda;
- estado;
- fecha y estudiante.

Eso permite reflejar el evento en Estudiante, Instructor y Admin incluso después de cerrar sesión y cambiar de rol. El JSON se ignora en Git y se reemplazará por MySQL.

## Pendiente real

1. Base de datos MySQL para usuarios, compras, inscripciones y pagos.
2. Webhook PayPal para reconciliación independiente del navegador.
3. Contenido/reproductor real de lecciones y progreso persistente.
4. Sustituir login mock por usuarios y roles reales.
5. Persistir mensajes, kardex, certificados, ventas y moderación.
