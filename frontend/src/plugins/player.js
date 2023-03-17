import Vue from "vue";
import VuePlyr from "vue-plyr";
import "vue-plyr/dist/vue-plyr.css";

Vue.use(VuePlyr, {
	plyr: {
		controls: [
			"play-large",
			"play",
			"progress",
			"current-time",
			"mute",
			"volume",
			"captions",
			"settings",
			"pip",
			"airplay",
			"fullscreen",
		],
		speed: {
			selected: 1,
			options: [0.5, 1, 1.5, 2],
		},
	},
});
