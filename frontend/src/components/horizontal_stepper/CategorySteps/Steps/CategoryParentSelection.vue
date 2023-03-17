<template>
	<div class="w-full h-full d-flex align-center justify-center pt-5">
		<div class="w-full">
			<CTitle :text="'Choose a category'" class="mb-4" />
			<div class="mb-3">
				<InputCard
					:title="'Select Category'"
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
						v-model="form.selectedCategory.$model"
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
import GlobalRules from "~/helpers/vuelidate";
import ItemsContainer from "../../../new_form_components/cat_product_selection/ItemsContainer.vue";
import InputCard from "../../../new_form_components/components/InputCard.vue";
import CTitle from "../../../new_form_components/Inputs/CTitle.vue";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			// product selection
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
			path: ["categories"], //stack
			loading: false,
			searchActive: false,
		};
	},
	methods: {
		async loaded() {
			this.fetchRootItems();
		},
		async validate() {
			this.form.selectedCategory.$touch();
			return !this.form.selectedCategory.$invalid;
		},
		// product selection
		async category_clicked(item) {
			this.form.selectedCategory.$model = item.id;
			await this.fetchChildItems(item);
			if (this.path[this.path.length - 1].id !== item.id) {
				this.path.push(item);
			}
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
		async fetchChildItems(category) {
			this.loading = true;
			if (this.history[category.id]) {
				this.history.current = this.history[category.id];
			} else {
				let res1 = await this.$axios.get("/single-sales/categories", {
					params: {
						fetch_for_form: true,
						category_id: category.id,
					},
				});
				let data = res1.data;
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
		back() {
			this.loading = true;
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
		async search(search) {
			// search_for_from
			this.loading = true;
			var data = [];
			var obj = {
				no_data: true,
				items: [],
				parent: null,
				pages: 0,
				page: 1,
			};
			try {
				if (search) {
					this.searchActive = true;
					let res = await this.$axios.get("/single-sales/categories", {
						params: {
							search_for_from: true,
							only_category: true,
							search,
							category_id: this.history.current.parent
								? this.history.current.parent.id
								: null,
						},
					});
					data = res.data.categories;
				}
				if (data.length > 0) {
					obj = {
						items: data.sort(this.sortByName),
						parent: null,
						pages: Math.ceil(data.length / 21),
						page: 1,
					};
				}
				this.history.search = obj;
			} catch (err) {}
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
		// end
	},
	components: { ItemsContainer, InputCard, CTitle },
};
</script>
