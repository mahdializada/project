<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<v-card class="main-Card px-3">
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px"
			>
				<h2 class="text-h5 font-weight-bold">
					{{
						isEdit
							? $tr("update_item", $tr("project"))
							: $tr("create_item", $tr("project"))
					}}
				</h2>

				<CloseBtn @click="toggle" type="button" />
			</v-card-title>
			<v-card-text
				class="position-relative card-pos"
				style="height: 650px; overflow-y: auto"
			>
				<v-form ref="insertForm" lazy-validation>
					<div class="countries">
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full">
								<CustomInput
									:items="companies"
									v-model="selectedCompany"
									return-object
									:label="$tr('label_required', $tr('company'))"
									:placeholder="$tr('choose_item', $tr('company'))"
									item-value="id"
									item-text="name"
									logoName=""
									has-logo
									type="autocomplete"
									class="me-md-4 mb-md-2"
									ref="projectInput"
									:loading="isFetchingCompany"
									:error-messages="companyErrorMessage"
								/>
							</div>
							<div class="w-100">
								<FormBtn
									@click="addSelectedCompany"
									:title="'add_plus'"
									:class="`mt-md-4 mb-2 top-minus-4`"
								/>
							</div>
						</div>
						<div class="selected d-flex flex-wrap">
							<SelectedItem
								v-for="company in selectedCompanies"
								:key="company.id"
								@close="() => removeSelectedCompany(company.id)"
								:title="company.name"
								logoName=""
								:logo-url="company.logo"
							/>
						</div>
					</div>
					<div class="countries">
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full">
								<CustomInput
									:items="countries"
									v-model="selectedCountry"
									return-object
									item-value="id"
									item-text="name"
									:label="$tr('label_required', $tr('country'))"
									:placeholder="$tr('choose_item', $tr('country'))"
									country
									ref="countryInput"
									type="autocomplete"
									class="me-md-4 mb-md-2 mb-0"
									:error-messages="countryErrorMessage"
									:loading="isFetchingCountry"
								/>
							</div>
							<div class="w-100">
								<FormBtn
									@click="addSelectedCountry"
									:title="'add_plus'"
									:class="`mt-md-4 mb-2 top-minus-4`"
								/>
							</div>
						</div>
						<div class="selected d-flex flex-wrap">
							<SelectedItem
								v-for="country in selectedCountries"
								:key="country.id"
								@close="() => removeSelectedCountry(country.id)"
								:title="country.name"
								:flag="`${country.iso2.toLowerCase()}`"
							/>
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('project_name'))"
								placeholder="project_name"
								v-model="$v.project.name.$model"
								:rules="validate($v.project.name, $root, 'project_name')"
								type="textfield"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('domain')"
								placeholder="domain"
								v-model="$v.project.domain.$model"
								:rules="validate($v.project.domain, $root, 'domain')"
								type="textfield"
								class="ms-md-2"
							/>
						</div>
					</div>
				</v-form>
			</v-card-text>
			<v-card-actions class="pa-3">
				<v-btn
					@click="resetForm"
					color="success"
					class="stepper-btn mr-2"
					type="button"
				>
					{{ $tr("reset_form") }}
				</v-btn>
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					class="stepper-btn px-3"
					style="min-width: 160px"
					@click="validateForm"
					:loading="isSubmitting"
					:disable="isSubmitting"
				>
					<template v-slot:loader>
						<span>
							<span>
								{{ $tr("submitting") + "..." }}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2"
							/>
						</span>
					</template>
					{{ $tr("submit") }}
				</v-btn>
				<v-btn
					@click="toggle"
					color="error"
					class="stepper-btn"
					:type="'button'"
				>
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
			isEdit: false,
			validate: GlobalRules.validate,
			isSubmitting: false,
			isFetchingCompany: false,
			isFetchingCountry: false,
			selectedCountry: null,
			selectedCompany: null,
			selectedCountries: [],
			selectedCompanies: [],
			countryErrorMessage: null,
			companyErrorMessage: null,
			companies: [],
			countries: [],

			project: {
				name: null,
				domain: null,
				countries: [],
				companies: null,
			},
		};
	},

	validations: {
		project: {
			name: Validations.requiredValidation,
			domain: Validations.domainValidation,
			countries: Validations.requiredValidation,
			companies: Validations.requiredValidation,
		},
	},

	methods: {
		toggle(item) {
			if (item) {
				let { id, name, domain, countries, companies } = item;
				this.selectedCountries = countries;
				this.selectedCompanies = companies;
				this.selectedCountry = null;
				this.selectedCompany = null;
				this.project = {
					id,
					name,
					domain,
				};
				this.isEdit = true;
			} else {
				if (this.isEdit) {
					this.project = {
						name: null,
						domain: null,
						companies: [],
						countries: [],
					};
					this.selectedCountry = null;
					this.selectedCountries = [];
					this.countryErrorMessage = null;
					this.selectedCompany = null;
					this.selectedCompanies = [];
					this.companyErrorMessage = null;
					this.isEdit = false;
					setTimeout(() => {
						this.resetForm();
					}, 100);
				}
			}
			this.model = !this.model;
			if (this.model) {
				this.fetchCountries();
				this.fetchCompanies();
			}
		},

		async fetchCountries() {
			try {
				this.isFetchingCountry = true;
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				this.countries = data.items;
			} catch (error) {}
			this.isFetchingCountry = false;
		},

		async fetchCompanies() {
			try {
				this.isFetchingCompany = true;
				const url = "common/companies/";
				const { data } = await this.$axios.get(url);
				this.companies = data.companies;
			} catch (error) {}
			this.isFetchingCompany = false;
		},

		// remove selected countries
		async removeSelectedCountry(id) {
			let items = this.selectedCountries.filter((item) => item.id !== id);
			this.selectedCountries = items;
			const requiredText = this.$tr("item_is_required", this.$tr("country"));
			this.selectedCountries.length < 1
				? (this.countryErrorMessage = requiredText)
				: (this.countryErrorMessage = "");
		},

		// add new country to selected countries
		async addSelectedCountry() {
			const requiredText = this.$tr("item_is_required", this.$tr("country"));
			if (this.selectedCountry) {
				let itemId = this.selectedCountry.id;
				const country = this.countries.find(({ id }) => id === itemId);
				if (!country) {
					return;
				}
				if (this.selectedCountries?.some(({ id }) => id === itemId)) {
					return;
				}
				this.selectedCountries.unshift(country);
				this.selectedCountry = "";
				this.selectedCountries?.length < 1
					? (this.countryErrorMessage = requiredText)
					: (this.countryErrorMessage = "");
			} else {
				this.countryErrorMessage = requiredText;
			}
		},

		// remove selected companies
		async removeSelectedCompany(id) {
			let items = this.selectedCompanies.filter((item) => item.id !== id);
			this.selectedCompanies = items;
			const requiredText = this.$tr("item_is_required", this.$tr("company"));
			this.selectedCompanies.length < 1
				? (this.companyErrorMessage = requiredText)
				: (this.companyErrorMessage = "");
		},

		// add new company to selected companies
		async addSelectedCompany() {
			const requiredText = this.$tr("item_is_required", this.$tr("company"));
			if (this.selectedCompany) {
				let itemId = this.selectedCompany.id;
				const company = this.companies.find(({ id }) => id === itemId);
				if (!company) {
					return;
				}
				if (this.selectedCompanies?.some(({ id }) => id === itemId)) {
					return;
				}
				this.selectedCompanies.unshift(company);
				this.selectedCompany = "";
				this.selectedCompanies?.length < 1
					? (this.companyErrorMessage = requiredText)
					: (this.companyErrorMessage = "");
			} else {
				this.companyErrorMessage = requiredText;
			}
		},

		resetForm() {
			this.selectedCountry = null;
			this.selectedCountries = [];
			this.countryErrorMessage = null;
			this.selectedCompany = null;
			this.selectedCompanies = [];
			this.companyErrorMessage = null;
			if (this.$refs.insertForm) {
				this.$refs.insertForm.reset();
			}
		},

		async validateForm() {
			const ids = this.selectedCountries.map(({ id }) => id);
			const companyIds = this.selectedCompanies.map(({ id }) => id);
			this.$v.project.countries.$model = ids;
			this.$v.project.companies.$model = companyIds;
			this.$refs.insertForm.validate();
			this.$v.project.$touch();
			if (!this.$v.project.$invalid) {
				this.isSubmitting = true;
				await this.insertRecord(this.project);
				this.isSubmitting = false;
			}
		},

		async insertRecord(project) {
			try {
				const url = "advertisement/projects";
				let data = {};
				if (this.isEdit) {
					const response = await this.$axios.put(
						url + `/${project.id}`,
						project
					);
					data = response.data;
				} else {
					const response = await this.$axios.post(url, project);
					data = response.data;
				}
				if (data.result) {
					if (this.isEdit) {
						// this.$toasterNA("green", this.$tr('successfully_updated'));
						this.$toasterNA("green", this.$tr("successfully_updated"));

						this.toggle(null);
					} else {
						this.$toasterNA("green", this.$tr("successfully_inserted"));
						const insertedRecord = data.data;
						this.$emit("pushRecord", insertedRecord);
					}
					this.resetForm();
				} else {
					//  this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr("something_went_wrong"));
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
