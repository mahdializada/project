<template>
	<v-list-group
		:value="value"
		:class="`${firstLevel ? 'first-level' : ''} ${small ? 'small' : ''}`"
		:append-icon="firstLevel ? 'mdi-plus-thick' : 'mdi-chevron-down'"
	>
		<template v-slot:activator>
			<v-list-item-icon v-html="item.icon"> </v-list-item-icon>
			<v-list-item-title>{{ $tr(item.key) }}</v-list-item-title>
		</template>
		<div
			:class="`left-border BaseLight ${
				small && firstLevel ? 'small-navbar' : ''
			}`"
		>
			<slot></slot>
		</div>
	</v-list-group>
</template>
<script>
export default {
	data() {
		return {
			value: false,
		};
	},
	props: {
		item: Object,
		small: Boolean,
		firstLevel: Boolean,
	},
};
</script>
