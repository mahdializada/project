import colors from "vuetify/es5/util/colors";

export default {
  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: "%s - frontend",
    title: "frontend",
    htmlAttrs: {
      lang: "en",
    },
    meta: [
      { charset: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { hid: "description", name: "description", content: "" },
      { name: "format-detection", content: "telephone=no" },
      { name: "csrf-token", content: "{{ csrf_token() }}" },
    ],
    link: [
      {
        rel: "icon",
        type: "image/x-icon",
        href: "/favicon.ico",
        rel: "stylesheet",
        type: "text/css",
        href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css",
        // rel:"stylesheet",  href: "https://emoji-css.afeld.me/emoji.css"
      },
    ],
    // link: [
    //   {
    //     rel: "stylesheet",
    //     href: "https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css",
    //   }
    // ],
    // script: [
    //   {
    //     src: "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js",
    //     src: "https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
    //   }
    // ],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/vuetify
    "@nuxtjs/vuetify",
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    // https://go.nuxtjs.dev/axios
    "@nuxtjs/auth-next",
    "@nuxtjs/axios",
    "@nuxtjs/toast",

    // // Simple usage
    // 'dropzone-nuxt',
    // // With options
    // ['dropzone-nuxt', { /* module options */ }],
  ],
  env: {
    backendUrl: "http://localhost:8000/",
  },

  // Axios module configuration: https://go.nuxtjs.dev/config-axios
  axios: {
    // Workaround to avoid enforcing hard-coded localhost:3000: https://github.com/nuxt-community/axios-module/issues/308
    baseURL: "http://localhost:8000/api/",
    credentials: true,
    changeOrigin: true,
    common: {
      Accept: "application/json",
    },
  },
  toast: {
    position: "top-right",
    duration: "3000",
  },
  router: {
    middleware: ["auth"],
  },
  auth: {
    strategies: {
      laravelSanctum: {
        url: "http://localhost:8000",

      },
      local: {
        user: {
          property: false,
          autoFetch: true,
        },
        token: {
          required: true,
          property: "token",
          type: "Bearer",
          global: true,
          maxAge: 24 * 60 * 30, // one month,
        },
        refreshToken: {
          maxAge: 24 * 60 * 365, // one year,
        },
        endpoints: {
          login: {
            url: "/auth/login",
            method: "post",
            propertyName: "token",
          },
          logout: {
            url: "/auth/logout",
            method: "post",
            propertyName: "token",
          },
          user: { url: "/auth/user", method: "get" },
        },
        autoLogout: false,
      },
    },
    redirect: {
      login: "/auth/login",
      logout: "/auth/login",
      home: "/",
      callback: false,
    },
  },
  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ["~/assets/variables.scss"],
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
      },
    },
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},
};
