/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          light: '#FF5252',
          DEFAULT: '#D32F2F',
          dark: '#B71C1C',
        },
        novel: {
          bg: '#F5F5F1',      // Color papel para lectura
          dark: '#1A1A1A',    // Texto principal
          muted: '#757575',   // Texto secundario o metadatos
          accent: '#2C3E50',  // Barras de navegación o footers
        }
      },
      fontFamily: {
        // Sugerencia para una lectura cómoda
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        serif: ['Lora', 'ui-serif', 'Georgia'], 
      }
    },
  },
  plugins: [],
}