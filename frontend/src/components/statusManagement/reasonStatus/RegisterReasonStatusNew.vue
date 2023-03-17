<template>
	<div>
		<Stepper
			:title="$tr('add_status_events')"
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
import ReasonStatus1 from "./ReasonStatus1.vue";
import ReasonStatus2 from "./ReasonStatus2.vue";
import Helpers from "../../../helpers/helpers";
import HandleException from "../../../helpers/handle-exception";
import { mapActions } from "vuex";

export default {
	name: "RegisterReasonStatusNew",
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
					title: "general",
					component: ReasonStatus1,
					props: {},
					hints: [],
				},
        {
					title: "reasons",
					component: ReasonStatus2,
					props: {},
					hints: [],
				},
			],

			form: {
        sub_systems:"",
        types:[],
				reasons:[],
				slug:this.$route.params.slug,
			},

			validateRules: {
				form: {
          sub_systems: Validations.requiredValidation,
          types :  Validations.requiredValidation,
					reasons: Validations.requiredValidation,
				},
			},
		};
	},

	methods: {
		...mapActions({
      fetchStatusEvent: "statusEvents/fetchStatusEvent",
    }),
		reset() {
			this.form = {
        sub_systems: '',
        types:[],
				reasons:[],
				slug:this.$route.params.slug,
			};
		},
		async submit(formRef, vuelidate) {
			try {
        const reasonStatus = Helpers.getFormData(this.form);
        const url = "status_events";
        const { data } = await this.$axios.post(url, reasonStatus);
        if (data.result) {
					this.fetchStatusEvent({ slug: this.slug });
          return true;
        } else {
					this.$toasterNA("red", this.$tr('something_went_wrong'));
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
