<template>
	<v-card
		elevation="2"
		v-bind="$attrs"
		v-on="$listeners"
		class="position-relative"
	>
		<v-card-title class="pa-0">
			<v-spacer />
			<DialogCloseBtn class="close-dialog-btn" @click="closeDialog" />
		</v-card-title>
		<slot />
	</v-card>
</template>
<script>
import DialogCloseBtn from "./CloseBtn.vue";
export default {
	components: {
		DialogCloseBtn,
	},
	methods: {
		closeDialog() {
			this.$emit("closeDialog");
		},
	},
};
</script>

<style scoped>
.close-dialog-btn {
	position: absolute;
	top: 10px;
	right: 10px;
	z-index: 400000000000;
}
.v-application--is-rtl .close-dialog-btn {
	right: unset;
	left: 10px;
}
</style>
