<template>
	<v-list-group v-if="item.items" color="primary">
		<template v-slot:activator>
			<v-list-item-icon class="j-menu-icon" v-html="item.icon">
			</v-list-item-icon>
			<v-list-item-title v-text="$tr(item.key)"></v-list-item-title>
		</template>
		<v-list class="ps-2">
			<JumptoMenuItem v-for="(item2, k) in item.items" :key="k" :item="item2" />
		</v-list>
	</v-list-group>
</template>
<script>
import JumptoMenuItem from "./JumptoMenuItem.vue";
export default {
	props: { item: Object },
	components: { JumptoMenuItem },
};
</script>
