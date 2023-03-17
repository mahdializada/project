<template>
	<v-app>
		<div class="layout-content ma-auto w-full">
			<nuxt />
		</div>
		<LoadingComponent :loadingProps.sync="loading" />
		<div
			style="
				position: fixed;
				top: 60px;
				right: 10px;
				min-width: 22%;
				max-width: 40%;
				z-index: 10000000;
			">
			<NasTosater></NasTosater>
		</div>
	</v-app>
</template>
<script>
import { mapActions } from "vuex";
import LoadingComponent from "../components/LoadingComponent.vue";
import NasTosater from "../components/alert/NasTosater.vue";

export default {
	components: {
		LoadingComponent,
		NasTosater,
	},
	async created() {
		if (process.client) {
			if (this.$auth.loggedIn) {
				await this.fetchTranslations({
					language_id: this.$auth.user.translated_language_id,
				});
			} else {
				await this.fetchTranslations({
					baseLanguage: true,
				});
			}
			this.loading = false;
		}
	},
	data() {
		return {
			loading: true,
		};
	},
	methods: {
		...mapActions({
			fetchTranslations: "translations/fetchTranslations",
		}),
	},
};
</script>

<style scoped>
.layout-side {
	width: 420px;
}

.layout-content {
	max-width: 480px;
}
</style>
