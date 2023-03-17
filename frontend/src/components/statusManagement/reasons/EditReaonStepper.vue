<template>
	<div>
		<Stepper
			:title="$tr('edit_reason')"
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
import EditReasonNew from "./EditReasonNew.vue";
import Helpers from "../../../helpers/helpers";
import HandleException from "../../../helpers/handle-exception";
import { mapActions } from "vuex";

export default {
	name: "EditReasonStepper",
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
					title: "Edit Reason",
					component: EditReasonNew,
					props: {},
					hints: [],
				},
			],

			form: {
				id:'',
				slug:"",
				title : '' ,
			},

			validateRules: {
				form: {
					id:Validations.requiredValidation,
					slug:Validations.requiredValidation,
					title : Validations.requiredValidation
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
				title: "",
				id:'',
				slug :this.slug,

			};
		},
		async submit(formRef, vuelidate) {
			
			try {
				const action = this.form;
				const url = "reasons/id";
				const { data } = await this.$axios.put(url, action);
				if (data.result) {
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
		showDialog(item = null) {
      this.form.title = item.title;
			this.form.id = item.id;
			this.form.slug = this.slug;
			this.show = true;
		},
	},

	components: { Stepper },
};
</script>
