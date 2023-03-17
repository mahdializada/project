<template>
	<div class="w-full h-full d-flex align-center-justify-center">
		<div class="w-full h-full">
			<div class="mb-3">
				<InputCard
					:title="'Study'"
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
								history[searchActive ? 'search' : 'current'].page * 21 - 21,
								history[searchActive ? 'search' : 'current'].page * 21
							)
						"
						:loading="loading"
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
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
export default {
	components: {
		InputCard,
		ItemsContainer,
	},
	data() {
		return {
			searchActive: false,
			loading: false,
			selectedItems: null,
			history: {
				current: {
					items: [
						{
							id: 1,
							name: "Study 1",
							type: "study",
						},
						{
							id: 2,
							name: "Study 2",
							type: "study",
						},
						{
							id: 3,
							name: "Study 3",
							type: "study",
						},
						{
							id: 4,
							name: "Study 4",
							type: "study",
						},
					],
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
		};
	},
	methods: {
		async loaded() {
		},
		async validate() {
			return true;
		},
		async fetchStudyItems() {
			this.loading = true;
			try {
				let res = await this.$axios.get("path", {
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
		async search(search) {
			// search_for_from
			this.loading = true;
			if (search) {
				this.searchActive = true;
				try {
					let res = await this.$axios.get("/path", {
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
	},
};
</script>
