<template>
	<div class="d-flex pa-3">
		<div class="stepper-title">
			{{ $tr(title || "title") }}
		</div>
		<div class="steps px-2">
			<div class="stepper_container">
				<div class="progress_holder">
					<div class="progress_line" :style="{ width: progress + 'px' }"></div>
				</div>

				<div class="step" v-for="(step, index) in steppers" :key="index">
					<div
						class="circle"
						:class="` ${
							index < doneStepper && index != activeStepper
								? 'done_stepper'
								: ''
						} ${index == activeStepper ? 'active_stepper' : ''}`"
					>
						<div class="check_holder"></div>
						<v-icon class="position-absolute" size="17">mdi-check</v-icon>
					</div>
					<span
						:class="index == activeStepper ? 'active_text' : ''"
						v-if="showTitle"
						>{{ step.title }}</span
					>
				</div>
				<div class="step">
					<div
						class="circle done-step"
						:class="`  ${
							steppers.length == activeStepper ? 'active_stepper' : ''
						}`"
					>
						<div class="check_holder"></div>
						<v-icon class="position-absolute" size="17">mdi-check</v-icon>
					</div>
					<span
						:class="steppers.length == activeStepper ? 'active_text' : ''"
						v-if="showTitle"
						>{{ step.title }}</span
					>
				</div>
			</div>
		</div>
		<div style="width: 8%" class="d-flex justify-end">
			<v-icon
				color="grey"
				size="35"
				v-text="'mdi-close-circle-outline'"
				@click="$emit('onClose')"
			></v-icon>
		</div>
	</div>
</template>

<script>
export default {
	name: "StepperHeaderLiner",
	props: ["steppers", "title", "showTitle"],
	data() {
		return {
			activeStepper: 0,
			doneStepper: 0,
			progress: 0,
			progressWidth: 0,
		};
	},

	created() {
		this.$nextTick(function () {
			this.resizeHandler();
		});
	},

	mounted() {
		this.progressWidth = document.querySelectorAll(".step")[2].offsetLeft;

		this.progress = (this.progressWidth - 40) / 2 + 10;
	},
	watch: {},
	methods: {
		resizeHandler() {
			this.progressWidth = document.querySelectorAll(".step")[1].offsetLeft;

			if (this.activeStepper > 0) {
				if (this.activeStepper == this.steppers.length - 1) {
					this.progress =
						(((this.progressWidth - 40) / 2) * 2 + 40) * this.activeStepper;
				} else {
					this.progress =
						(((this.progressWidth - 40) / 2) * 2 + 40) * this.activeStepper +
						((this.progressWidth - 40) / 2 + 40);
				}
			} else {
				this.progress =
					((this.progressWidth - 40) / 2 + 40) * (this.activeStepper + 1);
			}
		},

		nextStep() {
			this.activeStepper += 1;

			if (this.doneStepper > this.activeStepper) {
				return;
			} else {
				this.doneStepper += 1;
			}

			if (this.activeStepper > this.steppers.length - 1) {
				this.progress += this.progressWidth / 2;
				return;
			} else {
				if (this.activeStepper <= this.steppers.length) {
					this.progress += this.progressWidth;
				} else {
					this.progress += this.progressWidth / 2 + 20;
				}
			}
		},
		reset() {
			this.progress = (this.progressWidth - 40) / 2 + 40;
			this.activeStepper = 0;
			this.doneStepper = 0;
		},
		prevStep() {
			const progressLine = document.querySelector(".progress_line").offsetWidth;
			if (this.activeStepper <= 0) {
				return;
			} else {
				this.activeStepper -= 1;
			}

			if (this.progress <= (this.progressWidth - 40) / 2 + 40) {
				return;
			} else if (this.doneStepper != this.activeStepper - 1) {
				return;
			} else if (
				this.progress < progressLine &&
				this.progress > (this.progressWidth - 40) / 2 + 40
			) {
				this.progress -= this.progressWidth;
			} else {
				this.progress -= this.progressWidth / 2 + 20;
			}
		},
	},
};
</script>

<style scoped>
.stepper-title {
	width: 25%;
	max-width: 168px;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	font-weight: bold;
	color: rgb(160, 153, 153);
}
.stepper-liner h3 {
	color: grey;
}
.steps {
	width: 67%;
}
.stepper_container {
	width: 100%;
	display: flex;
	justify-content: space-between;
	position: relative;
}

.circle {
	width: 27px;
	height: 27px;
	background-color: white;
	border-radius: 20px;
	border: 3px solid rgb(203, 203, 203);
	display: flex;
	justify-content: center;
	align-items: center;
}

.circle div {
	width: 4px;
	height: 4px;
	background-color: white;
	border-radius: 20px;
	transition: transform 0.2s ease-in-out;
}

.step i {
	/* width: ; */
	color: white;
	display: none;
}

.step {
	z-index: 10;
	position: relative;
}

.step:nth-child(2) {
	margin-left: 0 !important;
}

.step:last-child {
	margin-right: 0;
}

.progress_holder {
	position: absolute;
	width: 100%;
	height: 3px;
	background-color: rgb(203, 203, 203);
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}

.progress_line {
	position: absolute;
	height: 2px;
	background-color: var(--v-primary-base);
	top: 50%;
	left: 0;
	transform: translateY(-50%);
	transition: width 0.2s ease-out;
}

.step span {
	position: absolute;
	left: 50%;
	transform: translateX(-50%);
	color: rgb(203, 203, 203);
	width: 80px;
	text-align: center;
}

.active_stepper {
	border-color: var(--v-primary-base);
}

.active_stepper div {
	transition: width 0.2s ease-out;

	transform: scale(4);
	background-color: var(--v-primary-base);
}

.done_stepper {
	border-color: var(--v-primary-base);
}

.done_stepper i {
	display: block;
	transform: scale(1);
}

.done_stepper div {
	transform: scale(4.3, 4.3);

	background-color: var(--v-primary-base);
}

.active_text {
	color: var(--v-primary-base) !important;
	font-weight: bold;
}
</style>
