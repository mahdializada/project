<template>
	<v-breadcrumbs :items="items" class="ps-0 pb-0 pa-0" divider="›">
		<template v-slot:item="{ item }">
			<v-breadcrumbs-item
				:to="item.to"
				class="breadcrumb-item text--primary text--primary text-uppercase breadcrumb-link"
				nuxt
				:exact="item.exact"
			>
				{{ $tr(item.text) }} 
			</v-breadcrumbs-item>
		</template>
	</v-breadcrumbs>
</template>
<script>
export default {
	props: {
		items: {
			type: Array,
		},
	},
};
</script>
<style scoped>
.breadcrumb-link {
	font-size: 12px;
	font-weight: 500;
	transition: color 0.3s;
}
.v-application .text--primary.breadcrumb-link:hover {
	color: var(--v-primary-base) !important;
	text-decoration: underline;
}
</style>
