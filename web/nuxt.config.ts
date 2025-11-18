import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      API_URL: "http://localhost:8000"
    }
  },

  css: [
    '@/assets/css/tailwind.css'
  ],

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  }
})
