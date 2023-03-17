<template>
	<v-form
		ref="departmentForm"
		lazy-validation
		@submit.prevent="onSubmit"
		id="departmentForm"
	>
		<CustomStepper
			ref="customStepper"
			:headers="stepper_header"
			:canNext="false"
			:loading="isDataLoaded"
			:isSubmitting="isLoadingOrSubmitting"
			@validate="validateStepper"
			@close="closeDialog"
			@changeToValidate="changeTo"
			@submit="onSubmit"
		>
			<template v-slot:general>
				<FilterCountry
					ref="filterCountry"
					showCompanyBtn
					mt-4
					isDepartment
					skipDepartment
				/>
			</template>
			<template v-slot:department>
				<div>
					<div class="w-full">
						<CustomInput
							v-model.trim="$v.department.name.$model"
							:rules="
								validate(
									$v.department.name,
									$root,
									$tr('item_name', $tr('department'))
								)
							"
							:label="$tr('item_name_required', $tr('department'))"
							:placeholder="$tr('item_name', $tr('department'))"
							:error-messages="nameErrorMessage"
							type="textfield"
							name="name"
						/>
					</div>
					<div class="w-full mt-2">
						<CustomInput
							v-model.trim="$v.department.business_location_id.$model"
							:rules="
								validate(
									$v.department.business_location_id,
									$root,
									'business_location'
								)
							"
							:items="allBusinessLocations"
							:label="$tr('label_required', $tr('business_location'))"
							:placeholder="$tr('choose_item', $tr('business_location'))"
							item-value="id"
							item-text="name"
							type="autocomplete"
						/>
					</div>

					<div>
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full">
								<CustomInput
									v-model="selectedIndustry"
									:items="industries"
									:label="$tr('label_required', $tr('industry'))"
									:placeholder="$tr('choose_item', $tr('industry'))"
									item-value="id"
									return-object
									item-text="name"
									class="me-md-4 mb-md-2"
									type="autocomplete"
									:error-messages="industryErrorMessage"
								/>
							</div>
							<div class="w-100">
								<FormBtn
									@click="addSelectedIndustry"
									:title="'add_plus'"
									:class="`mt-md-4 mb-2`"
								/>
							</div>
						</div>
						<div class="selected d-flex flex-wrap">
							<SelectedItem
								v-for="selectedIndustry in selectedIndustries"
								:key="selectedIndustry.id"
								@close="removeSelectedIndustry(selectedIndustry.id)"
								:title="selectedIndustry.name"
							/>
						</div>
					</div>
				</div>
			</template>
			<template v-slot:remark>
				<LabelRemark
					ref="remarksRef"
					:item="$v.department"
				/>
			</template>
			<template v-slot:done>
				<div class="d-flex justify-center">
					<done-message
						:title="$tr('item_all_set', $tr('department'))"
						:subTitle="$tr('you_can_access_your_item', $tr('department'))"
					/>
				</div>
			</template>
		</CustomStepper>
	</v-form>
</template>
<script>
import Department from "../../../configs/pages/departments";
import FilterCountry from "../../users/FilterCountry.vue";
import CustomInput from "../../design/components/CustomInput.vue";
import CustomStepper from "../../design/FormStepper/CustomStepper.vue";
import { mapGetters, mapActions } from "vuex";
import DepartmentValidations from "../../../validations/department-validations";
import DoneMessage from "../../design/components/DoneMessage.vue";
import HandleException from "../../../helpers/handle-exception";
import Helpers from "../../../helpers/helpers";
import GlobalRules from "~/helpers/vuelidate";
import LabelRemark from "../../users/LabelRemark.vue";
import FormBtn from "../../design/components/FormBtn";
import SelectedItem from "../../design/components/SelectedItem";

