<template>
	<div>
		<div class="h-full d-flex align-center flex-column justify-center mt-5">
			<CTitle :text="`status_events`" />
			<div class="w-full d-flex">
				<InputCard
					:title="this.$tr('sub_systems')"
					:hasSearch="true"
					:hasPagination="false"
					@search="(v) => (searchSubSystem = v)">
					<ItemsContainer
						v-model="form.sub_systems.$model"
						:items="filterSubSystems"
						:loading="loading"
						:no_data="filterSubSystems.length < 1"
						height="250px"></ItemsContainer>
				</InputCard>
			</div>
			<InputCard
				class="mt-4"
				:title="this.$tr('types')"
				:hasSearch="false"
				:hasPagination="false">
				<div style="height: 230px" class="d-flex my-3 overflow-auto flex-wrap">
					
					<div
						style="height: 100%; width: 100%"
						class="d-flex align-center justify-center"
						v-if="form.$model.sub_systems.length == 0">
						Select a sub system first
					</div>

					<div class="d-flex" v-if="isFetchingTypes">
						<v-skeleton-loader
							v-for="i in 5"
							:key="i"
							width="200"
							height="50"
							class="mx-2 d-flex align-center"
							type="text,avatar">
						</v-skeleton-loader>
					</div>
					<div
						v-else
						v-for="(item, index) in types"
						:key="index"
						@click="typeClick(item)"
						class="type-div mx-1 d-flex align-center justify-space-between"
						:style="
							item.selected ? `border:1px solid var(--v-primary-base)` : ``
						">
						<span class="mx-1">{{ item.translated }}</span>
						<v-icon v-if="item.selected" color="primary " class="mx-1"
							>mdi-check-circle</v-icon
						>
						<v-icon v-else class="mx-1" color="grey"
							>mdi-checkbox-blank-circle-outline</v-icon
						>
					</div>
				</div>
			</InputCard>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import Icons from "~/configs/menus/menuIcons.js";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";

export default {
	name: "StatusStep",
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
			searchSubSystem: "",
			searchSubSystemType: "",
			subSystems: [],
			loading: false,
			isFetchingTypes: false,
			types: [],
		};
	},
	computed: {
		filterSubSystems: function () {
			if (this.searchSubSystem) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchSubSystem?.toLowerCase());
				return this.subSystems.filter(filter);
			}
			return this.subSystems;
		},
		filterTypes: function () {
			if (this.searchSubSystemType) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchSubSystemType?.toLowerCase());
				return this.types.filter(filter);
			}
			return this.types;
		},
	},

	watch: {
		"form.sub_systems.$model": async function (id) {
			if (id.length > 1) {
				this.isFetchingTypes = true;
				const item = this.subSystems.find((row) => row.id == id);
				const response = await this.$axios.get("subsystems", {
					params: { subsystem: item.name },
				});
				this.types = response.data;
				this.types = this.types.map((type) => {
					return {
						id: this.generateID(),
						translated: this.$tr(type),
						type,
						selected: false,
					};
				});
				this.isFetchingTypes = false;
			} else {
				this.types = [];
			}
		},
	},

	methods: {
		async validate() {
			if (
				this.form.$model.sub_systems.length > 0 &&
				this.form.$model.types.length > 0
			) {
				return true;
			}
			return false;
		},
		async loaded() {
			this.types = [];
			this.loading = true;
			await this.fetchSubSystems();
			this.loading = false;
		},
		async fetchSubSystems() {
			try {
				const response = await this.$axios.get(
					`systems_sub_systems?system_name=${this.$route.params.slug}`,
				);
				if (response?.status === 200 && response?.data?.result) {
					const subs = response?.data?.data;
						for (let i = 0; i < subs.length; i++) {
							if (
							!(subs[i].name == "Labels" || subs[i].name == "Reasons")
							) {
							subs[i];
							subs[i].icon = "";
							let b = subs[i].phrase;
							if (Icons[b]) subs[i].icon = Icons[b];
							else if (Icons[b + "s"]) subs[i].icon = Icons[b + "s"];
							else if (Icons[b + "_management_system"])
								subs[i].icon = Icons[b + "_management_system"];
							else if (Icons[b.substr(0, b.length - 1) + "_list"])
								subs[i].icon = Icons[b.substr(0, b.length - 1) + "_list"];
								else {
									subs[i].icon = "<h1 style='color:#2e7be6'>"+b.charAt(0).toUpperCase()+"</h1>";
								}
							}
						}
						console.log(Icons);
						console.log(subs);
						this.subSystems = subs;
				} else {
					this.$toasterNA("red", this.$tr("something_went_wrong"));
				}
			} catch (error) {
				if (error?.response?.status === 404 && !error?.response?.data?.result) {
					this.$toasterNA("red", this.$tr("sub_system_not_found"));
				} else {
					HandleException.handleApiException(this, error);
				}
			}
		},
		generateID() {
			return (
				"Id" +
				Math.floor(
					(Date.now() *
						Math.floor(
							Math.random() * Math.floor(Math.random() * Date.now()),
						)) /
						(Math.random() * 1000000000),
				)
			);
		},
		typeClick(params) {
			this.types = this.types.map((row) => {
				if (row.type == params.type) {
					if (row.selected == true) {
						row.selected = false;
					} else {
						row.selected = true;
					}
				}
				return row;
			});
			this.form.$model.types = this.types
				.filter((row) => row.selected == true)
				.map((r) => r.type);
		},
	},

	components: { CTitle, InputCard, ItemsContainer, HorizontalItemContainer },
};
</script>

<style>
.type-div {
	height: 50px;
	width: 25%;
	border-radius: 10px;
	border: 1px solid var(--v-background-darken1);
}
.type-div:hover {
	cursor: pointer;
}
</style>
