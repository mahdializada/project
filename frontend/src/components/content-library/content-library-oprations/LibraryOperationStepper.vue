<template>
	<div>
		<stepper
			:title="$tr('create_item', $tr('landing_page'))"
			cookieName="cookie_name_must_be_uniqe_across_the_app"
			@close="show = false"
			:show="show"
			:steps="steps"
			:form="form"
			:validateRules="validateRules"
			@reset="reset"
			:submit="submit"
		/>
	</div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import SelectCountryStep from "./SelectCountryStep.vue";
import ItemCodeStep from "./ItemCodeStep.vue";
import ContentInfoStep from "./ContentInfoStep.vue";
import Media from "./MediaStep.vue";
import MarketingInfoStep from "./MarketingInfoStep.vue";
import DesignInfoStep from "./DesignInfoStep.vue";
import Validations from "../../../validations/validations";

export default {
	components: {
		Stepper,
		SelectCountryStep,
		ItemCodeStep,
		ContentInfoStep,
		Media,
		MarketingInfoStep,
		DesignInfoStep,
	},
	name: "ActionStepper",
	data() {
		return {
			show: false,
			steps: [
				{
					title: this.$tr("country"),
					component: SelectCountryStep,
					props: {},
				},
				{
					title: this.$tr("item_code"),
					component: ItemCodeStep,
					props: {},
				},
				{
					title: this.$tr("content_info"),
					component: ContentInfoStep,
					props: {},
				},
				{
					title: this.$tr("media"),
					component: Media,
					props: {},
				},
				{
					title: this.$tr("marketing_information"),
					component: MarketingInfoStep,
					props: {},
				},
				{
					title: this.$tr("design_information"),
					component: DesignInfoStep,
					props: {},
				},
			],
			form: {
				country_id: "",
				company_id: "",
				sales_type: "",
				item_code: "",
				item_name: "",
				requested_when: "",
				content_source: "",
				content_type: "",
				content_language: "",
				season: "",
				media: [
					{
						project_url: "",
						media_size: "",
					},
				],
				risk_palicy: "",
				main_or_customer: "",
				info_offer: "",
				content_direction: "",
				voice_over: "",
				music: "",
				shooting: "",
				people: "",
				graphics: "",
			},
			validateRules: {
				form: {
					country_id: Validations.requiredValidation,
					company_id: Validations.requiredValidation,
					sales_type: Validations.requiredValidation,
					item_code: Validations.requiredValidation,
					item_name: "",
					media: {
						$each: {
							project_url: Validations.urlValidationRequired,
							media_size: Validations.requiredValidation,
						},
					},
					requested_when: Validations.requiredValidation,
					content_source: Validations.requiredValidation,
					content_type: Validations.requiredValidation,
					content_language: Validations.requiredValidation,
					season: Validations.requiredValidation,
					risk_palicy: Validations.requiredValidation,
					main_or_customer: Validations.requiredValidation,
					info_offer: Validations.requiredValidation,
					content_direction: Validations.requiredValidation,
					voice_over: Validations.requiredValidation,
					music: Validations.requiredValidation,
					shooting: Validations.requiredValidation,
					people: Validations.requiredValidation,
					graphics: Validations.requiredValidation,
				},
			},
		};
	},

	methods: {
		reset() {
			this.form = {
				country_id: "",
				company_id: "",
				sales_type: "",
				item_code: "",
				item_name: "",
				media: [
					{
						project_url: "",
						media_size: "",
					},
				],
				requested_when: "",
				content_source: "",
				content_type: "",
				content_language: "",
				season: "",
				risk_palicy: "",
				main_or_customer: "",
				info_offer: "",
				content_direction: "",
				voice_over: "",
				music: "",
				shooting: "",
				people: "",
				graphics: "",
			};
		},
		async submit() {
			try {
				const response = await this.$axios.post(
					"library/content-library",
					this.form
				);
				if (response.status == 201) {
					this.$toasterNA("green", this.$tr("successfully_updated"));
					this.$emit("addRecord",response.data);
					return true;
				}
			} catch (error) {}
		},
		togglelibrary() {
			this.show = true;
		},
	},
};
</script>
