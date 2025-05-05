// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: false,

  app: {
    pageTransition: {
      name: 'fade',
      mode: 'out-in',
    },
    layoutTransition: {
      name: 'fade',
      mode: 'out-in',
    },
    head: {
      htmlAttrs: {
        lang: 'en',
      },
      link: [{ rel: 'icon', type: 'image/png', href: '/images/logo/icon.png' }],
    },
  },

  typescript: {
    shim: false,
  },

  modules: [
    'dayjs-nuxt',
    [
      '@pinia/nuxt',
      {
        autoImports: ['defineStore'],
      },
    ],
    'nuxt-icons',
  ],

  dayjs: {
    locales: ['en', 'fr', 'de', 'it'],
    plugins: ['relativeTime', 'utc', 'timezone', 'localeData'],
    defaultLocale: 'en',
  },

  imports: {
    dirs: ['stores'],
  },

  css: ['@/assets/scss/app.scss'],

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  build: {
    transpile: ['@vuepic/vue-datepicker'],
  },

  runtimeConfig: {
    // The private keys which are only available server-side
    // apiSecret: '123',
    // Keys within public are also exposed client-side
    public: {
      API_BASE_URL: process.env.API_BASE_URL,
      fullCalendarKey: process.env.FULL_CALENDAR_LICENSE_KEY,
      gtagId: process.env.GTAG_ID,
    },
  },

  compatibilityDate: '2024-10-02',
});