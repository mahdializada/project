<template>
	<v-btn class="stepper-btn" fab dark x-small @click="$emit('click')">
		<v-icon class="primary--text" dark size="16px"> {{ icon }} </v-icon>
	</v-btn>
</template>
<script>
export default {
	props: {
		icon: {
			type: String,
			default: "",
		},
	},
};
</script>
<style scoped>
.stepper-btn {
	height: 26px !important;
	width: 26px !important;
	background: #2e7be622 !important;
	margin: 2px;
}
</style>
