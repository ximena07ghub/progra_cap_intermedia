/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  // The existing project already has its own reset/base CSS. Disabling
  // Tailwind preflight lets us migrate page-by-page without changing the
  // appearance of Home/Login/Register unexpectedly.
  corePlugins: {
    preflight: false,
    container: false,
  },
  theme: {
    extend: {
      colors: {
        aula: {
          bg: '#0c110e',
          'bg-soft': '#506857',
          surface: '#1a221c',
          'surface-strong': '#404d44',
          cream: '#f4f1e8',
          muted: '#aaa9a1',
          green: '#68b982',
          'green-dark': '#244934',
          'green-soft': '#21382a',
          orange: '#ef8a43',
          'orange-soft': '#f4b77f',
          yellow: '#c6bb35',
        },
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Arial', 'Helvetica', 'sans-serif'],
        editorial: ['DM Serif Display', 'Georgia', 'serif'],
        mono: ['Azeret Mono', 'Consolas', 'monospace'],
      },
      maxWidth: {
        aula: '1180px',
      },
      borderRadius: {
        aula: '13px',
        'aula-lg': '16px',
      },
      boxShadow: {
        aula: '0 25px 60px rgba(0, 0, 0, 0.24)',
        floating: '0 35px 90px rgba(0, 0, 0, 0.38)',
      },
    },
  },
  plugins: [],
}
