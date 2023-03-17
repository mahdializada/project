<template>
	<div class="topTitle mb-3">
		<h1
			class="text-center text-h4 font-weight-bold border-bottom pb-1 pt-1 text-uppercase"
			style="color: #58595b"
		>
			{{ $tr(title) }}
		</h1>
	</div>
</template>

<script>
export default {
	name: "HeaderTitle",
	props: {
		title: {
			required: true,
			type: String,
		},
	},
};
</script>

<style></style>
