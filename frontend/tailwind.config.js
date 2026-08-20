/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#E3001B',
        secondary: '#F5C518',
        'neutral-dark': '#333333',
      }
    },
  },
  plugins: [],
}
