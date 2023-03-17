<template>
	<div class="section w-full position-relative">
		<div
			:class="`section-title background primary--text ${
				active == 0 ? '' : 'hide'
			}`"
		>
			{{ title }}
		</div>
		<div class="section-content">
			<div
				:class="`section-item px-5 py-4 ${i < active ? 'before' : ''} ${
					i > active ? 'after' : ''
				}`"
				v-for="(step, i) in steps"
				:key="i"
			>
				<client-only>
					<component
						:ref="'step_' + i"
						:is="step.component"
						v-bind="step.props"
						:form="formData"
						:data="form"
					/>
				</client-only>
			</div>
			<div
				:class="`section-item px-5 py-4 ${
					steps.length == active ? '' : 'after'
				}`"
			>
				<component
					v-if="doneStepper"
					:ref="'doneStep'"
					:is="doneStepper.component"
					v-bind="doneStepper.props"
				/>
				<StepperDone v-else />
			</div>
		</div>
		<div
			class="section-footer py-3 px-5 d-flex justify-space-between background"
		>
			<div>
				<v-btn
					v-if="showBack && !isDone && active != 0"
					rounded
					color="primary"
					outlined
					dark
					@click="
						() => {
							submitting || validating ? null : back();
						}
					"
				>
					<v-icon left dark>
						{{ `mdi-chevron-${$vuetify.rtl ? "right" : "left"}` }}
					</v-icon>
					{{ $tr("back") }}
				</v-btn>
			</div>
			<div>
				<v-btn
					v-if="
						active < steps.length - 1 && (steps[active].skip ? true : false)
					"
					rounded
					color="primary"
					text
					dark
					@click="
						() => {
							submitting || validating ? null : $emit('skip');
						}
					"
				>
					{{ $tr("skip") }}
				</v-btn>
				<v-btn
					v-if="active < steps.length - 1"
					:loading="validating"
					rounded
					color="primary"
					dark
					@click="
						() => {
							submitting || validating ? null : next();
						}
					"
				>
					{{ $tr("next") }}
					<v-icon right dark>
						{{ `mdi-chevron-${$vuetify.rtl ? "left" : "right"}` }}
					</v-icon>
				</v-btn>
				<v-btn
					v-if="active == steps.length - 1"
					:loading="submitting || validating"
					rounded
					color="primary"
					dark
					@click="() => (submitting || validating ? null : $emit('submit'))"
				>
					{{ $tr(submitText) }}
				</v-btn>
				<v-btn
					v-if="isDone"
					rounded
					color="primary"
					dark
					@click="
						() => {
							submitting || validating ? null : $emit('close');
						}
					"
				>
					{{ $tr("done") }}
				</v-btn>
			</div>
		</div>
	</div>
</template>
<script>
import StepperDone from "./StepperDone.vue";

export default {
	props: {
		title: {
			type: String,
			required: true,
		},
		submitText: {
			type: String,
			default: "submit",
		},
		steps: {
			type: Array,
		},
		active: {
			type: Number,
		},
		isDone: {
			type: Boolean,
		},
		submitting: {
			type: Boolean,
		},
		validating: {
			type: Boolean,
		},
		formData: {
			type: Object,
		},
		form: {
			type: Object,
		},
		showBack: {
			type: Boolean,
		},
		doneStepper: {
			type: Object,
		},
	},

	methods: {
		next() {
			if (this.$refs[`step_${this.active}`][0].callInsideNext) {
				this.$refs[`step_${this.active}`][0].next();
				return;
			}
			this.$emit("next", this.$refs[`step_${this.active}`][0]);
		},
		back() {
			if (this.$refs[`step_${this.active}`][0].callInsidePrev) {
				this.$refs[`step_${this.active}`][0].prev();
				return;
			}
			this.$emit("prev", this.$refs[`step_${this.active}`][0]);
		},
	},
	components: { StepperDone },
};
</script>
<style scoped>
.section-content {
	background-color: #f7f8fc;
	background-image: url(/stepper/bottom-left.svg), url(/stepper/top-right.svg);
	background-position: left bottom, right top;
	position: absolute;
	top: 0;
	right: 0;
	left: 0;
	bottom: 84px;
	overflow-y: clip;
}
.theme--dark .section-content {
	background-color: var(--v-surface-base) !important;
}
.section-footer {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
}
.section-item {
	position: absolute;
	top: 0;
	right: 0;
	left: 0;
	bottom: 0;
	height: 100%;
	width: 100%;
	transform: translateY(0%);
	transition: all 0.4s;
	overflow: auto;
}
.section-item.before {
	transform: translateY(-100%);
}
.section-item.after {
	transform: translateY(100%);
}
.section-title {
	position: absolute;
	top: 0;
	left: 50%;
	transform: translate(-50%, 0);
	z-index: 1;
	font-size: 18px;
	font-weight: 600;
	padding: 12px 60px !important;
	border-radius: 0 0 20px 20px;
	transition: all 0.5s;
}
.section-title.hide {
	transform: translate(-50%, -100%);
}
</style>
