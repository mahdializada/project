<template>
	<v-menu offset-y left transition="slide-y-transition">
		<template v-slot:activator="{ on }">
			<v-btn text :icon="$vuetify.breakpoint.smAndDown" v-on="on">
				<flag-icon
					:round="$vuetify.breakpoint.smAndDown"
					:flag="
						getTranslatedLanguage != null
							? getTranslatedLanguage.country_language.country.iso2.toLowerCase()
							: ''
					"
				>
				</flag-icon>
				<span v-show="$vuetify.breakpoint.mdAndUp && showLabel" class="ms-1">
					{{
						getTranslatedLanguage != null
							? `${getTranslatedLanguage.country_language.language.native} (${getTranslatedLanguage.country_language.country.iso2})`
							: ""
					}}</span
				>
				<v-icon v-if="showArrow" right>mdi-chevron-down</v-icon>
			</v-btn>
		</template>

		<v-list dense nav>
			<v-list-item
				v-for="language in languages"
				:key="language.id"
				@click="setLocale(language)"
			>
				<flag-icon
					class="me-1"
					:flag="language.country_language.country.iso2.toLowerCase()"
				></flag-icon>
				<v-list-item-title>{{
					`${language.country_language.language.native} (${language.country_language.country.iso2})`
				}}</v-list-item-title>
			</v-list-item>
		</v-list>
	</v-menu>
</template>

<script>
import FlagIcon from "../common/FlagIcon";
import { mapActions, mapGetters } from "vuex";
import Helper from "~/helpers/helpers";

/*
|---------------------------------------------------------------------
| Language Switcher Component
|---------------------------------------------------------------------
|
| Locale menu to choose the language based on the locales present in
| vue-i18n locales available array
|
*/
export default {
	components: {
		FlagIcon,
	},
	props: {
		// Show dropdown arrow
		showArrow: {
			type: Boolean,
			default: false,
		},
		// Show the country label
		showLabel: {
			type: Boolean,
			default: true,
		},
	},
	computed: {
		...mapGetters({
			languages: "languages/getActiveLanguages",
			getTranslatedLanguage: "translations/getTranslatedLanguage",
		}),
	},
	data() {
		return {};
	},
	async created() {
		await this.fetchActiveLanguages({ itemsPerPage: -1, key: "active" });
		if (process.client) {
			this.$vuetify.rtl =
				this.getTranslatedLanguage != null
					? this.getTranslatedLanguage.country_language.language.dir == "rtl"
					: false;
			this.$vuetify.lang.current =
				this.getTranslatedLanguage != null
					? this.getTranslatedLanguage.country_language.language.code
						? this.getTranslatedLanguage.country_language.language.code
						: "en"
					: "en";
		}
	},
	methods: {
		...mapActions({
			fetchActiveLanguages: "languages/fetchActiveLanguages",
			fetchTranslations: "translations/fetchTranslations",
		}),
		async setLocale(language) {
			if (
				this.$vuetify.lang.current !=
				language.country_language.language.code.toLowerCase()
			) {
				this.$nuxt.$loading.start();
				this.$vuetify.lang.current =
					language.country_language.language.code.toLowerCase();
				this.$vuetify.rtl = language.country_language.language.dir == "rtl";
				if (process.client) {
					if (this.$auth.loggedIn) {
						await this.fetchTranslations({
							language_id: language.id,
						});
					} else {
						await this.fetchTranslations({
							baseLanguage: true,
						});
					}
				}
				await this.updateUser();
				this.$nuxt.$loading.finish();
			}
		},
		async updateUser() {
			try {
				const response = await this.$axios.post(`users/changeLanguage`, {
					params: { language_id: this.getTranslatedLanguage.id },
				});
				if (response?.status === 200 && response?.data?.result) {
				} else {
				}
			} catch (error) {}
		},
	},
};
</script>
