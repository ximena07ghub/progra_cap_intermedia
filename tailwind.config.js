/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './includes/**/*.php',
    './estudiante/**/*.php',
    './instructor/**/*.php',
    './admin/**/*.php',
    './actions/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          orange: '#ff7704',
          green: '#73c103',
          blue: '#20afe8',
          magenta: '#ca1181',
          coral: '#ff2c48',
        },
        aula: {
          bg: '#100d10',
          'bg-soft': '#171318',
          surface: '#19161b',
          'surface-strong': '#231e24',
          cream: '#f5f0e8',
          muted: '#aaa1a7',
          green: '#8eb67b',
          'green-dark': '#223125',
          'green-soft': '#1a251d',
          orange: '#e88950',
          'orange-soft': '#efb38d',
          yellow: '#cdbb73',
          plum: '#744a63',
          'plum-soft': '#241820',
          smoke: '#706971',
          mist: '#d9d0ca',
        },
      },
      fontFamily: {
        sans: ['Sora', 'Arial', 'Helvetica', 'sans-serif'],
        editorial: ['Bricolage Grotesque', 'Sora', 'Arial', 'sans-serif'],
        mono: ['Sora', 'Arial', 'sans-serif'],
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
