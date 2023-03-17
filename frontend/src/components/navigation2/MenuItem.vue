<template>
	<v-list-item
		:value="item.key"
		v-if="$isInScope(item.scope) || item.showForAll"
		link
		:to="item.link"
		:exact="item.exact"
	>
		<v-list-item-icon v-html="item.icon"> </v-list-item-icon>
		<v-list-item-title>{{ $tr(item.key) }}</v-list-item-title>
	</v-list-item>
</template>
<script>
export default {
	props: {
		item: Object,
	},
};
</script>
