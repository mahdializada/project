<template>
	<div class="sidebar-top">
		<StepperSideBarTopBtn icon="mdi-close" @click="$emit('close')" />
		<StepperSideBarTopBtn
			:icon="fullScreen ? 'mdi-dock-window' : 'mdi-checkbox-blank-outline'"
			@click="$emit('toggleMaximize')"
		/>
	</div>
</template>
<script>
import StepperSideBarTopBtn from "./StepperSideBarTopBtn.vue";
export default {
	props: {
		fullScreen: Boolean,
	},
	components: {
		StepperSideBarTopBtn,
	},
};
</script>
<style scoped>
.sidebar-top {
	position: absolute;
	top: 16px;
	left: 16px;
	right: 16px;
}
</style>
