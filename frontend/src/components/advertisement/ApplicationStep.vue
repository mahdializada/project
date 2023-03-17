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
					v-model="form.country_id.$model"
					:items="filterCountry"
					:loading="fetchingCountries"
					:no_data="filterCountry.length < 1 && !fetchingCountries"
					height="150px"
				></ItemsContainer>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard
				:title="this.$tr('platforms')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchPlaform = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.platform.$model"
					:item_text="'platform_name'"
					:items="filterPlatform"
					:loading="isFetchingPlatforms"
					:no_data="filterPlatform.length < 1 && !isFetchingPlatforms"
					height="150px"
					:hasLogo="true"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
		<div class="d-flex flex-column flex-md-row">
			<div class="w-full">
				<CTextField
					:title="$tr('label_required', $tr('name'))"
					placeholder="name"
					type="textfield"
					class="me-md-2"
					rounded
					:prepend-inner-icon="'mdi-monitor'"
					:rules="validateRule(form.name, $root, $tr('name'))"
					:invalid="form.name.$invalid"
					v-model="form.name.$model"
				/>
			</div>

			<div class="w-full" v-if="selectedPlatform">
				<CTextField
					v-if="selectedPlatform.platform_name === 'google ads'"
					:title="$tr('label_required', $tr('developer_token'))"
					placeholder="google_ads_developer_token"
					type="textfield"
					class="me-md-2"
					rounded
					:rules="
						validateRule(form.developer_token, $root, $tr('developer_token'))
					"
					:invalid="form.developer_token.$invalid"
					v-model="form.developer_token.$model"
				/>
				<CTextField
					v-else-if="selectedPlatform.platform_name === 'tiktok'"
					:title="$tr('label_required', $tr('rid'))"
					:placeholder="$tr('rid')"
					type="textfield"
					class="me-md-2"
					rounded
					:rules="validateRule(form.rid, $root, $tr('rid'))"
					:invalid="form.rid.$invalid"
					v-model="form.rid.$model"
				/>
				<CTextField
					v-else-if="selectedPlatform.platform_name === 'snapchat'"
					:title="$tr('label_required', $tr('organization_id'))"
					placeholder="Snapchat Organization ID"
					type="textfield"
					class="me-md-2"
					rounded
					:rules="
						validateRule(form.organization_id, $root, $tr('organization_id'))
					"
					:invalid="form.organization_id.$invalid"
					v-model="form.organization_id.$model"
				/>
			</div>
		</div>
		<div class="d-flex flex-column flex-md-row">
			<div class="w-full">
				<CTextField
					:title="$tr('label_required', $tr('client_id'))"
					placeholder="client_id"
					type="text"
					class="me-md-2"
					rounded
					:prepend-inner-icon="'mdi-shield-account'"
					:rules="validateRule(form.client_id, $root, $tr('client_id'))"
					:invalid="form.client_id.$invalid"
					v-model="form.client_id.$model"
				/>
			</div>
			<div class="w-full">
				<CTextField
					:title="$tr('label_required', $tr('client_secret'))"
					placeholder="0000000000"
					prepend-inner-icon="mdi-key "
					:rules="validateRule(form.client_secret, $root, $tr('client_secret'))"
					:hasCustomBtn="true"
					btnIcon="mdi-content-paste "
					:invalid="form.client_secret.$invalid"
					v-model="form.client_secret.$model"
				/>
			</div>
		</div>

		<div class="mb-3">
			<InputCard
				:title="this.$tr('users')"
				:hasSearch="true"
				:hasPagination="false"
				@search="(v) => (searchUser = v)"
			>
				<NoCheckboxItemContainer
					v-model="form.users.$model"
					:items="filterUser"
					:loading="isFetchingUsers"
					:no_data="filterUser.length < 1 && !isFetchingUsers"
					height="150px"
					:item_text="'firstname'"
					:hasImg="true"
					:multi="true"
				></NoCheckboxItemContainer>
			</InputCard>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import NoCheckboxItemContainer from "../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import ItemsContainer from "../new_form_components/cat_product_selection/ItemsContainer.vue";

export default {
	props: {
		form: Object,
		fetchCountry: Boolean,
		isEdit: false,
	},

	watch: {
		"form.platform.$model": function (platform_id) {
			this.selectedPlatform = this.platforms.find(
				(item) => item.id == platform_id
			);
			this.form.platform_name.$model = this.selectedPlatform?.platform_name;
		},
	},

	data() {
		return {
			searchPlaform: "",
			searchUser: "",
			isFetchingPlatforms: false,

			validateRule: GlobalRules.validate,
			isFetchingUsers: false,

			platforms: [],
			fetchedUsers: [],
			selectedPlatform: "",
			countries: [],
			fetchingCountries: false,
			searchCountry: "",
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

		filterUser() {
			if (this.searchUser) {
				const filter = (item) =>
					item?.firstname
						?.toLowerCase()
						?.includes(this.searchUser?.toLowerCase());

				return this.fetchedUsers.filter(filter);
			}
			return this.fetchedUsers;
		},
		filterCountry: function () {
			if (this.searchCountry) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchCountry?.toLowerCase());
				return this.countries.filter(filter);
			}
			console.log("countries", this.countries);
			return this.countries;
		},
	},

	methods: {
		async loaded() {
			this.platforms = [];
			this.fetchedUsers = [];
			this.countries = [];
			await this.fetchCountries();
			await this.fetchPlatformNames();
			await this.fetchUsers();
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

		validate() {
			this.form.platform.$touch();
			this.form.name.$touch();
			this.form.client_id.$touch();
			this.form.client_secret.$touch();
			this.form.organization_id.$touch();
			this.form.rid.$touch();
			this.form.developer_token.$touch();
			this.form.users.$touch();
			let isValid =
				!this.form.platform.$invalid &&
				!this.form.name.$invalid &&
				!this.form.client_id.$invalid &&
				!this.form.client_secret.$invalid &&
				!this.form.organization_id.$invalid &&
				!this.form.rid.$invalid &&
				!this.form.developer_token.$invalid &&
				!this.form.users.$invalid;
			return isValid;
		},

		async fetchPlatformNames() {
			try {
				this.isFetchingPlatforms = true;
				const url = "advertisement/platforms?only_platforms=true";
				const { data } = await this.$axios.get(url);
				this.platforms = data.platforms;
			} catch (error) {}
			this.isFetchingPlatforms = false;
		},

		async fetchUsers() {
			try {
				this.isFetchingUsers = true;
				const { data } = await this.$axios.get(
					"/advertisement/applications/users"
				);
				this.fetchedUsers = data.users;
			} catch (error) {}
			this.isFetchingUsers = false;
		},

		removeUser(id) {
			this.application.users = this.application.users.filter(
				(user) => user.id !== id
			);
		},
	},
	components: {
		CTitle,
		InputCard,
		NoCheckboxItemContainer,
		CTextField,
		ItemsContainer,
	},
};
</script>

<style scoped lang="scss">
.sales__type__container {
	display: flex;
	justify-content: space-between;
}
</style>
