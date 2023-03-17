<template>
	<div class="d-flex flex-column align-center justify-center h-full">
		<CTitle class="mb-4" :text="`Choose a study`" />
		<InputCard
			:title="'Studies'"
			:hasSearch="true"
			:hasPagination="true"
			:page="history[searchActive ? 'search' : 'current'].page"
			:pages="history[searchActive ? 'search' : 'current'].pages"
			@search="search"
			@disableSearch="disableSearch"
			@updatePage="
				(newPage) => {
					page = newPage;
				}
			"
		>
			<ItemsContainer
				v-model="form.product_study_id.$model"
				:items="
					history[searchActive ? 'search' : 'current'].items.slice(
						history[searchActive ? 'search' : 'current'].page * 21 - 21,
						history[searchActive ? 'search' : 'current'].page * 21
					)
				"
				:loading="loading"
				:no_data="
					history[searchActive ? 'search' : 'current'].no_data ? true : false
				"
			></ItemsContainer>
		</InputCard>
	</div>
</template>
<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
	components: { InputCard, ItemsContainer, CTitle },
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			type: "study",
			searchActive: false,
			loading: false,
			product_study_id: [],
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
			validateRule: GlobalRules.validate, // gloabl function fro validate
			selectedProductId: this.form.product_id.$model,
		};
	},
	watch: {
		["form.product_id.$model"](val) {
			if (this.selectedProductId != val) {
				this.selectedProductId = val;
				this.fetchStudyItems();
			}
		},
	},
	methods: {
		async loaded() {
			this.fetchStudyItems();
		},
		async validate() {
			// validate function must validate this step here and return true of false
			this.form.product_study_id.$touch();
			return !this.form.product_study_id.$invalid;
		},

		async fetchStudyItems() {
			this.loading = true;
			try {
				let res = await this.$axios.get(
					`single-sales/product-study/get?product_id=${this.selectedProductId}`,
					{
						params: {
							fetch_for_form: true,
						},
					}
				);
				let obj = {};
				if (res.data.length > 0) {
					obj = {
						items: res.data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(res.data.length / 21),
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
			// search_for_from
			this.loading = true;
			if (search) {
				this.searchActive = true;
				try {
					let data = [];
					var obj = {};
					for (let i = 0; i < this?.history?.current?.items?.length; i++) {
						const el = this?.history?.current?.items[i];
						if (el) {
							if (el.code.toLowerCase().indexOf(search.toLowerCase()) > -1) {
								data.push(el);
							}
						}
					}
					if (data.length > 0) {
						obj = {
							items: data.sort(this.sortByName),
							parent: null,
							pages: Math.ceil(data.length / 21),
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
					this.history.search = obj;
				} catch (err) {}
			}
			this.loading = false;
		},
		disableSearch() {
			this.searchActive = false;
		},
		sortByName(itemA, itemB) {
			const nameA = itemA.name.toLowerCase();
			const nameB = itemB.name.toLowerCase();
			return nameA.localeCompare(nameB);
		},
	},
};
</script>
