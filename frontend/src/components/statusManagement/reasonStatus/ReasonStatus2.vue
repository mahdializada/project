<template>
	<div>
		<div class="h-full d-flex align-center flex-column justify-center mt-5">
			<InputCard title="reasons">
				<div class="overflow-auto" style="height: 72vh">
					<div v-if="loading">
						<v-skeleton-loader
							class="my-1"
							v-for="i in 5"
							:key="i"
							type="list-item-avatar"></v-skeleton-loader>
					</div>
					<div v-else-if="allReasonsShow.length == 0 ">
						please add reason for this type of sub system
					</div>
					<span v-else>
						<div
							v-for="(item, i) in allReasonsShow"
							:key="i"
							@click="reasonClick(item)"
							class="reasonDiv my-2 px-2 me-1 d-flex justify-space-between"
							:style="
								item.selected ? `border:1px solid var(--v-primary-base)` : ``
							">
							<span class="mt-1 d-flex">
								<v-avatar size="30" color="light-blue lighten-5">
									<v-icon color="primary" size="20">mdi-text-box</v-icon>
								</v-avatar>
								<span class="ms-1" style="margin-top: 3px">{{
									item.title
								}}</span>
							</span>
							<span class="align-self-center">
								<v-icon v-if="item.selected" color="primary " class="mx-1"
									>mdi-check-circle</v-icon
								>
								<v-icon v-else class="mx-1" color="grey"
									>mdi-checkbox-blank-circle-outline</v-icon
								>
							</span>
						</div>
					</span>
				</div>
			</InputCard>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";

export default {
	name: "reasonsStep",
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
			allReasonsShow: [],
			loading: false,
		};
	},
	methods: {
		async validate() {
			if (this.form.$model.reasons.length > 0) return true;
			else return false;
		},
		async loaded() {
			await this.fetchReasons({
				slug: this.$route.params.slug,
				selectedTypes: this.form.$model.types,
				subsystem: this.form.$model.sub_systems,
			});
		},

		async fetchReasons(data) {
			this.loading = true;
			try {
				const response = await this.$axios.get("status-event-reasons", {
					params: data,
				});
				if (response?.status === 200) {
					this.allReasonsShow = response?.data;
					this.allReasonsShow = this.allReasonsShow.map((type) => {
						return {
							code: type.code,
							created_at: type.created_at,
							id: type.id,
							system_id: type.system_id,
							tabs: type.tabs,
							title: type.title,
							selected: false,
						};
					});
				}
			} catch (error) {}
			this.loading = false;
		},

		reasonClick(item) {
			this.allReasonsShow = this.allReasonsShow.map((row) => {
				if (row.id == item.id) {
					if (row.selected == true) {
						row.selected = false;
					} else {
						row.selected = true;
					}
				}
				return row;
			});

			this.form.$model.reasons = this.allReasonsShow
				.filter((row) => row.selected == true)
				.map((r) => r.id);
		},
	},
	components: { CTitle, InputCard },
};
</script>

<style>
.reasonDiv {
	border: 1px solid var(--v-background-darken1);
	border-radius: 10px;
	min-height: 60px;
}
.reasonDiv:hover {
	cursor: pointer;
}
</style>
