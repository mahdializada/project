<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<v-card class="main-Card px-3">
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px">
				<h2 class="text-h5 font-weight-bold">
					{{
						isEdit
							? $tr("update_item", $tr("platform"))
							: $tr("create_item", $tr("platform"))
					}}
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
								@blur="$v.platform.platform_name.$touch()"
								v-model="$v.platform.platform_name.$model"
								:rules="
									validate($v.platform.platform_name, $root, 'platform_name')
								"
								:label="$tr('label_required', $tr('platform_name'))"
								:items="platformNames"
								item-value="name"
								item-text="name"
								:placeholder="$tr('choose_item', $tr('platform_name'))"
								type="autocomplete"
								class="me-md-2"
								:loading="isFetchingPlatformNames"
								has-logo />
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('platform_key'))"
								placeholder="platform_key"
								v-model="$v.platform.platform_key.$model"
								:rules="
									validate($v.platform.platform_key, $root, 'platform_key')
								"
								type="textfield"
								class="ms-md-2" />
						</div>
					</div>

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
									:error-messages="companyErrorMessage" />
							</div>
							<div class="w-100">
								<FormBtn
									@click="addSelectedCompany"
									:title="'add_plus'"
									:class="`mt-md-4 mb-2 top-minus-4`" />
							</div>
						</div>
						<div class="selected d-flex flex-wrap">
							<SelectedItem
								v-for="company in selectedCompanies"
								:key="company.id"
								@close="() => removeSelectedCompany(company.id)"
								:title="company.name"
								logoName=""
								:logo-url="company.logo" />
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
								{{ $tr("submitting") + "..." }}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2" />
						</span>
					</template>
					{{ $tr("submit") }}
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
			isEdit: false,
			validate: GlobalRules.validate,
			isSubmitting: false,
			isFetchingCompany: false,
			isFetchingPlatformNames: false,
			companies: [],
			selectedCompany: null,
			companyErrorMessage: null,
			selectedCompanies: [],
			platformNames: [],

			platform: {
				platform_name: null,
				platform_key: null,
				companies: null,
			},
		};
	},

	validations: {
		platform: {
			platform_name: Validations.requiredValidation,
			platform_key: Validations.requiredValidation,
			companies: Validations.requiredValidation,
		},
	},

	methods: {
		toggle(item) {
			if (item) {
				this.platform = item;
				this.isEdit = true;
				this.selectedCompany = null;
				this.selectedCompanies = item.companies;
				this.companyErrorMessage = null;
			} else {
				if (this.isEdit) {
					this.platform = {
						platform_name: null,
						platform_key: null,
						companies: null,
					};
					this.isEdit = false;
					this.selectedCompany = null;
					this.selectedCompanies = [];
					this.companyErrorMessage = null;
					setTimeout(() => {
						this.resetForm();
					}, 100);
				}
			}
			this.model = !this.model;
			if (this.model) {
				this.fetchPlatformNames();
				this.fetchCompanies();
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

		async fetchPlatformNames() {
			try {
				this.isFetchingPlatformNames = true;
				const url = "advertisement/platform_names";
				const { data } = await this.$axios.get(url);
				this.platformNames = data;
			} catch (error) {}
			this.isFetchingPlatformNames = false;
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

		resetForm() {
			if (this.$refs.insertForm) {
				this.$refs.insertForm.reset();
				this.selectedCompany = null;
				this.selectedCompanies = [];
				this.companyErrorMessage = null;
			}
		},

		async validateForm() {
			const requiredText = this.$tr("item_is_required", this.$tr("company"));
			this.selectedCompanies.length < 1
				? (this.companyErrorMessage = requiredText)
				: (this.companyErrorMessage = "");
			const companyIds = this.selectedCompanies.map(({ id }) => id);
			this.$v.platform.companies.$model = companyIds;
			this.$refs.insertForm.validate();
			this.$v.platform.$touch();
			if (!this.$v.platform.$invalid) {
				this.isSubmitting = true;
				await this.insertRecord(this.platform);
				this.isSubmitting = false;
			}
		},

		async insertRecord(platform) {
			try {
				const url = "advertisement/platforms";
				let data = {};
				if (this.isEdit) {
					const response = await this.$axios.put(
						url + `/${platform.id}`,
						platform,
					);
					data = response.data;
				} else {
					const response = await this.$axios.post(url, platform);
					data = response.data;
				}
				if (data.result) {
					if (this.isEdit) {
						// this.$toasterNA("green", this.$tr('successfully_updated'));
						this.$toasterNA("green", this.$tr("successfully_updated"));

						this.toggle(null);
					} else {
						// this.$toasterNA("green", this.$tr('successfully_inserted'));
						this.$toasterNA("green", this.$tr("successfully_inserted"));

						const insertedRecord = data.data;
						this.$emit("pushRecord", insertedRecord);
					}
					this.resetForm();
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
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
