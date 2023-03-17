<template>
	<div class="w-full h-full d-flex align-center-justify-center">
		<div class="w-full h-full pt-5">
			<div class="mb-3">
				<InputCard
					:title="'Teams'"
					:hasSearch="true"
					:sub_title="'You can select one or multiple teams.'"
					:hasPagination="true"
					:page="history[searchActive ? 'search' : 'current'].page"
					:pages="history[searchActive ? 'search' : 'current'].pages"
					@search="search"
					@disableSearch="searchActive = false"
					@updatePage="
						(newPage) => {
							history[searchActive ? 'search' : 'current'].page = newPage;
						}
					"
				>
					<ItemsContainer
						v-model="form.selectedTeams.$model"
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
						:multi="true"
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
	props: {
		form: Object,
	},
	data() {
		return {
			selectedItems: [],
			loading: false,
			searchActive: false,
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
		};
	},
	methods: {
		async loaded() {
			this.fetchTeamItems();
		},
		async validate() {
			return true;
		},
		async fetchTeamItems() {
			this.loading = true;
			try {
				let res = await this.$axios.get("teams?key=teams-only");
				let obj = {};
				let data = [];
				for (let i = 0; i < res?.data?.data?.length; i++) {
					const el = res?.data?.data[i];
					data.push({
						...el,
						type: "team",
					});
				}
				if (data?.length > 0) {
					obj = {
						items: data,
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
			this.history.search = obj;
			this.loading = false;
		},
	},
	components: { InputCard, ItemsContainer },
};
</script>
