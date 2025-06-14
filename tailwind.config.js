/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        montserrat: ['Montserrat', 'sans-serif'],
      },
      colors: {
        golden: ' #F6C867',    
        cream: ' #FFF4E0',     
        coffee: ' #5C3E1F',
        salmon: ' #E67E53',    
        flour: ' #FAF9F6',     
        sunrise: ' #FFE8A3',   
      },
      keyframes: {
        'slide-in': {
          '0%': {
            transform: 'translateX(-100%)',
            opacity: '0',
          },
          '100%': {
            transform: 'translateX(0)',
            opacity: '1',
          },
        },
      },
      animation: {
        'slide-in': 'slide-in 0.5s ease-out forwards',
      },
    },
  },
  plugins: [],
}
