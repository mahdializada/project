<template>
	<div>
		<Stepper
			ref="Stepper"
			title="Category"
			cookieName="category_stepper"
			:show="show"
			:steps="steps"
			:form="form"
			:validateRules="validateRules"
			:submit="submit"
			:skipStep="skip"
			@close="show = false"
			@reset="() => {}"
		/>

	</div>
</template>
<script>
import Stepper from "../Stepper.vue";
import CategoryTypeSelection from "./Steps/CategoryTypeSelection.vue";
import CategoryParentSelection from "./Steps/CategoryParentSelection.vue";
import CategoryInfo from "./Steps/CategoryInfo.vue";
import Validations from "~/validations/validations";
import CategroyAttributes from "./Steps/CategroyAttributes.vue";

import Helpers from "~/helpers/helpers";
import single_salse_menu from '../../../configs/menus/single_salse_menu';
// "../../../helpers/helpers";

export default {
	data() {
		return {
			show: false,
			skip: [2],
			duplicateError: null,
			steps: [
				{
					id: "section",
					title: "Section",
					component: CategoryTypeSelection,
				},
				{
					id: "category",
					title: "Category",
					component: CategoryParentSelection,
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
				},
			],
			form: {
				itemType: "",
				selectedCategory: null,
				name: "",
				description: "",
				icon: null,
				banner: null,
				attribute: [],
				newAttribute: [],
			},
			validateRules: {
				form: {
					name: Validations.name100Validation,
					itemType: Validations.requiredValidation,
					selectedCategory: Validations.requiredValidation,
					description: "",
					icon: Validations.imageValidation,
					banner: Validations.imageValidation,
					attribute: Validations.requiredValidation,
					newAttribute: {
						$each: {
							name: Validations.name100Validation,
							value: "",
						},
					},
				},
			},
		};
	},
	methods: {
		reset() {
			this.form = {
				itemType: "",
				selectedCategory: null,
				name: "",
				description: "",
				icon: null,
				banner: null,
				attribute: [],
				newAttribute: [],
			};
		},
		delay(delayInms) {
			return new Promise((resolve) => {
				setTimeout(() => {
					resolve(2);
				}, delayInms);
			});
		},
		async submit(formRef, vuelidate) {
			if (!this.form.selectedCategory) {
				this.form.selectedCategory = "";
			}
			try {
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
	components: { Stepper, CategroyAttributes },
};
</script>
