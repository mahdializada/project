import "@fortawesome/fontawesome-free/css/all.css"; // Ensure you are using css-loader
import Vue from "vue";
import Vuetify from "vuetify/lib";

Vue.use(Vuetify);

export default new Vuetify({
	theme: {
		themes: {
			light: {
				friqiBase: "#115598",
				BaseLight: "#104982",
				tabBackground  : "#F0F3F6"
			},
			dark: {
				friqiBase: "#115598",
				BaseLight: "#104982",
				tabBackground : "#111b27"
			},
		},
	},
	icons: {
		iconfont: "fa",
	},
});
