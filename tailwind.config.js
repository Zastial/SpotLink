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
        'primary-dark':'#241f78',
        'white': '#ffffff',
        'red': '#c41a1a',
        'red-dark': '#a71717',
        'green': '#33b020',
        'green-dark': '#278519',
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
        8:'3em',
        50: '50%',
        70: '70%',
        80: '80%',
      },
    },
  },
  plugins: [],
}
