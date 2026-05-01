import colors from 'vuetify/es5/util/colors'

require('dotenv').config()
// eslint-disable-next-line nuxt/no-cjs-in-config
const {join} = require('path')
const {copySync, removeSync} = require('fs-extra')

export default {
  ssr: false,
  srcDir: __dirname,
  server: {
    port: process.env.APP_PORT || 5000 // default: 3000
    // host: '0.0.0.0', // default: localhost
  },

  head: {
    titleTemplate: '%s - CRM',
    title: process.env.APP_NAME,
    meta: [
      {charset: 'utf-8'},
      {name: 'viewport', content: 'width=device-width, initial-scale=1'},
      {hid: 'description', name: 'description', content: ''},
      {name: 'format-detection', content: 'telephone=no'},
    ],
    link: [{rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'}, {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap',
      media: 'all'
    }],
  },
  css: [

  ],
  plugins: [
    '~/plugins/global',
    {src: '~/plugins/animate.js', mode: 'client'},
    {src: '~/plugins/apexcharts.js', mode: 'client'},
    {src: '~plugins/v-mask', ssr: false},
    {src: '~plugins/filters'},
    {src: '~plugins/vue2-datepicker'},
    {src: '~/plugins/vue-print-nb.js', ssr: false},
    {src: '~/plugins/vidle.js', ssr: false},
    {src: '~/plugins/vue-shortkey.js', mode: 'client'},
    { src: '~plugins/vue-loading-overlay' },
    // // // filters

    {src: '~/filters/formatCurrency.js'},
    '~/plugins/vue-gates'
  ],
  components: false,
  buildModules: [
    // '@nuxtjs/eslint-module',
    // '@nuxtjs/stylelint-module',
    ['@nuxtjs/laravel-echo'],
    '@nuxtjs/vuetify',
    '@nuxtjs/moment',
    '@nuxtjs/composition-api/module',

  ],
  modules: [
    '@nuxtjs/axios',
    // '@nuxtjs/pwa',
    '@nuxtjs/router',
    '@nuxtjs/auth-next',
    ["vue-toastification/nuxt", {
      timeout: 1000,
      draggable: false
    }]
  ],
  axios: {
    baseURL: process.env.API_URL,
    proxy: false,
    credentials: true,
  },
  moment: {
    locales: ['ru'],
    defaultTimezone: 'Russia/Moscow'
  },
  auth: {
    strategies: {
      laravelSanctum: {
        provider: 'laravel/sanctum',
        url: process.env.API_URL,
        endpoints: {
          login: {url: '/login', method: 'post'},
          logout: {url: '/logout', method: 'post'},
          user: {url: '/user', method: 'get'}
        }
      }
    },
    redirect: {
      login: '/auth/signin',
      logout: '/',
      callback: '/auth/signin',
      home: '/'
    }
  },

  loading: {color: '#fb8c00', height: '8px'},

  router: {
    middleware: ['auth']
  },
  echo: {
    broadcaster: 'pusher',
    authModule: false,
    connectOnLogin: false,
    disconnectOnLogout: false,
    authEndpoint: process.env.APP_URL + '/api/broadcasting/auth',
    host: '127.0.0.1',
    key: process.env.PUSHER_APP_KEY,
    cluster: process.env.PUSHER_APP_CLUSTER,
    wsHost: process.env.WS_HOST,
    wsPort: process.env.WS_PORT,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws']
  },
  vuetify: {
    defaultAssets: false,
    icons: {
      iconfont: 'mdi',
    },
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: false,
      themes: {
        dark: {
          primary: colors.blue.darken2,
          accent: colors.grey.darken3,
          secondary: colors.amber.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
        },
        light: {
          primary: '#42a5f6',
          secondary: '#050b1f',
          accent: '#204165',
          background: '#f2f5f8',
        }
      },
    },
  },

  build: {},

  hooks: {
    generate: {
      done(generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '_nuxt')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  }
}