export default {
	components: {
		CustomStepper,
		FilterCountry,
		CustomInput,
		DoneMessage,
		LabelRemark,
		FormBtn,
		SelectedItem,
	},
	props: {
		options: {
			type: Object,
			require: true,
		},
		tabKey: {
			type: String,
			require: true,
		},
	},
	data() {
		return {
			validate: GlobalRules.validate,
			nameErrorMessage: "",
			stepper_header: Department(this).steppers,

			//*HM* industry section *HM*
			selectedIndustry: null,
			selectedIndustries: [],
			industryErrorMessage: "",

			department: {
				name: "",
				company_ids: "",
				industries: "",
				note: "",
				business_location_id: "",
			},
			isLoadingOrSubmitting: false,
			isDataLoaded: false,
			allBusinessLocations: [],
		};
	},

	validations: {
		department: DepartmentValidations.validations.department,
	},

	async created() {
		this.fetchIndustries();
		this.fetchBusinessLocations();
	},

	computed: {
		...mapGetters({
			industries: "filterData/getIndustries",
		}),
	},
	methods: {
		...mapActions({
			fetchDepartments: "departments/fetchDepartments",
			fetchIndustries: "filterData/fetchIndustries",
		}),

		//*HM* Remove selected Industry *HM*
		removeSelectedIndustry(id) {
			if (id) {
				this.selectedIndustries = this.selectedIndustries.filter(
					(item) => item.id !== id
				);
			}
		},

		//*HM* Add the selected industry *HM*
		addSelectedIndustry() {
			if (this.selectedIndustry) {
				this.industryErrorMessage = "";
				const itemId = this.selectedIndustry.id;
				const found = this.selectedIndustries.find(
					(item) => item.id === itemId
				);
				if (found) return;

				this.selectedIndustries.push(this.selectedIndustry);
				this.selectedIndustry = "";
			} else {
				this.industryErrorMessage = this.$tr(
					"item_is_required",
					this.$tr("industry")
				);
			}
		},

		async changeTo(current_index) {
			switch (current_index) {
				case 2:
					if (this.isGenerateValid()) {
						this.$refs.customStepper.changeTo(current_index);
					}
					break;
				case 3:
					if (this.isGenerateValid()) {
						if (await this.isStepTwoDataValid()) {
							this.$refs.customStepper.changeTo(current_index);
						}
					}
					break;
			}
		},

		async validateStepper(currentStep) {
			this.department.industries = this.selectedIndustries.map(
				(item) => item.id
			);
			switch (currentStep) {
				case 1:
					if (this.isGenerateValid()) {
						this.$refs.customStepper?.forceNext();
						this.$refs.departmentForm.resetValidation();
					}
					break;
				case 2:
					if (await this.isStepTwoDataValid()) {
						this.$refs.customStepper?.forceNext();
						this.$refs.departmentForm.resetValidation();
					}
					break;
				case 3:
					if (this.isRemarksValid()) {
						this.$refs.customStepper?.forceNext();
						this.$refs.departmentForm.resetValidation();
					}
					break;
			}
		},

		isGenerateValid() {
			return this.$refs.filterCountry.checkValidation();
		},

		isRemarksValid() {
			return this.$refs.remarksRef.checkValidation();
		},

		async isStepTwoDataValid() {
			if (this.selectedIndustries.length < 1) {
				this.industryErrorMessage = this.$tr(
					"item_is_required",
					this.$tr("industry")
				);
			}
			this.validateForm();
			let isNameDublicate = false;
			let isStepTwoDataInValid =
				this.$v.department.name.$invalid ||
				this.$v.department.industries.$invalid ||
				this.$v.department.business_location_id.$invalid;
			if (!isStepTwoDataInValid) {
				this.isDataLoaded = true;
				this.$refs.departmentForm.resetValidation();
				const res = await this.checkNameUniqueness();
				isNameDublicate = res.isNameAlreadyExists;
				if (isNameDublicate) {
					this.nameErrorMessage = this.$tr(
						"item_already_exists",
						this.$tr("department")
					);
				} else {
					this.nameErrorMessage = "";
				}
				this.isDataLoaded = false;
				return !(!isStepTwoDataInValid && isNameDublicate);
			} else {
				return !isStepTwoDataInValid;
			}
		},

		async checkNameUniqueness() {
			try {
				this.$v.department.$touch();
				const name = this.$v.department?.name.$model;
				const response = await this.$axios.post(
					"departments/check-uniqueness",
					{ name: name }
				);
				return {
					isNameAlreadyExists: response?.data?.name,
				};
			} catch (error) {
				HandleException.handleApiException(this, error);
				return false;
			}
		},

		setData() {
			const selectedCompanies = this.$refs.filterCountry.selectedCompanies;
			this.department.company_ids = selectedCompanies.map(
				(company) => company.id
			);
		},

		resetForm() {
			this.$refs.departmentForm.reset();
			this.$refs.departmentForm.resetValidation();
			this.$refs.filterCountry.selectedCountries = [];
			this.$refs.filterCountry.selectedCompanies = [];
			this.selectedIndustries = [];
			this.selectedIndustry = null;
		},

		closeDialog() {
			this.resetForm();
			this.$emit("onCloseDialog");
		},

		isFormDataValid() {
			return this.$v.department.$invalid === false;
		},

		async insertRecord(department) {
			try {
				this.isLoadingOrSubmitting = true;
				const response = await this.$axios.post("/departments", department);
				if (response?.status === 201 && response?.data?.result) {
					// this.fetchDepartments({
					// 	...this.options,
					// 	key: this.tabKey,
					// });
					this.resetForm();
					this.$refs.customStepper.forceNext();
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
			this.isDataLoaded = false;
			this.isLoadingOrSubmitting = false;
		},

		async onSubmit() {
			this.setData();
			this.validateForm();
			if (this.isFormDataValid()) {
				this.isDataLoaded = true;
				let formData = Helpers.getFormData(this.department);
				await this.insertRecord(formData);
				this.$refs.departmentForm.resetValidation();
			} else {
				// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

			}
		},

		validateForm() {
			this.$refs.departmentForm.validate();
			this.$v.department.$touch();
		},
		// fetch items according to table name
		async fetchBusinessLocations() {
			try {
				const response = await this.$axios.get("/business-locations/for-select");
				this.allBusinessLocations = response?.data?.data;
				return response?.data?.data;
			} catch (error) {
				return null;
			}
		},
	},
};
</script>
