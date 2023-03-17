<template>
	<div>
		<NuxtLink :to="`/files/personal/drive/${$route.params.pathMatch}/kdkdk`">
			{{ child_test }} : {{ $route.params.pathMatch }}
		</NuxtLink>
		<br />
		{{ test }}
	</div>
</template>
<script>
export default {
	props: {
		test: {
			type: Number,
		},
	},
	data() {
		return {
			child_test: Math.random() * 100000,
		};
	},
	created() {
	},
};
</script>
