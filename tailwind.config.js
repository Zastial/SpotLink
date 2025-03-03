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
        'partialrefuse' : '#FEC3A6',
        'partialrefuse-dark' : '#feb692',
        'validate' : '#C9FBAC',
        'validate-dark' : '#b1fb87',
        'awaiting' : '#EFE9AE',
        'awaiting-dark' : '#efe68f',
        'totalrefuse' : '#F0604D',
        'totalrefuse-dark' : '#f0503b'
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
  safelist: [
    {
      pattern: /^bg-(red|blue|green|yellow|purple|gray|pink|indigo|teal|cyan|lime|partialrefuse|validate|awaiting|totalrefuse)(-(100|200|300|400|500|600|700|800|900|dark))?$/,
      variants: ["hover"],
    },
  ],
  plugins: [],
}
