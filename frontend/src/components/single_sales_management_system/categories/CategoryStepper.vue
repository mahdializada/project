<template>
	<div>
		<stepper
			ref="Stepper"
			title="Category"
			cookieName="category_stepper"
			:show="show"
			:steps="steps"
			:form="form"
			:validateRules="validateRules"
			:submit="submit"
			@close="show = false"
			@reset="() => {}"
		/>

	</div>
</template>
<script>
import CategoryInfo from "./Steps/CategoryInfo.vue";
// import CategoryParentSelection from "./Steps/CategoryParentSelection.vue";
import CategoryTypeSelection from "./Steps/CategoryTypeSelection.vue";
import CategroyAttributes from "./Steps/CategroyAttributes.vue";
import Validations from "~/validations/validations";
import Helpers from "~/helpers/helpers";
import Stepper from '../../horizontal_stepper/Stepper.vue';
import GeneralInfo from "./Steps/GeneralInfoStep.vue";

export default {
  components: { Stepper },
	data() {
		return {
			show: false,
			duplicateError: null,
			steps:[
				{
					id: "section",
					title: "Section",
					component: CategoryTypeSelection,
				},
				{
					id: "info",
					title: "Info",
					component: CategoryInfo,
				},
				{
					id: "attributes",
					title: "Attributes",
					component: CategroyAttributes,
				}
			],
			form: {
				itemType: "",
				// selectedCategory: null,
				name: "",
				arabic_name: "",
				arabic_description: "",
				description: "",
				icon: null,
				attribute: [],
			},
			validateRules: {
				form: {
					name: Validations.name100Validation,
					arabic_name: Validations.name100Validation,
					itemType: Validations.requiredValidation,
					// selectedCategory: Validations.requiredValidation,
					description: "",
					arabic_description: "",
					icon: Validations.imageValidation,
					attribute: Validations.requiredValidation,
				},
			},
		};
	},
	methods: {
		reset() {
			this.form = {
				itemType: "",
				// selectedCategory: null,
				name: "",
				arabic_name: "",
				arabic_description: "",
				description: "",
				icon: null,
				// banner: null,
				attribute: [],
				// newAttribute: [],
				// country_id: "",
        		// companyIds: [],
			};
		},
		async submit(formRef, vuelidate) {
			// if (!this.form.selectedCategory) {
			// 	this.form.selectedCategory = "";
			// }
			try {
				console.log(this.form);
				const form = await this._.cloneDeep(this.form);
				form.attribute = form.attribute.map((attr) => attr.id);
				const category = Helpers.getFormData(form);
				const url = "single-sales/categories-ssp";
				const data = await this.$axios.post(url, category);

				if (data.data.result) {
					this.$toasterNA("green", this.$tr('successfully_inserted'));
					this.$emit("pushRecord", data?.data?.category);
					this.reset();
					return true;
				} else if (data.data.duplicate_error) {
					this.duplicateError = "Attribute";
					data.data.duplicate_error.forEach((element) => {
						this.duplicateError = this.duplicateError + " " + element;
					});
					this.duplicateError = this.duplicateError + "Already Exist!";
					// this.$toastr.e(this.duplicateError);
			this.$toasterNA("red", this.duplicateError);

				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

					return false;
				}
			} catch (error) {
			}
		},
	},
	watch: {
		["form.itemType"]: function (value) {
			if (value == "sub_category") {
				this.skip = [];
			} else {
				this.skip = [1];
			}
		},
	},
};
</script>
