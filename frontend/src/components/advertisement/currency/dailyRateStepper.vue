<template>
	<Stepper
		ref="hello"
		:title="$tr('generate_daily_rate')"
		cookieName="generate_daily_rate"
		:show="show"
		:steps="steps"
		:skipStep="skip"
		:form="currency"
		:validateRules="currencyRules"
		:submit="validateForm"
		@close="show = false"
		:showBack="showBack" />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import dailyRateInfo from "./dailyRateInfo.vue";
import Validations from "~/validations/validations";
import HandleException from "~/helpers/handle-exception";
import { requiredIf } from "vuelidate/lib/validators";

export default {
	components: { Stepper },

	data() {
		return {
			show: false,
			skip: [1],
			showBack: true,
			steps: [
				{
					title: this.$tr("daily_rate"),
					component: dailyRateInfo,
					id: "type",
				},
			],
			isSubmitting: false,

			currency: {
				currency_from: null,
				currency_to: null,
				rate: null,
			},
			currencyRules: {
				form: {
					currency_from: Validations.requiredValidation,
					currency_to: Validations.requiredValidation,
					rate: Validations.requiredValidation,
				},
			},
		};
	},

	watch: {},

	methods: {
		toggle() {
			this.show = !this.show;
			if (this.show) {
				this.skip = [1];
			}
		},

		async validateForm() {
			try {
				const data = await this.$axios.post(
					"advertisement/currency",
					this.currency,
				);

				if (data.data.result) {
					this.$toasterNA("green", this.$tr('successfully_inserted'));

					this.$emit("fetchNewData");
					return true;
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr("something_went_wrong"));
					return false;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
	},
};
</script>

<style></style>
