<template>
	<h2 class="form-step-title text-center my-4" v-bind="$attrs" v-on="$listeners">
		{{ $tr(text) }}
	</h2>
</template>
<script>
export default {
	props: {
		text: String,
	},
};
</script>
<style scoped>
.form-step-title {
	color: #555;
	font-size: 22px;
	font-weight: 600;
}
</style>
