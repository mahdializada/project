<template>
	<div class="connection__container">
		<CTitle text="connection_information" />
		<div class="mb-3">
			<InputCard
				:title="this.$tr('platforms')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchPlaform = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.platform_id.$model"
					:items="filterPlatform"
					:loading="isFetchingplatforms"
					:no_data="filterPlatform.length < 1 && !isFetchingplatforms"
					height="150px"
					:hasLogo="true"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard
				:title="this.$tr('application_or_organization')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchApplication = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.application_id.$model"
					:items="filterApplication"
					:loading="isFetchingapplications"
					:no_data="filterApplication.length < 1 && !isFetchingapplications"
					height="150px"
					:hasLogo="false"
					nameLogo="name"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard
				:title="this.$tr('ad_accounts')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchAdAccount = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.ad_account_id.$model"
					:items="filterAdAccount"
					:loading="isFetchingadAccounts"
					:no_data="filterAdAccount.length < 1 && !isFetchingadAccounts"
					height="150px"
					:hasLogo="false"
					nameLogo="name"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<CTextField
				v-model="form.pixel_id.$model"
				:title="$tr('label_required', $tr('pixel_id'))"
				:placeholder="$tr('pixel_id')"
				prepend-inner-icon="mdi-link-variant "
				:rules="validateRule(form.pixel_id, $root, $tr('pixel_id'))"
				:hasCustomBtn="true"
				btnIcon="mdi-content-paste "
				:invalid="form.pixel_id.$invalid"
				@add="copyPixelFromClipboard"
			/>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";

export default {
	props: {
		form: Object,
	},

	watch: {
		"form.company_id.$model": function (company_id) {
			if (company_id) {
				this.platforms = [];
				this.applications = [];
				this.adAccounts = [];
				this.fetchItems({ type: "platforms", id: company_id });
			}
		},
		"form.platform_id.$model": function (platform_id) {
			if (this.form.shouldReset.$model) {
				this.form.application_id.$model = null;
			}
			if (platform_id) {
				this.applications = [];
				this.adAccounts = [];
				this.fetchItems({ type: "applications", id: platform_id });
			}
		},
		"form.application_id.$model": function (application_id) {
			if (this.form.shouldReset.$model) {
				this.form.ad_account_id.$model = null;
			}
			if (application_id) {
				this.adAccounts = [];
				this.fetchItems({ type: "adAccounts", id: application_id });
			}
		},
	},

	data() {
		return {
			searchPlaform: "",
			searchApplication: "",
			searchAdAccount: "",

			validateRule: GlobalRules.validate,
			isFetchingplatforms: false,
			isFetchingapplications: false,
			isFetchingadAccounts: false,

			platforms: [],
			applications: [],
			adAccounts: [],
		};
	},

	computed: {
		filterPlatform() {
			if (this.searchPlaform) {
				const filter = (item) =>
					item?.platform_name
						?.toLowerCase()
						?.includes(this.searchPlaform?.toLowerCase());
				return this.platforms.filter(filter);
			}

			return this.platforms;
		},
		filterApplication() {
			if (this.searchApplication) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchApplication?.toLowerCase());
				return this.applications.filter(filter);
			}
			return this.applications;
		},
		filterAdAccount() {
			if (this.searchAdAccount) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchAdAccount?.toLowerCase());
				return this.adAccounts.filter(filter);
			}
			return this.adAccounts;
		},
	},

	methods: {
		validate() {
			let isValid =
				!this.form.platform_id.$invalid &&
				!this.form.application_id.$invalid &&
				!this.form.ad_account_id.$invalid;
			return isValid;
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
		async copyPixelFromClipboard() {
			try {
				const clipboard = navigator.clipboard;
				const pixel_id = await clipboard.readText();
				this.form.pixel_id.$model = pixel_id;
			} catch (error) {}
		},
		async loaded() {
		},
	},
	components: {
		CTitle,
		InputCard,
		NoCheckboxItemContainer,
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
