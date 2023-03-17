<template>
	<div class="sidebar position-relative elevation-10">
		<StepperSidebarTop
			:fullScreen="fullScreen"
			@close="$emit('close')"
			@toggleMaximize="$emit('toggleMaximize')"
		/>
		<StepperSideBarBody
			ref="StepperSideBarBody"
			:active="active"
			:progress="progress"
			:steps="steps"
			:isDone="isDone"
			:invalidSteps="invalidSteps"
			@changeTo="
				(index) => {
					$emit('changeTo', index);
				}
			"
		/>
		<StepperSidebarBottom />
	</div>
</template>
<script>
import StepperSidebarTop from "./StepperSidebarTop.vue";
import StepperSideBarBody from "./StepperSideBarBody.vue";
import StepperSidebarBottom from "./StepperSidebarBottom.vue";
export default {
	props: {
		steps: {
			type: Array,
		},
		active: {
			type: Number,
		},
		progress: {
			type: Number,
		},
		isDone: {
			type: Boolean,
		},
		fullScreen: {
			type: Boolean,
		},
		invalidSteps: {
			type: Array,
		},
	},
	methods: {
		moveRocket() {
			this.$refs.StepperSideBarBody.moveRocket();
		},
	},
	components: {
		StepperSidebarTop,
		StepperSideBarBody,
		StepperSidebarBottom,
	},
};
</script>
<style scoped>
.sidebar {
	min-width: 300px;
	background-image: url(/stepper/stepper-background-svg.svg);
	background-size: cover;
	background-position: center;
	height: 100%;
	border-radius: 30px 0 0 30px;
	position: relative;
	z-index: 1;
}
.v-application--is-rtl .sidebar {
	border-radius: 0 30px 30px 0;
}
</style>
