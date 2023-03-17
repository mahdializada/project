<template>
	<div class="connection__container">
		<CTitle
			:text="
				isEdit
					? $tr('update_item', $tr('project'))
					: $tr('create_item', $tr('project'))
			"
		/>
		<div class="mb-3">
			<InputCard
				:title="this.$tr('country')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchCountry = v)"
			>
				<ItemsContainer
					v-model="form.country_ids.$model"
					:items="filterCountry"
					:loading="fetchingCountries"
					:no_data="filterCountry.length < 1 && !fetchingCountries"
					height="150px"
					:multi="true"
				></ItemsContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard
				:title="this.$tr('company')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchCompany = v)"
			>
				<HorizontalItemContainer
					v-model="form.company_ids.$model"
					:items="filterCompany"
					:loading="isFetchingcompanies"
					:no_data="filterCompany.length < 1 && !isFetchingcompanies"
					height="150px"
					:multi="true"
				></HorizontalItemContainer>
			</InputCard>
		</div>
		<div class="d-flex flex-column flex-md-row">
			<div class="w-full">
				<CTextField
					:title="$tr('label_required', $tr('project_name'))"
					placeholder="project_name"
					type="textfield"
					class="me-md-2"
					rounded
					v-model="form.name.$model"
					:invalid="form.name.$invalid"
					:rules="validateRule(form.name, $root, $tr('name'))"
				/>
			</div>
			<div class="w-full">
				<CTextField
					v-model="form.domain.$model"
					:title="$tr('label_required', $tr('domain'))"
					placeholder="http:://www.website.com"
					prepend-inner-icon="mdi-link-variant "
					:rules="validateRule(form.domain, $root, $tr('domain'))"
					:hasCustomBtn="true"
					btnIcon="mdi-content-paste "
					:invalid="form.domain.$invalid"
					@add="copyToAdId"
				/>
			</div>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import ItemsContainer from "../new_form_components/cat_product_selection/ItemsContainer.vue";
import HorizontalItemContainer from "../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import NoCheckboxItemContainer from "../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import SelectItem from "../new_form_components/components/SelectItem.vue";
import CSelect from "../new_form_components/Inputs/CSelect.vue";
import CAutoComplete from "../new_form_components/Inputs/CAutoComplete.vue";
import CustomInput from "../design/components/CustomInput.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";

export default {
	props: {
		form: Object,
		fetchCountry: Boolean,
		isEdit: false,
	},

	watch: {
		"form.country_ids.$model": function (countryId) {
			if (!this.isEdit) {
				this.form.company_ids.$model = [];
			}
			if (countryId) {
				this.companies = [];
				this.fetchCompanies({ country_ids: this.form.country_ids.$model });
			}
		},
	},

	data() {
		return {
			searchCountry: "",
			searchCompany: "",
			searchProject: "",
			fetchingCountries: false,

			validateRule: GlobalRules.validate,
			isFetchingcompanies: false,
			isFetchingprojects: false,
			isFetchingproducts: false,
			isFetchingispps: false,

			countries: [],
			companies: [],
		};
	},

	computed: {
		filterCountry: function () {
			if (this.searchCountry) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchCountry?.toLowerCase());
				return this.countries.filter(filter);
			}
			return this.countries;
		},
		filterCompany() {
			if (this.searchCompany) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchCompany?.toLowerCase());
				return this.companies.filter(filter);
			}
			return this.companies;
		},
	},

	methods: {
		async loaded() {
			this.countries = [];
			this.companies = [];
			await this.fetchCountries();
		},

		validate() {
			this.form.name.$touch();
			this.form.domain.$touch();
			let isValid =
				!this.form.country_ids.$invalid &&
				!this.form.company_ids.$invalid &&
				!this.form.name.$invalid &&
				!this.form.domain.$invalid;
			return isValid;
		},

		async fetchCountries() {
			try {
				this.countries = [];
				this.fetchingCountries = true;
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				this.countries = data.items;
			} catch (error) {}
			this.fetchingCountries = false;
		},

		async fetchItems({ type, id }) {
			try {
				this["isFetching" + type] = true;
				const url = `advertisement/connection/generate_link/${type}/${id}`;
				const { data } = await this.$axios.get(url);
				this[data.type] = data.items;
			} catch (error) {}
			this["isFetching" + type] = false;
		},

		async fetchCompanies(param) {
			try {
				this.isFetchingCompany = true;
				const url = "common/companies";
				const { data } = await this.$axios.get(url, { params: param });
				this.companies = data.companies;
			} catch (error) {}
			this.isFetchingCompany = false;
		},

		async copyToAdId() {
			try {
				const clipboard = navigator.clipboard;
				const account_po = await clipboard.readText();
				if (account_po) {
					this.form.domain.$model = account_po;
				}
			} catch (error) {}
		},
	},
	components: {
		CTitle,
		InputCard,
		ItemsContainer,
		HorizontalItemContainer,
		NoCheckboxItemContainer,
		SelectItem,
		CSelect,
		CAutoComplete,
		CustomInput,
		CTextField,
	},
};
</script>

<style scoped lang="scss">
.sales__type__container {
	display: flex;
	justify-content: space-between;
}
</style>
