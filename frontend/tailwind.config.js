/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#C8102E', // Softer, more corporate Frito-Lay red (Pantone 186 C)
        secondary: '#FCA311', // Warmer, more golden yellow
        'neutral-dark': '#333333',
        'neutral-light': '#F8F9FA'
      }
    },
  },
  plugins: [],
}
