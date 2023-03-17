<template>
	<div class="w-full h-full d-flex align-center-justify-center mt-5">
		<div class="w-full h-full">hello</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";

export default {
	data() {
		return {
			validateRule: GlobalRules.validate,
		};
	},
	methods: {
		async loaded() {},
		async validate() {
			return true;
		},
	},
};
</script>
