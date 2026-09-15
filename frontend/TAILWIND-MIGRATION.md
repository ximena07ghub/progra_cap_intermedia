# AulaGo — Tailwind + Vue/Vite

## Qué se hizo

Este proyecto ya tenía `tailwindcss@3.3.2`, `postcss` y `autoprefixer` instalados, pero faltaban los archivos de configuración que hacen que Vite procese las directivas de Tailwind y escanee los componentes `.vue`.

Se agregaron:

- `tailwind.config.js`
- `postcss.config.js`
- configuración de rutas `content` para `index.html` y `src/**/*.{vue,js,ts,jsx,tsx}`
- colores, tipografías y medidas de AulaGo como tokens de Tailwind
- `preflight: false` para no romper las vistas antiguas mientras se migran poco a poco

## Estrategia recomendada

No borres `main.css`, `components.css` ni `forms.css` todavía. Home, Login, Registro y parte del catálogo dependen de ellos.

A partir de ahora:

1. Las vistas nuevas se construyen con Tailwind.
2. Si un patrón se repite, conviértelo en un componente Vue (`src/components`).
3. Solo agrega CSS manual cuando Tailwind no sea suficiente o cuando sea una regla global realmente compartida.
4. Migra las vistas antiguas una por una, no todo el proyecto de golpe.

## Ejemplo

Antes:

```html
<div class="student-card">...</div>
```

```css
.student-card {
  padding: 24px;
  background: #1a221c;
  border: 1px solid rgba(255,255,255,.1);
}
```

Ahora:

```html
<div class="border border-white/10 bg-aula-surface p-6">...</div>
```

## Tokens de AulaGo disponibles

Puedes usar, entre otros:

- `bg-aula-bg`
- `bg-aula-surface`
- `text-aula-cream`
- `text-aula-muted`
- `text-aula-green`
- `text-aula-orange`
- `font-editorial`
- `font-mono`
- `max-w-aula`
- `shadow-aula`

## Flujo local

Desde la carpeta `frontend`:

```bash
npm install
npm run dev
```

Para validar antes de subir a GitHub:

```bash
npm run build
```

## Qué CSS debería quedarse a largo plazo

Idealmente el CSS manual debería terminar reducido a:

- variables/globales realmente necesarias;
- estilos muy especiales de animación o arte;
- alguna regla que no tenga sentido expresar con utilidades.

El layout, spacing, tipografía, colores, grids, responsive y estados visuales comunes pueden vivir en Tailwind.
