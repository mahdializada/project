<template>
	<div
		:class="`cursor-pointer stepper-sidebar-item mb-5 text-uppercase rounded font-weight-medium d-flex position-relative ${state} ${
			active ? 'active' : ''
		} ${passedOver ? 'passed-over' : ''} ${hasError ? 'has-error' : ''} `"
		@click="$emit('click')"
	>
		<div class="stepper-sidebar-item-before">
			<div
				v-if="hasError"
				class="step-icon err-icon d-flex align-center justify-center stepper-icon"
			>
				<div class="step-icon-inner error d-flex align-center justify-center">
					<v-icon dark size="14">mdi-close</v-icon>
				</div>
			</div>
			<div
				v-else-if="state !== 'done' && !hideIcon"
				class="icon stepper-icon"
			></div>
			<div
				v-else-if="state == 'done' && !hideIcon"
				class="step-icon done-icon d-flex align-center justify-center stepper-icon"
			>
				<div class="step-icon-inner primary d-flex align-center justify-center">
					<v-icon dark size="14">mdi-check-bold</v-icon>
				</div>
			</div>
		</div>
		<div class="stepper-sidebar-item-text">{{ $tr(title) }}</div>
	</div>
</template>
<script>
export default {
	props: {
		state: {
			type: String,
			default: "",
		},
		title: {
			type: String,
			required: true,
		},
		active: {
			type: Boolean,
			default: false,
		},
		hideIcon: {
			type: Boolean,
			default: false,
		},
		passedOver: {
			type: Boolean,
			default: false,
		},
		hasError: {
			type: Boolean,
			default: false,
		},
	},
};
</script>
<style scoped>
.stepper-sidebar-item {
	padding: 10px;
	margin: 0 20px;
	color: #777;
	font-size: 14px !important;
	transition: all 0.4s;
}
.stepper-sidebar-item.active {
	background: #2e7be60f !important;
	color: var(--v-primary-base);
}
.stepper-sidebar-item.has-error {
	color: var(--v-error-base);
}
.stepper-sidebar-item::after {
	transition: all 0.4s;
	content: "";
	display: block;
	opacity: 0;
}

.stepper-sidebar-item.active::after {
	opacity: 1;
	background: var(--v-primary-base);
	height: 24px;
	width: 8px;
	border-radius: 4px;
	position: absolute;
	top: 50%;
	right: -24px;
	transform: translateY(-50%);
}
.v-application--is-rtl .stepper-sidebar-item.active::after {
	right: unset;
	left: -24px;
}
.stepper-sidebar-item-before {
	width: 70px;
	position: relative;
}
.stepper-sidebar-item-before .icon {
	height: 26px;
	width: 26px;
	border-radius: 50%;
	background: var(--v-background-base);
	border: 5px solid var(--gray);
}
.stepper-icon {
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	transition: all 0.4s;
}
.stepper-sidebar-item.pending .stepper-sidebar-item-before .icon {
	background: var(--v-primary-base);
	border: 8px solid var(--v-background-base);
	box-shadow: 0px 2px 10px -1px rgb(85 85 85 / 8%),
		0px 1px 10px 0px rgb(85 85 85 / 6%), 0px 1px 30px 0px rgb(85 85 85 / 3%) !important;
}
.stepper-sidebar-item.done .stepper-sidebar-item-before .step-icon,
.stepper-sidebar-item.has-error .stepper-sidebar-item-before .step-icon {
	height: 30px !important;
	width: 30px !important;
	border-radius: 50% !important;
	padding: 4px !important;
}
.stepper-sidebar-item.done .stepper-sidebar-item-before .done-icon {
	border: 1px solid var(--v-primary-base) !important;
}
.stepper-sidebar-item.has-error .stepper-sidebar-item-before .err-icon {
	border: 1px solid var(--v-error-base) !important;
}
.step-icon-inner {
	border-radius: 50% !important;
	width: 100% !important;
	height: 100% !important;
}
.stepper-sidebar-item-text {
	max-width: 168px;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
}
</style>
