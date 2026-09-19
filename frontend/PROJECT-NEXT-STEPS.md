# AulaGo — siguientes avances

## Primer avance actual

Ya existe navegación para:

Landing → Login / Registro → Estudiante / Instructor / Administrador → Cursos

También existen catálogo, categorías, búsqueda, detalle de curso y diferentes vistas privadas por rol.

## Lo que sigue para convertir el prototipo en aplicación real

1. Sustituir la sesión simulada de Pinia/localStorage por autenticación del backend.
2. Validar roles y permisos también en el servidor.
3. Mover cursos, categorías, comentarios y progreso desde `src/data` a la API/base de datos.
4. Conectar inscripción y compra real.
5. Conectar contenido real de cursos: videos, documentos, actividades y niveles.
6. Guardar progreso por estudiante y lección.
7. Generar certificados desde datos reales.
8. Conectar panel del instructor con creación/edición real de cursos.
9. Conectar panel administrador con usuarios, categorías y moderación reales.
10. Añadir pruebas de navegación y permisos antes de publicar.

## Orden recomendado

```text
1. Frontend visual y navegación
2. Autenticación real
3. Cursos y categorías desde API
4. Inscripción / progreso
5. Instructor
6. Administrador
7. Pagos / certificados
8. Pruebas y despliegue
```
