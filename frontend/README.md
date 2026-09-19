# AulaGo — Vue 3 + Vite + Tailwind

Frontend de la plataforma AulaGo.

## Stack actual

- Vue 3
- Composition API con `<script setup>`
- Vue Router
- Pinia
- Tailwind CSS
- Swiper para el carrusel Coverflow
- Vite

## Instalación

Desde la carpeta `frontend`:

```bash
npm install
npm run dev
```

La aplicación queda disponible normalmente en:

```text
http://localhost:5173/
```

Para validar producción:

```bash
npm run build
```

## CSS

El proyecto ya no mantiene hojas de estilo manuales para pantallas o componentes.

Solo queda:

```text
src/assets/styles/tailwind.css
```

Ese archivo contiene únicamente las tres directivas necesarias para que Tailwind funcione:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Swiper importa su CSS interno directamente desde el paquete npm dentro del componente del carrusel.

## Rutas principales

- `/` — Landing
- `/login` — Iniciar sesión
- `/registro` — Crear cuenta
- `/cursos` — Catálogo
- `/categorias` — Categorías
- `/buscar` — Búsqueda
- `/estudiante` — Espacio estudiante
- `/instructor` — Panel instructor
- `/admin` — Panel administrador

Consulta `PROJECT-STATUS.md` para las rutas completas y la forma de probar cada rol.
