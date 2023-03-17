<template>
	<div class="w-full h-full d-flex align-center-justify-center mt-5">
		<TextField
			:label="$tr('label_required', $tr('firstname'))"
			v-model="form.name.$model"
			:rules="
				validateRule(
					form.name, // validate objec
					$root, // context
					$tr('firstname') // lable for feedback
				)
			"
			:invalid="form.name.$invalid"
		/>
		 
		<!-- sample Text Field with validations -->
	</div>
</template>
<script>
import TextField from "../../common/TextField.vue";
import GlobalRules from "~/helpers/vuelidate"; 

export default {
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
		};
	},
	methods: {
		async validate() {
			// validate function must validate this step here and return true of false
			this.form.name.$touch();
			return !this.form.name.$invalid;
		},
	},
	components: { TextField},
};
</script>
