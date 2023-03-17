<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<v-card class="main-Card px-3">
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px">
				<h2 class="text-h5 font-weight-bold">
					{{ $tr("create_item", $tr("link")) }}
				</h2>
				<CloseBtn @click="toggle" type="button" />
			</v-card-title>
			<v-card-text
				class="position-relative card-pos"
				style="height: 650px; overflow-y: auto">
				<v-form ref="insertForm" lazy-validation>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.country_id.$touch()"
								v-model="$v.connection.country_id.$model"
								:rules="validate($v.connection.country_id, $root, 'country')"
								:items="countries"
								item-value="id"
								item-text="name"
								:label="$tr('label_required', $tr('country'))"
								:placeholder="$tr('choose_item', $tr('country'))"
								country
								type="autocomplete"
								class="me-md-2"
								:loading="isFetchingcountries" />
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.company_id.$touch()"
								v-model="$v.connection.company_id.$model"
								:rules="validate($v.connection.company_id, $root, 'company')"
								:label="$tr('label_required', $tr('company'))"
								:items="companies"
								item-value="id"
								item-text="name"
								has-logo
								logoName=""
								:placeholder="$tr('choose_item', $tr('company'))"
								type="autocomplete"
								:loading="isFetchingcompanies"
								class="ms-md-2" />
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.sales_type.$touch()"
								v-model="$v.connection.sales_type.$model"
								:rules="validate($v.connection.sales_type, $root, 'sales_type')"
								:items="salesType"
								item-value="id"
								item-text="text"
								:label="$tr('label_required', $tr('sales_type'))"
								:placeholder="$tr('choose_item', $tr('sales_type'))"
								type="autocomplete"
								class="me-md-2" />
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.ispp_id.$touch()"
								v-model="$v.connection.ispp_id.$model"
								:rules="validate($v.connection.ispp_id, $root, 'ispp')"
								:items="ispps"
								item-value="id"
								:item-text="(item) => `${item.code} (${item.product_name})`"
								:label="$tr('label_required', $tr('ispp'))"
								:placeholder="$tr('choose_item', $tr('ispp'))"
								type="autocomplete"
								class="ms-md-2"
								:loading="isFetchingispps" />
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.project_id.$touch()"
								v-model="$v.connection.project_id.$model"
								:rules="validate($v.connection.project_id, $root, 'project')"
								:label="$tr('label_required', $tr('project'))"
								:items="projects"
								item-value="id"
								item-text="name"
								has-logo
								:placeholder="$tr('choose_item', $tr('project'))"
								type="autocomplete"
								:loading="isFetchingprojects"
								class="me-md-2" />
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.platform_id.$touch()"
								v-model="$v.connection.platform_id.$model"
								:rules="validate($v.connection.platform_id, $root, 'platform')"
								:label="$tr('label_required', $tr('platform'))"
								:items="platforms"
								item-value="id"
								item-text="platform_name"
								:placeholder="$tr('choose_item', $tr('platform'))"
								type="autocomplete"
								:loading="isFetchingplatforms"
								class="ms-md-2"
								:platform="true" />
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.application_id.$touch()"
								v-model="$v.connection.application_id.$model"
								:rules="
									validate(
										$v.connection.application_id,
										$root,
										'application_or_organization',
									)
								"
								:label="
									$tr('label_required', $tr('application_or_organization'))
								"
								:items="applications"
								item-value="id"
								item-text="name"
								has-logo
								:placeholder="
									$tr('choose_item', $tr('application_or_organization'))
								"
								type="autocomplete"
								:loading="isFetchingapplications"
								class="me-md-2" />
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.connection.ad_account_id.$touch()"
								v-model="$v.connection.ad_account_id.$model"
								:rules="
									validate($v.connection.ad_account_id, $root, 'ad_account')
								"
								:label="$tr('label_required', $tr('ad_account'))"
								:items="adAccounts"
								item-value="id"
								item-text="name"
								has-logo
								:placeholder="$tr('choose_item', $tr('ad_account'))"
								type="autocomplete"
								:loading="isFetchingadAccounts"
								class="ms-md-2" />
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_code'))"
								placeholder="product_code"
								v-model="$v.connection.pcode.$model"
								:rules="validate($v.connection.pcode, $root, 'product_code')"
								type="textfield"
								class="me-md-2" />
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('landing_page_link'))"
								placeholder="landing_page_link"
								v-model="$v.connection.landing_page_link.$model"
								:rules="
									validate(
										$v.connection.landing_page_link,
										$root,
										'landing_page_link',
									)
								"
								type="textfield"
								class="ms-md-2" />
						</div>
					</div>

					<div v-if="generated_link">
						<div class="w-full">
							<CustomInput
								:label="$tr('generated_link')"
								placeholder="generated_link"
								type="textfield"
								:value="generated_link"
								readonly
								@dblclick="copyGeneratedLink"
								@focus="copyGeneratedLink" />
							<v-btn
								@click="copyGeneratedLink"
								color="secondary"
								class="stepper-btn mr-2"
								type="button">
								{{ $tr("copy_link") }}
							</v-btn>
						</div>
						<div class="d-flex flex-column flex-md-row mt-2">
							<div class="w-full">
								<CustomInput
									:label="$tr('label_required', $tr('ad_id'))"
									placeholder="ad_id"
									v-model="$v.connectionMedia.ad_id.$model"
									:rules="validate($v.connectionMedia.ad_id, $root, 'ad_id')"
									type="textfield"
									class="me-md-2" />
							</div>
							<div class="w-full">
								<CustomInput
									:label="$tr('label_required', $tr('media_link'))"
									placeholder="media_link"
									v-model="$v.connectionMedia.media_link.$model"
									:rules="
										validate($v.connectionMedia.media_link, $root, 'media_link')
									"
									type="textfield"
									class="ms-md-2" />
							</div>
						</div>
					</div>
				</v-form>
			</v-card-text>
			<v-card-actions class="pa-3">
				<v-btn
					@click="resetForm"
					color="success"
					class="stepper-btn mr-2"
					type="button">
					{{ $tr("reset_form") }}
				</v-btn>
				<v-btn
					@click="resetLink"
					color="secondary"
					class="stepper-btn mr-2"
					type="button">
					{{ $tr("new_link") }}
				</v-btn>
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					class="stepper-btn px-3"
					style="min-width: 160px"
					@click="validateForm"
					:loading="isSubmitting"
					:disable="isSubmitting">
					<template v-slot:loader>
						<span>
							<span>
								{{
									generated_link ? $tr("submitting") : $tr("generating") + "..."
								}}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2" />
						</span>
					</template>
					{{ generated_link ? $tr("submit") : $tr("generate") }}
				</v-btn>

				<v-btn
					@click="toggle"
					color="error"
					class="stepper-btn"
					:type="'button'">
					{{ $tr("cancel") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Validations from "../../validations/validations";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import HandleException from "../../helpers/handle-exception";
import FormBtn from "../design/components/FormBtn.vue";
import SelectedItem from "../design/components/SelectedItem.vue";

export default {
	data() {
		return {
			model: false,
			validate: GlobalRules.validate,
			generated_link: null,
			isSubmitting: false,
			isFetchingcompanies: false,
			isFetchingcountries: false,
			isFetchingplatforms: false,
			isFetchingapplications: false,
			isFetchingprojects: false,
			isFetchingispps: false,
			isFetchingadAccounts: false,

			companies: [],
			countries: [],
			platforms: [],
			applications: [],
			projects: [],
			ispps: [],
			adAccounts: [],
			salesType: [
				{ id: "Single Sale", text: this.$tr("single_sales") },
				{ id: "Shopping Cart", text: this.$tr("shopping_cart") },
			],

			connectionMedia: {
				connection_id: null,
				media_link: null,
				ad_id: null,
			},
			connection: {
				pcode: null,
				sales_type: null,
				landing_page_link: null,
				platform_id: null,
				application_id: null,
				ad_account_id: null,
				project_id: null,
				company_id: null,
				country_id: null,
				ispp_id: null,
			},
		};
	},

	validations: {
		connection: {
			pcode: Validations.requiredValidation,
			sales_type: Validations.requiredValidation,
			landing_page_link: Validations.urlValidationRequired,
			platform_id: Validations.requiredValidation,
			application_id: Validations.requiredValidation,
			ad_account_id: Validations.requiredValidation,
			project_id: Validations.requiredValidation,
			company_id: Validations.requiredValidation,
			country_id: Validations.requiredValidation,
			ispp_id: Validations.requiredValidation,
		},
		connectionMedia: {
			connection_id: Validations.requiredValidation,
			media_link: Validations.requiredValidation,
			ad_id: Validations.requiredValidation,
		},
	},

	watch: {
		"connection.country_id": function (countryId) {
			this.$v.connection.company_id.$model = null;
			this.$v.connection.ispp_id.$model = null;
			this.$v.connection.project_id.$model = null;
			this.$v.connection.platform_id.$model = null;
			this.$v.connection.application_id.$model = null;
			if (countryId) {
				this.companies = [];
				this.ispps = [];
				this.projects = [];
				this.platforms = [];
				this.applications = [];
				this.adAccounts = [];
				this.fetchItems({ type: "companies", id: countryId });
			}
		},
		"connection.company_id": function (company_id) {
			this.$v.connection.ispp_id.$model = null;
			this.$v.connection.project_id.$model = null;
			this.$v.connection.platform_id.$model = null;
			this.$v.connection.application_id.$model = null;

			if (company_id) {
				this.ispps = [];
				this.projects = [];
				this.platforms = [];
				this.applications = [];
				this.adAccounts = [];
				this.fetchItems({ type: "ispps", id: company_id });
				this.fetchItems({ type: "projects", id: company_id });
				this.fetchItems({ type: "platforms", id: company_id });
			}
		},
		"connection.platform_id": function (platform_id) {
			this.$v.connection.application_id.$model = null;
			if (platform_id) {
				this.applications = [];
				this.adAccounts = [];
				this.fetchItems({ type: "applications", id: platform_id });
			}
		},
		"connection.application_id": function (application_id) {
			this.$v.connection.ad_account_id.$model = null;
			if (application_id) {
				this.adAccounts = [];
				this.fetchItems({ type: "adAccounts", id: application_id });
			}
		},
	},

	methods: {
		toggle() {
			this.model = !this.model;
			if (this.model) {
				this.generated_link = null;
				this.fetchCountries();
			}
		},

		copyGeneratedLink() {
			if (navigator.clipboard) {
				const app = this;
				navigator.clipboard.writeText(app.generated_link).then(
					function () {
						// app.$toastr.i(app.$tr("link_has_copied_to_your_clipboard"));
				app.$toasterNA("primary",app.$tr('link_has_copied_to_your_clipboard'));

					},
					function (err) {
						//  app.$toastr.e(app.$tr("link_is_not_copied"));
						app.$toasterNA("red", app.$tr("link_is_not_copied"));
					},
				);
			}
		},

		async fetchCountries() {
			try {
				this.isFetchingcountries = true;
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				this.countries = data.items;
			} catch (error) {}
			this.isFetchingcountries = false;
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

		resetForm() {
			this.generated_link = null;
			if (this.$refs.insertForm) {
				this.$refs.insertForm.reset();
				this.connectionMedia = {
					connection_id: null,
					media_link: null,
					ad_id: null,
				};
			}
		},

		resetLink() {
			this.generated_link = null;
			this.connection.landing_page_link = null;
			if (this.$refs.insertForm) {
				this.$refs.insertForm.resetValidation();
				this.connectionMedia = {
					connection_id: null,
					media_link: null,
					ad_id: null,
				};
			}
		},

		async validateForm() {
			this.$refs.insertForm.validate();
			if (this.generated_link) {
				this.$v.connectionMedia.$touch();
				if (!this.$v.connectionMedia.$invalid) {
					this.isSubmitting = true;
					await this.updateConnection(this.connectionMedia);
					this.isSubmitting = false;
				}
			} else {
				this.$v.connection.$touch();
				if (!this.$v.connection.$invalid) {
					this.isSubmitting = true;
					await this.insertRecord(this.connection);
					this.isSubmitting = false;
				}
			}
		},

		async updateConnection(connectionMedia) {
			try {
				const url = "advertisement/connection/update";
				const { data } = await this.$axios.put(url, connectionMedia);
				if (data.result) {
					// this.$toasterNA("green", this.$tr('successfully_inserted'));
				this.$toasterNA("green", this.$tr('successfully_inserted'));

				} else if (data.not_found) {
					// this.$toastr.e(this.$tr("ad_id_not_found"));
					this.$toasterNA("red", this.$tr("ad_id_not_found"));
				} else if (data.invalid_ad_id) {
					// this.$toastr.e(this.$tr("invalid_ad_ad_account_id"));
					this.$toasterNA("red", this.$tr("invalid_ad_ad_account_id"));
				} else if (data.ad_id_exists) {
					// this.$toastr.e(this.$tr("ad_id_already_exists"));
					this.$toasterNA("red", this.$tr("ad_id_already_exists"));
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},

		async insertRecord(connection) {
			try {
				const url = "advertisement/connection/generate_link";
				const { data } = await this.$axios.post(url, connection);
				if (data.result) {
					this.$toasterNA("green", this.$tr('successfully_inserted'));
					const insertedRecord = data.connection;
					this.generated_link = insertedRecord.generated_link;
					this.connectionMedia.connection_id = insertedRecord.id;
				} else {
					// this.$toastr.e(this.$tr(data.message || "something_went_wrong"));
					this.$toasterNA(
						"red",
						this.$tr(data.message || "something_went_wrong"),
					);
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
	},
	components: { CloseBtn, CustomInput, FormBtn, SelectedItem },
};
</script>

<style></style>
