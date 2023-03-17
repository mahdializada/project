<template>
	<Stepper
		:title="$tr('create_item',$tr('ispp'))"
		cookieName="cookie_name_must_be_uniqe_across_the_app"
		@close="show = false"
		:show="show"
		:steps="steps"
		:form="form"
		:validateRules="validateRules"
		:submit="submit"
		@reset="reset"
		@OnCategoryChanged="setCategoryId($event)"
	/>
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "../../../validations/validations";
import StudyStep from "./StudyStep";
import StudyStep2 from "./StudyStep2";
import GeneralInfo from "./GeneralInfoStep";
import StoreInfo from "./StoreInfo";
import FinancialInfo from "./FinancialInfo";
import HandleException from "../../../helpers/handle-exception";

export default {
	name: "IsspStepper",
	components: { Stepper },
	data() {
		return {
			show: false,
			category_id: "",
			steps: [
				{
					title: "CATEGORY & PRODUCT",
					component: StudyStep,
					props: {},
					hints: [],
				},
				{
					title: "STUDY",
					component: StudyStep2,
					props: {},
					hints: [],
				},
				{
					title: "ISP GENERAL INFO",
					component: GeneralInfo,
					props: {
						category_id: this.category_id,
					},
				},
				{
					title: "STORE INFO",
					skip: true,
					component: StoreInfo,
					hints: [],
				},
				{
					title: "FINANCIAL INFO",
					component: FinancialInfo,
					props: {},
					hints: [],
				},
			],
			form: {
				category_id: "",
				product_id: "",
				name: "",
				creation_type: "",
				selling_goals: "",
				sale_currency: "",
				landing_page_style: "",
				company_id: "",
				display_language_id: "",
				landing_page_note: "",
				product_study_id: [],
				sales_modal: "",
				attribute: [],
				price_patterns: {
					free_quantity_style: [
						{
							quantity: "12",
							quantityAmount: "313",
						},
					],
					multiple_quantity_style: [
						{
							quantity: "12",
							quantityAmount: "123",
						},
						{
							quantity: "123",
							quantityAmount: "123",
						},
						{
							quantity: "1",
							quantityAmount: "1",
						},
						{
							quantity: "2",
							quantityAmount: "2",
						},
					],
					gift_style: [
						{
							quantity: "12",
							quantityAmount: "Bar",
						},
					],
				},
				head_selling_price: "",
			},
			validateRules: {
				form: {
					category_id: Validations.requiredValidation,
					name: Validations.requiredValidation,
					product_id: Validations.requiredValidation,
					product_study_id: Validations.requiredValidation,
					creation_type: Validations.requiredValidation,
					selling_goals: Validations.requiredValidation,
					landing_page_style: Validations.requiredValidation,
					company_id: Validations.requiredValidation,
					display_language_id: Validations.requiredValidation,
					sale_currency: Validations.requiredValidation,
					landing_page_note: Validations.requiredValidation,
					sales_modal: Validations.requiredValidation,
					attribute: Validations.requiredValidation,
					price_patterns: Validations.requiredValidation,
					head_selling_price: Validations.requiredValidation,
				},
			},
		};
	},
	methods: {
		reset() {
			this.form = {
				product_id: [],
				name: "",
				creation_type: "",
				selling_goals: "",
				sale_currency: "",
				landing_page_style: "",
				company_id: "",
				display_language_id: "",
				landing_page_note: "",
				product_study_id: [],
				sales_modal: "",
				attribute: [],
				price_patterns: "",
				head_selling_price: "",
			};
		},
		async submit(formRef, vuelidate) {
			try {
				const url = "single-sales/ispp";
				const { data } = await this.$axios.post(url, vuelidate.$model);

				if (data.result) {
					this.$toasterNA("green", this.$tr('successfully_inserted'));
					this.$emit("pushRecord", data?.ispp);

					return true;
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

				}
			} catch (error) {
				HandleException.handleApiException(this, error);
				return false;
			}
		},

		setCategoryId(event) {
			console.log("categoryChange", event);
		},

		showDialog() {
			this.show = true;
		},
	},
};
</script>

<style scoped></style>
