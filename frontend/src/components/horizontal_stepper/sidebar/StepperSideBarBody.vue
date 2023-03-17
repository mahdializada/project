<template>
	<div ref="stepperSidebarBody" class="sidebar-body d-flex">
		<div
			:class="`w-full position-relative ${
				steps.length <= 5
					? 'd-flex flex-column justify-space-around'
					: 'ma-auto'
			}`"
		>
			<StepperSidebarItem
				v-for="(step, i) in steps"
				:key="i"
				:ref="'step_' + i"
				:title="step.title"
				:active="active == i"
				:state="progress == i ? 'pending' : progress > i ? 'done' : ''"
				:passedOver="progress == i ? true : false"
				:hasError="invalidSteps.includes(i)"
				@click="$emit('changeTo', i)"
			/>
			<StepperSidebarItem
				ref="done"
				:title="$tr('done')"
				:hideIcon="isDone"
				:passedOver="isDone"
			/>
			<div
				class="stepper-progress"
				:style="`top: ${top}px; height: ${height}px`"
			></div>
			<div
				class="stepper-progress stepper-progress2"
				:style="`top: ${top}px; height: ${height2}px`"
			>
				<div class="stepper-rocket">
					<StepperRocket />
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import StepperSidebarItem from "./StepperSidebarItem.vue";
import StepperRocket from "./StepperRocket.vue";
export default {
	components: {
		StepperSidebarItem,
		StepperRocket,
	},
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
		invalidSteps: {
			type: Array,
		},
	},
	data() {
		return {
			top: 0,
			height: 0,
			height2: 0,
		};
	},
	mounted() {
		this.top = this.$refs["step_0"][0].$el.offsetTop + 20;
		this.height = this.$refs.done.$el.offsetTop - this.top + 20;
		this.moveRocket();
	},
	methods: {
		moveRocket() {
			setTimeout(() => {
				let offset = 46;
				if (this.isDone) {
					offset = 10;
				}
				this.height2 =
					this.$refs.stepperSidebarBody.getElementsByClassName("passed-over")[0]
						.offsetTop + offset;
			}, 0);
		},
	},
	watch: {
		progress(value) {
			this.moveRocket();
		},
	},
};
</script>
<style scoped>
.sidebar-body {
	position: absolute;
	top: 120px;
	bottom: 120px;
	left: 0px;
	right: 0px;
	overflow-y: auto;
	overflow-x: hidden;
	border-bottom: 1px solid #efefef;
}

.stepper-progress {
	width: 2px;
	background: var(--gray);
	position: absolute;
	left: 64px;
	z-index: -1;
}

.v-application--is-rtl .stepper-progress {
	left: unset;
	right: 64px;
}

.stepper-progress2 {
	background: var(--v-primary-base);
	transition: height 0.6s;
}

.stepper-rocket {
	position: absolute;
	bottom: -20px;
	left: 50%;
	transform: translateX(-50%);
	z-index: 10;
}
</style>
