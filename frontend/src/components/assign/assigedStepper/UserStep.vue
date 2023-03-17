<template>
	<div class="w-full h-full d-flex align-center-justify-center">
		<div class="w-full h-full pt-5">
			<div class="mb-3">
				<InputCard
					:title="'Teams'"
					:hasSearch="true"
					:sub_title="'You can select one or multiple teams.'"
					:hasPagination="true"
					:hasAllSwitch="true"
					:page="history[searchActive ? 'search' : 'current'].page"
					:pages="history[searchActive ? 'search' : 'current'].pages"
					@search="search"
					@disableSearch="searchActive = false"
					:rules="
						validateRule(
							form.user, // validate objec
							$root, // context
							$tr('user') // lable for feedback
						)
					"
					@updatePage="
						(newPage) => {
							history[searchActive ? 'search' : 'current'].page = newPage;
						}
					"
					@allSwitchChange="allSwitchChange"
				>
					<ItemsContainer
						v-model="form.user.$model"
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
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
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
			this.fetchUsers();
		},
		async validate() {
			this.form.user.$touch();
			return !this.form.user.$invalid;
		},
		async fetchUsers() {
			this.loading = true;
			try {
				let res = await this.$axios.get("users", {
					params: {
						not_assigned_users: true,
						team_ids: this.form.team.$model,
					},
				});
				let obj = {};
				let data = [];
				for (let i = 0; i < res?.data?.users?.length; i++) {
					const el = res?.data?.users[i];
					data.push({
						...el,
						type: "user",
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
						if (
							el.firstname.toLowerCase().indexOf(search.toLowerCase()) > -1 ||
							el.lastname.toLowerCase().indexOf(search.toLowerCase()) > -1 ||
							(
								el.firstname.toLowerCase() +
								" " +
								el.lastname.toLowerCase()
							).indexOf(search.toLowerCase()) > -1
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
		allSwitchChange(value) {
			this.form.user.$model = [];
			if (value == true) {
				for (let i = 0; i < this.history.current.items.length; i++) {
					const el = this.history.current.items[i];
					this.form.user.$model.push(el.id);
				}
			}
		},
	},
	components: { InputCard, ItemsContainer },
};
</script>
