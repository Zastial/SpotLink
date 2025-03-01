/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#2f299a',
        'white': '#ffffff'
      },
      fontFamily: {
        lexend: ['"Lexend Deca"', 'sans-serif'],
      },
      spacing: {
        0.1:'0.1em',
        0.2:'0.2em',
        0.25:'0.25em',
        0.30:'0.30em',
        0.35:'0.35em',
        80: '80%',
      },
    },
  },
  plugins: [],
}
