<template>
	<v-list-item
		link
		:to="item.link"
		:exact="item.exact"
		v-if="$isInScope(item.scope) || item.showForAll"
	>
		<v-list-item-icon class="j-menu-icon" v-html="item.icon"></v-list-item-icon>
		<v-list-item-content>
			<v-list-item-title v-text="$tr(item.key)"></v-list-item-title>
		</v-list-item-content>
	</v-list-item>
</template>
<script>
export default {
	props: {
		item: Object,
	},
};
</script>
