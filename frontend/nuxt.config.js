import config from "./src/configs";

const { gaId } = config.analytics;

export default {


	// target: "static",
	srcDir: "src/",
	// generate: {
	//   dir: "public",
	// },
	loading: "./components/LoadingComponent.vue",

	ssr: false,
	// target: 'static',

	head: {
		titleTemplate: "%s",
		title: "Smart Friqi",
		meta: [
			{
				charset: "utf-8",
			},
			{
				name: "viewport",
				content: "width=device-width, initial-scale=1",
			},
			{
				hid: "description",
				name: "description",
				content: "",
			},
		],
		link: [
			{
				rel: "icon",
				type: "image/svg",
				href: "/favicon.svg",
			},
			{
				rel: "stylesheet",
				href: "https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap",
			},
			{
				rel: "stylesheet",
				href: "https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap",
			},
			{
				rel: "stylesheet",
				href: "/assets/css/owl.min.css",
			},
			...config.icons.map((href) => ({
				rel: "stylesheet",
				href,
			})),
		],
		script: [
			{
				src: "/assets/script/lottie-player.js",
			},
			{
				src: "/assets/script/jquery.js",
			},
			{
				src: "/assets/script/owl.min.js",
			},
			{
				src: "/assets/script/script.js",
			},
			{
				src: "/assets/script/audio.js",
			},
		],
	},

	css: ["~/assets/scss/theme.scss", "~/assets/main.scss"],

	plugins: [
		// plugins
		{
			src: "~/plugins/axios2.js",
			mode: "all",
		},
		{
			src: "~/plugins/Vuelidate.js",
			mode: "all",
		},
		{
			src: "~/plugins/vue-toastr.js",
			mode: "client",
		},
		{
			src: "~/plugins/lodash.js",
			mode: "all",
		},
		{
			src: "~/plugins/animate.js",
			mode: "client",
		},
		{
			src: "~/plugins/apexcharts.js",
			mode: "client",
		},
		{
			src: "~/plugins/clipboard.js",
			mode: "client",
		},
		{
			src: "~/plugins/vue-shortkey.js",
			mode: "client",
		},
		{
			src: "~/plugins/draggable.js",
			mode: "client",
		},
		{
			src: "~/plugins/csvExport.js",
			mode: "client",
		},
		{
			src: "~/plugins/vuetify.js",
			mode: "client",
		},
		{
			src: "~/plugins/global.js",
			mode: "all",
		},
		{
			src: "~/plugins/alertNasim.js",
			mode: "all",
		},
		{
			src: "~/plugins/nasToaster.js",
			mode: "client",
		},
		{
			src: "~/plugins/editor.js",
			mode: "client",
		},
		{
			src: "~/plugins/echo.js",
			mode: "client",
		},
		{
			src: "~/plugins/player.js",
			mode: "client",
		},
		{
			src: "~/plugins/chart.js",
			mode: "client",
		},

		// // // filters
		{
			src: "~/filters/capitalize.js",
		},
		{
			src: "~/filters/lowercase.js",
		},
		{
			src: "~/filters/uppercase.js",
		},
		{
			src: "~/filters/formatDate.js",
		},
		{
			src: "~/filters/helpers.js",
		},
		{
			src: "~/filters/kFormatter.js",
		},
	],

	buildModules: [
		// https://go.nuxtjs.dev/vuetify
		[
			"@nuxtjs/vuetify",
			{
				customVariables: ["~/assets/scss/vuetify/variables/_index.scss"],
				optionsPath: "~/configs/vuetify.js",
				treeShake: true,
				defaultAssets: {
					font: false,
				},
			},
		],
	],

	// Modules (https://go.nuxtjs.dev/config-modules)
	modules: [
		"@nuxtjs/auth-next",
		"@nuxtjs/google-gtag",
		"@nuxtjs/axios",
		"vue-sweetalert2/nuxt",
		[
			"vuejs-google-maps/nuxt",
			{
				apiKey: "AIzaSyDNtpPnk9q69exb8wEGA_XYIM5YJlJR9aI",
				libraries: ["places"],
			},
		],
		"vue2-editor/nuxt",
	],

	sweetalert: {
		confirmButtonColor: "#FF5252",
		cancelButtonColor: "#2c7be5",
	},

	"google-gtag": {
		id: gaId,
		debug: true, // enable to track in dev mode
		disableAutoPageTrack: false, // disable if you don't want to track each page route with router.afterEach(...).
	},
	server: {
		port: 3004,
		// host: "0.0.0.0",
	},
	axios: {
		// baseURL: "https://clientbackend.oredoh.org/api/v1",
		baseURL: "http://localhost:8000/api/v1",
		//	baseURL: "http://192.168.3.111:8000/api/v1",
		credentials: true,
		changeOrigin: true,
		common: {
			Accept: "application/json",
		},
	},

	auth: {
		cookie: false,
		localStorage: {
			prefix: "auth.",
		},
		redirect: {
			login: "/auth/signin",
			logout: "/auth/signin",
			callback: "/auth/signin",
			home: false,
		},
		strategies: {
			local: {
				token: {
					property: "token",
					global: true,
					required: true,
					type: "Bearer",
				},
				user: {
					property: "data",
					autoFetch: true,
				},
				endpoints: {
					login: {
						url: "/login",
						method: "post",
					},
					logout: {
						url: "/logout",
						method: "post",
					},
					user: {
						url: "/auth/user",
						method: "get",
					},
				},
			},
		},
	},
	router: {
		middleware: ["auth", "authScope"],
	},

	build: {},
};

// full code of nuxt.config.js
