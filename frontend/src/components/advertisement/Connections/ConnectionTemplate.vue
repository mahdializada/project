<template>
	<div class="connection__container">
		<div class="mb-3 d-flex">
			<InputCard
				:title="this.$tr('choose_a_template')"
				:hasSearch="true"
				@search="search"
				@disableSearch="searchActive = false"
				@updatePage="
					(newpage) => {
						page = newpage;
					}
				"
				@filterDone="filterDone"
				:hasFilter="true"
				:filterItems="
					filterItems.map((item) => {
						item.title = $tr(item.title);
						return item;
					})
				"
				:hasPagination="true"
				:page="page"
				:pages="Math.ceil(filteredTemplates.length / 14)"
			>
				<NoCheckboxItemContainer
					v-model="form.template_id.$model"
					:itemsss="filteredTemplates"
					:items="
						searchActive
							? searchItems.slice(page * 14 - 14, page * 14)
							: filteredTemplates.slice(page * 14 - 14, page * 14)
					"
					:loading="loading"
					:no_data="false"
					height="260px"
					nameLogo="name"
					hasFavorite
					@toggleFavorite="toggleFavorite"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="mb-3 d-flex">
			<InputCard
				:title="this.$tr('favorites')"
				:hasSearch="true"
				@search="search2"
				@disableSearch="searchActive2 = false"
				@updatePage="
					(newpage) => {
						page2 = newpage;
					}
				"
				@filterDone="filterDone2"
				:hasFilter="true"
				:filterItems="
					filterItems2.map((item) => {
						item.title = $tr(item.title);
						return item;
					})
				"
				:hasPagination="true"
				:page="page2"
				:pages="Math.ceil(favoriteFilteredTemplates.length / 14)"
			>
				<NoCheckboxItemContainer
					v-model="form.template_id.$model"
					:items="
						searchActive2
							? searchItems2.slice(page2 * 14 - 14, page2 * 14)
							: favoriteFilteredTemplates.slice(page2 * 14 - 14, page2 * 14)
					"
					:loading="loading"
					:no_data="false"
					height="260px"
					nameLogo="name"
					hasFavorite
					@toggleFavorite="toggleFavorite"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import moment from "moment-timezone";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			filterItems: [
				{
					id: "country",
					title: "country",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "company",
					title: "company",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "platform",
					title: "platform",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "platform_name",
					itemValue: "id",
				},
				{
					id: "organization",
					title: "organization",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "pcode",
					title: "pcode",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "id",
					itemValue: "id",
				},
				{
					id: "date",
					title: "date",
					icon: "mdi-earth",
					selected: {
						start_date: "",
						end_date: "",
					},
					items: [],
					menu: false,
					filterDone: this.filterDone,
					itemText: "id",
					itemValue: "id",
				},
			],
			filterItems2: [
				{
					id: "country",
					title: "country",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "company",
					title: "company",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "platform",
					title: "platform",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "platform_name",
					itemValue: "id",
				},
				{
					id: "organization",
					title: "organization",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "name",
					itemValue: "id",
				},
				{
					id: "pcode",
					title: "pcode",
					icon: "mdi-earth",
					selected: null,
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "id",
					itemValue: "id",
				},
				{
					id: "date",
					title: "date",
					icon: "mdi-earth",
					selected: {
						start_date: "",
						end_date: "",
					},
					items: [],
					menu: false,
					filterDone: this.filterDone2,
					itemText: "id",
					itemValue: "id",
				},
			],
			loading: false,
			allTemplates: [],
			filteredTemplates: [],
			allFavoriteTemplates: [],
			favoriteFilteredTemplates: [],
			page: 1,
			page2: 1,
			searchText: "",
			searchText2: "",
			searchActive: false,
			searchActive2: false,
			searchItems: [],
			searchItems2: [],
			template: null,
		};
	},
	watch: {
		["form.template_id.$model"](value) {
			this.form.shouldReset.$model = false;
			this.template = this.allTemplates.find((item) => item.id == value);
			this.form.country_id.$model = this.template?.connection.country_id;
			this.form.company_id.$model = this.template?.connection.company_id;
			this.form.project_id.$model = this.template?.connection.project_id;
			this.form.sales_type.$model = this.template?.connection.sales_type;
			this.form.pcode.$model = this.template?.connection.pcode;
			this.form.ispp_id.$model = this.template?.connection.ispp_id;
			this.form.platform_id.$model = this.template?.connection.platform_id;
			this.form.application_id.$model =
				this.template?.connection.application_id;
			this.form.ad_account_id.$model =
				this.template?.connection.server_account_id;
			this.form.landing_page_link.$model =
				this.template?.connection.landing_page_link;
		},
	},
	methods: {
		async loaded() {
			this.fetchTemplates();
			this.filterItems.forEach((el) => {
				if (el.items.length == 0 && el.id !== "date") {
					this.fetchData(el.id.toLowerCase());
				}
			});
		},
		filterDone() {
			let data = {
				country_id: this.filterItems[0].selected,
				company_id: this.filterItems[1].selected,
				platform_id: this.filterItems[2].selected,
				application_id: this.filterItems[3].selected,
				pcode: this.filterItems[4].selected,
			};
			let start_date = this.filterItems[5].selected.start_date;
			let end_date = this.filterItems[5].selected.end_date;
			this.filteredTemplates = this.allTemplates.filter((item) => {
				let condition = true;
				for (let i = 0; i < Object.keys(data).length; i++) {
					let arr_item = Object.keys(data)[i];
					if (data[arr_item]) {
						condition =
							condition && item?.connection[arr_item] == data[arr_item];
					}
				}
				if (start_date) {
					condition =
						condition && moment(start_date).isBefore(moment(item.created_at));
				}
				if (end_date) {
					condition =
						condition && moment(end_date).isAfter(moment(item.created_at));
				}
				return condition;
			});
			this.search(this.searchText);
		},
		filterDone2() {
			let data = {
				country_id: this.filterItems2[0].selected,
				company_id: this.filterItems2[1].selected,
				platform_id: this.filterItems2[2].selected,
				application_id: this.filterItems2[3].selected,
				pcode: this.filterItems2[4].selected,
			};
			let start_date = this.filterItems2[5].selected.start_date;
			let end_date = this.filterItems2[5].selected.end_date;
			this.favoriteFilteredTemplates = this.allFavoriteTemplates.filter(
				(item) => {
					let condition = true;
					for (let i = 0; i < Object.keys(data).length; i++) {
						let arr_item = Object.keys(data)[i];
						if (data[arr_item]) {
							condition =
								condition && item?.connection[arr_item] == data[arr_item];
						}
					}
					if (start_date) {
						condition =
							condition && moment(start_date).isBefore(moment(item.created_at));
					}
					if (end_date) {
						condition =
							condition && moment(end_date).isAfter(moment(item.created_at));
					}
					return condition;
				}
			);
			this.search2(this.searchText);
		},
		validate() {
			this.form.template_id.$touch();
			return !this.form.template_id.$invalid;
		},
		async fetchTemplates() {
			try {
				this.loading = true;
				let res = await this.$axios.get(`/advertisement/templates`);
				this.allTemplates = res?.data?.data;
				this.filteredTemplates = JSON.parse(JSON.stringify(this.allTemplates));
				this.allFavoriteTemplates = this.allTemplates.filter(
					(item) => item.favorite
				);
				this.favoriteFilteredTemplates = JSON.parse(
					JSON.stringify(this.allFavoriteTemplates)
				);
				this.loading = false;
			} catch (e) {}
		},
		async fetchData(type) {
			try {
				let res = await this.$axios.get(`/advertisement/templates/${type}`);
				this.filterItems = this.filterItems.map((item) => {
					if (item.id.toLowerCase() == type) {
						item.items = res?.data?.data;
					}
					return item;
				});
				this.filterItems2 = this.filterItems2.map((item) => {
					if (item.id.toLowerCase() == type) {
						item.items = res?.data?.data;
					}
					return item;
				});
			} catch (e) {}
		},
		search(search) {
			this.searchText = search;
			if (this.searchText ? this.searchText.trim() != "" : false) {
				this.loading = true;
				this.searchActive = true;
				this.searchItems = this.filteredTemplates.filter(
					(item) => item.name.toLowerCase().indexOf(this.searchText) >= 0
				);
				this.loading = false;
			}
		},
		search2(search) {
			this.searchText2 = search;
			if (this.searchText ? this.searchText.trim() != "" : false) {
				this.loading = true;
				this.searchActive2 = true;
				this.searchItems2 = this.favoriteFilteredTemplates.filter(
					(item) => item.name.toLowerCase().indexOf(this.searchText) >= 0
				);
				this.loading = false;
			}
		},
		async toggleFavorite(item) {
			try {
				this.toggleItemsFavorite(item);
				let res = await this.setFavorite(item.id, item.favorite);
				if (!res) {
					throw "exception";
				}
			} catch (e) {
				this.toggleItemsFavorite(item);
			}
		},
		toggleItemsFavorite(item) {
			this.allTemplates = this.allTemplates.map((item2) => {
				if (item.id == item2.id) {
					item2.favorite = !item2.favorite;
				}
				return item2;
			});
			this.filteredTemplates = this.filteredTemplates.map((item2) => {
				if (item.id == item2.id) {
					item2.favorite = !item2.favorite;
				}
				return item2;
			});
			this.favoriteFilteredTemplates = this.allTemplates.filter(
				(item2) => item2.favorite
			);
		},
		async setFavorite(id, favorite) {
			let res = await this.$axios.post("/advertisement/templates", {
				id,
				favorite,
			});
			return res?.data.result;
		},
	},
	components: {
		CTitle,
		InputCard,
		NoCheckboxItemContainer,
	},
};
</script>
