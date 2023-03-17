<template>
	<div>
		<Stepper
			title="Stepper Name"
			cookieName="cookie_name_must_be_uniqe_across_the_app"
			@close="show = false"
			:show="show"
			:steps="steps"
			:form="form"
			:validateRules="validateRules"
			:submit="submit"
		/>
		<v-btn @click="show = !show">Test</v-btn>
	</div>
</template>
<script>
import Stepper from "../Stepper.vue";
import StepperStepVue from "./StepperStep.vue";
import Validations from "../../../validations/validations";

export default {
	data() {
		return {
			show: false,
			steps: [
				{
					title: "Step 1",
					component: StepperStepVue,
					props: {},
					hints: [
						"step 1 1 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus.",
						"step 1 2 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus.",
						"step 1 3 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus.",
					],
				},
				{
					title: "Step 2",
					component: StepperStepVue,
					props: {},
				},
				{
					title: "Step 3",
					skip: true,
					component: StepperStepVue,
					hints: [
						"step 3 1 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus",
						"step 3 2 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus",
						"step 3 3 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus",
					],
				},
				{
					title: "Step 4",
					component: StepperStepVue,
					props: {},
					hints: [
						"step 4 1 Lorem ipsum dolor sit amet consectetur adipisicing elit. Quiarecusandae possimus.",
					],
				},
			],
			form: {
				name: "",
			},
			validateRules: {
				form: {
					name: Validations.name100Validation,
				},
			},
		};
	},
	methods: {
		async submit(formRef, vuelidate) {
			return true;
		},
	},
	components: { Stepper },
};
</script>
