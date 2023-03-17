<template>
	<div class="w-full h-full d-flex align-center-justify-center">
		<div class="w-full h-full mt-5">
			<div class="mb-3 d-flex flex-column flex-md-row">
				<div class="me-md-2 w-full">
					<CRangeSlider
						v-model="rangeModel"
						title="Range Slider"
						:min="0"
						:max="90"
					/>
				</div>
				<div class="me-md-2 w-full">
					<CGenderSelection
						title="Gender"
						v-model="gender"
						:items="[
							{
								value: 'male',
								label: 'Male',
							},
							{
								value: 'female',
								label: 'Female',
							},
							{
								value: 'all',
								label: 'All',
							},
						]"
					/>
				</div>
			</div>
			<div class="mb-3">
				<InputCard
					:title="'Landing Page Style'"
					:hasSearch="true"
					:hasPagination="true"
					:page="history2[search2Active ? 'search' : 'current'].page"
					:pages="history2[search2Active ? 'search' : 'current'].pages"
					@search="search2"
					@disableSearch="disableSearch2"
					@updatePage="
						(newPage) => {
							history2[search2Active ? 'search' : 'current'].page = newPage;
						}
					"
				>
					<CTemplateSelection
						v-model="selectedTemplate"
						:items="
							history2[search2Active ? 'search' : 'current'].items.slice(
								history2[search2Active ? 'search' : 'current'].page * 8 - 8,
								history2[search2Active ? 'search' : 'current'].page * 8
							)
						"
						:loading="loading"
						:no_data="
							history[searchActive ? 'search' : 'current'].no_data
								? true
								: false
						"
					/>
				</InputCard>
			</div>
			<div class="mb-3">
				<InputCard
					:title="'Languages'"
					:hasSearch="true"
					:hasPagination="true"
					:page="history[searchActive ? 'search' : 'current'].page"
					:pages="history[searchActive ? 'search' : 'current'].pages"
					@search="search"
					@disableSearch="disableSearch"
					@updatePage="
						(newPage) => {
							history[searchActive ? 'search' : 'current'].page = newPage;
						}
					"
				>
					<ItemsContainer
						v-model="selectedItems"
						:items="
							history[searchActive ? 'search' : 'current'].items.slice(
								history[searchActive ? 'search' : 'current'].page * 14 - 14,
								history[searchActive ? 'search' : 'current'].page * 14
							)
						"
						:loading="loading"
						:no_data="
							history[searchActive ? 'search' : 'current'].no_data
								? true
								: false
						"
						:height="'260px'"
					></ItemsContainer>
				</InputCard>
			</div>
		</div>
	</div>
</template>
<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import CTemplateSelection from "../../new_form_components/Inputs/CTemplateSelection.vue";
import CRangeSlider from "../../new_form_components/Inputs/CRangeSlider.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CGenderSelection from "../../new_form_components/Inputs/CGenderSelection.vue";

export default {
	components: {
		InputCard,
		ItemsContainer,
		CTemplateSelection,
		CRangeSlider,
		CTextField,
		CGenderSelection,
	},
	data() {
		return {
			searchActive: false,
			loading: false,
			selectedItems: null,
			history: {
				current: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
				search: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
			},

			// template items
			selectedTemplate: null,
			search2Active: false,
			loading2: false,
			history2: {
				current: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
				search: {
					items: [],
					pages: 0,
					page: 1,
					parent: null,
				},
			},

			// range model
			rangeModel: [0, 10],
			gender: null,
		};
	},
	methods: {
		async loaded() {
			this.fetchLanguages();
			this.fetchTemplatees();
		},
		async validate() {
			return true;
		},
		async fetchLanguages() {
			this.loading = true;
			try {
				let res = await this.$axios.get("languages", {
					params: {
						itemsPerPage: -1,
						withCountries: true,
					},
				});
				let data = [];
				for (let i = 0; i < res?.data?.data?.length; i++) {
					const el = res?.data?.data[i];
					data.push({
						id: el.country_language_id,
						name: el.language_native,
						english_name: el.language_name,
						country_name: el.country_name,
						iso2: el.iso2.toLowerCase(),
						type: "language",
					});
				}
				let obj = {};
				if (data?.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(data.length / 14),
						page: 1,
					};
				} else {
					obj = {
						no_data: true,
						items: [],
						parent: null,
						pages: 0,
						page: 1,
					};
				}
				this.history.current = obj;
				this.history.parent = obj;
			} catch (error) {}

			this.loading = false;
		},
		async search(search) {
			this.loading = true;
			let data = [];
			var obj = {
				no_data: true,
				items: [],
				parent: null,
				pages: 0,
				page: 1,
			};
			if (search) {
				this.searchActive = true;
				for (let i = 0; i < this?.history?.current?.items?.length; i++) {
					const el = this?.history?.current?.items[i];
					if (el) {
						if (
							el.name.toLowerCase().indexOf(search.toLowerCase()) > -1 ||
							el.english_name.toLowerCase().indexOf(search.toLowerCase()) >
								-1 ||
							el.country_name.toLowerCase().indexOf(search.toLowerCase()) > -1
						) {
							data.push(el);
						}
					}
				}
			}

			if (data.length > 0) {
				obj = {
					items: data,
					parent: null,
					pages: Math.ceil(data.length / 21),
					page: 1,
				};
			}
			this.history.search = obj;
			this.loading = false;
		},
		sortByName(itemA, itemB) {
			const nameA = itemA.name.toLowerCase();
			const nameB = itemB.name.toLowerCase();
			return nameA.localeCompare(nameB);
		},
		disableSearch() {
			this.searchActive = false;
		},

		// template selection
		async fetchTemplatees() {
			this.loading2 = true;
			try {
				// let res = await this.$axios.get("languages", {
				// 	params: {
				// 		itemsPerPage: -1,
				// 		withCountries: true,
				// 	},
				// });
				let data = [
					{
						id: 1,
						name: "Template 1 ",
						image: "https://picsum.photos/500/300?image=60",
					},
					{
						id: 2,
						name: "Template 2 ",
						image: "https://picsum.photos/500/300?image=60",
					},
					{
						id: 3,
						name: "Template 3 ",
						image: "https://picsum.photos/500/300?image=60",
					},
					{
						id: 4,
						name: "Template 4 ",
						image: "https://picsum.photos/500/300?image=60",
					},
				];

				let obj = {};
				if (data?.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(data.length / 8),
						page: 1,
					};
				} else {
					obj = {
						no_data: true,
						items: [],
						parent: null,
						pages: 0,
						page: 1,
					};
				}
				this.history2.current = obj;
				this.history2.parent = obj;
			} catch (error) {}

			this.loading2 = false;
		},
		async search2(search) {
			this.loading2 = true;
			let data = [];
			var obj = {
				no_data: true,
				items: [],
				parent: null,
				pages: 0,
				page: 1,
			};
			if (search) {
				this.search2Active = true;
				for (let i = 0; i < this?.history2?.current?.items?.length; i++) {
					const el = this?.history2?.current?.items[i];
					if (el) {
						if (el.name.toLowerCase().indexOf(search.toLowerCase()) > -1) {
							data.push(el);
						}
					}
				}
			}

			if (data.length > 0) {
				obj = {
					items: data,
					parent: null,
					pages: Math.ceil(data.length / 21),
					page: 1,
				};
			}
			this.history2.search = obj;
			this.loading2 = false;
		},
		disableSearch2() {
			this.search2Active = false;
		},
	},
};
</script>
