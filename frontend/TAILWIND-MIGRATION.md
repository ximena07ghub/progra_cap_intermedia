# Migración Tailwind — completada para el frontend actual

## Resultado

Se retiraron las hojas antiguas:

- `src/assets/styles/main.css`
- `src/assets/styles/components.css`
- `src/assets/styles/forms.css`

La presentación de las vistas se expresa ahora con utilidades Tailwind dentro de los componentes Vue.

El único archivo CSS local restante es `src/assets/styles/tailwind.css`, requerido como punto de entrada de Tailwind y sin reglas personalizadas.

## Configuración visual

`tailwind.config.js` contiene:

### Paleta principal nueva

- `brand-orange` → `#ff7704`
- `brand-green` → `#73c103`
- `brand-blue` → `#20afe8`
- `brand-magenta` → `#ca1181`
- `brand-coral` → `#ff2c48`

Uso:

```html
<div class="bg-brand-orange text-white"></div>
<span class="text-brand-blue"></span>
```

### Tokens AulaGo existentes

También se conservan los tokens `aula-*` usados por dashboards de estudiante, instructor y administrador para no romper esas pantallas durante la limpieza.

## Landing

La Landing fue separada en:

- `HomeHero.vue`
- `HomeCourseCarousel.vue`
- `HomeHowItWorks.vue`
- `HomeLearningModes.vue`
- `HomeVideoShowcase.vue`
- `HomeTestimonials.vue`

`HomeView.vue` solo ensambla los componentes.

## Línea de aprendizaje

La sección **Aprende a tu ritmo, nivel por nivel** ya no usa cards horizontales. Ahora es una línea de tiempo vertical con cinco pasos.

## Swiper

El carrusel utiliza:

```text
swiper ^12.1.2
```

Instalación:

```bash
npm install
```

o si estás copiando únicamente los archivos modificados a otro proyecto:

```bash
npm install swiper@^12.1.2
```

El Coverflow usa `slideToClickedSlide`, de forma que una tarjeta lateral pasa al centro cuando se selecciona.
