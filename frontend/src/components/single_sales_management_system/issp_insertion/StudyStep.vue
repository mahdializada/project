<template>
	<div class="w-full h-full d-flex align-center justify-center mt-5">
		<div class="w-full">
			<CTitle :text="`choose_a_${titlePage}`" />
			<div class="mb-3">
				<InputCard
					:title="
						path.length == 1
							? 'Find your product'
							: history.current.parent
							? history.current.parent.name
							: ''
					"
					:hasSearch="true"
					:hasPagination="true"
					:showBack="path.length > 1"
					:page="history[searchActive ? 'search' : 'current'].page"
					:pages="history[searchActive ? 'search' : 'current'].pages"
					:path="path"
					@back="back"
					@search="search"
					@disableSearch="disableSearch"
					@updatePage="
						(newpage) => {
							history[searchActive ? 'search' : 'current'].page = newpage;
						}
					"
					@changeTo="changeTo"
				>
					<ItemsContainer
						v-model="form.product_id.$model"
						:items="
							history[searchActive ? 'search' : 'current'].items.slice(
								history[searchActive ? 'search' : 'current'].page * 21 - 21,
								history[searchActive ? 'search' : 'current'].page * 21
							)
						"
						:loading="loading"
						@category_clicked="category_clicked"
						:no_data="
							history[searchActive ? 'search' : 'current'].no_data
								? true
								: false
						"
					></ItemsContainer>
				</InputCard>
			</div>
		</div>
	</div>
</template>

<script>
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
export default {
	props: {
		form: Object,
	},
	components: { ItemsContainer, InputCard, CTitle },
	data() {
		return {
			loading: false,
			searchActive: false,
			validateRule: GlobalRules.validate,
			titlePage: "category",
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
			path: ["categories"], //stack
		};
	},
	methods: {
		async loaded() {
			this.path = ["categories"];
			this.fetchRootItems();
		},
		async validate() {
			this.form.product_id.$touch();
			this.form.category_id.$touch();
			return !this.form.category_id.$invalid && !this.form.product_id.$invalid;
		},
		async fetchRootItems() {
			this.loading = true;
			try {
				let res = await this.$axios.get("/single-sales/categories-ssp", {
					params: {
						fetch_for_form: true,
					},
				});
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
		back() {
			this.loading = true;
			this.titlePage = "category";
			if (this.path.length > 1) {
				this.path.pop();
				if (this.path.length == 1) {
					if (this.history.parent) {
						this.history.current = this.history.parent;
					} else {
						this.fetchRootItems();
					}
				} else {
					if (this.history[this.path[this.path.length - 1].id]) {
						this.history.current =
							this.history[this.path[this.path.length - 1].id];
					} else {
						this.fetchChildItems(this.path[this.path.length - 1].id);
					}
				}
			}
			this.loading = false;
		},
		async search(search) {
			// search_for_from
			this.loading = true;
			if (search) {
				try {
					this.searchActive = true;
					let res = await this.$axios.get("/single-sales/categories-ssp", {
						params: {
							search_for_from: true,
							search,
							category_id: this.history.current.parent
								? this.history.current.parent.id
								: null,
						},
					});
					let data = [...res.data.products, ...res.data.categories];
					var obj = {};
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
		async category_clicked(item) {
			this.form.$model.category_id = item.id;
			await this.fetchChildItems(item);
			if (this.path[this.path.length - 1].id !== item.id) {
				this.path.push(item);
			}
		},
		async fetchChildItems(category) {
			this.titlePage = "product";
			this.loading = true;
			if (this.history[category.id]) {
				this.history.current = this.history[category.id];
			} else {
				let res1 = await this.$axios.get("/single-sales/categories-ssp", {
					params: {
						fetch_for_form: true,
						category_id: category.id,
					},
				});
				let res2 = await this.$axios.get("/single-sales/products-ssp", {
					params: {
						fetch_for_form: true,
						category_id: category.id,
					},
				});
				console.log(res2);
				let data = [...res1.data, ...res2.data];
				var obj = {};
				if (data.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: category,
						pages: Math.ceil(data.length / 21),
						page: 1,
					};
				} else {
					obj = {
						no_data: true,
						items: [],
						parent: category,
						pages: 0,
						page: 1,
					};
				}
				this.history[category.id] = obj;
				this.history.current = obj;
			}
			this.loading = false;
		},
		changeTo(item) {
			if (item == "parent") {
				this.path = this.path.slice(0, 1);
				if (this.history.parent) {
					this.history.current = this.history.parent;
				} else {
					this.fetchRootItems();
				}
			} else {
				let index = this.path.findIndex((item2) => item2?.id == item.id);
				this.path = this.path.slice(0, index + 1);
				if (this.history[this.path[this.path.length - 1].id]) {
					this.history.current =
						this.history[this.path[this.path.length - 1].id];
				} else {
					this.fetchChildItems(this.path[this.path.length - 1].id);
				}
			}
		},
		sortByName(itemA, itemB) {
			const nameA = itemA.name.toLowerCase();
			const nameB = itemB.name.toLowerCase();
			return nameA.localeCompare(nameB);
		},
	},
};
</script>
