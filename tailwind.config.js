/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
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
        coffee: '#1c1917',
        salmon: ' #E67E53',
        flour: ' #FAF9F6',
        skyblue: '#89B6DC',    // bleu doux pour validation
        mossgreen: '#8DB580',  // vert naturel pour action
        pebble: '#D3D3D3',
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
  plugins: [
    
  ],
}