<template>
	<div>
		<Stepper
			:title="$tr('add_reason')"
			cookieName="cookie_name_must_be_uniqe_across_the_app"
			@close="show = false"
			:show="show"
			:steps="steps"
			:form="form"
			:validateRules="validateRules"
			@reset="reset"
			:submit="submit" />
	</div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "../../../validations/validations";
import registerReasonNew from "./registerReasonNew.vue";
import Helpers from "../../../helpers/helpers";
import HandleException from "../../../helpers/handle-exception";
import { mapActions } from "vuex";


export default {
	name: "registerReasonStepper",
	props:{
		slug: {
			type: String,
		},
	},
	data() {
		return {
			show: false,
			steps: [
				{
					title: "Add Reasons",
					component: registerReasonNew,
					props: {},
					hints: [],
				},
			],

			form: {
				titles: [],
			},

			validateRules: {
				form: {
					titles: {
						$each: {
							disabled: true,
							value: Validations.requiredValidation,
						},
					},
				},
			},
		};
	},

	methods: {

		...mapActions({
      fetchReasons: "reasons/fetchReasons",
    }), 

		reset() {
			this.form = {
				titles: [],
			};
		},
		async submit(formRef, vuelidate) {
			try {
				let formData =  [];
				formData.titles = this.form.titles.map(row=>row.value);
				formData.slug = this.slug;
				const action = Helpers.getFormData(formData);
				const url = "reasons";
				const { data } = await this.$axios.post(url, action);
				if (data == 201) {
					await this.fetchReasons({ slug: this.slug });
					return true;
				} else {
					this.$toasterNA("red", this.$tr("something_went_wrong"));
					return false;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		showDialog() {
			this.show = true;
		},
	},

	components: { Stepper },
};
</script>
