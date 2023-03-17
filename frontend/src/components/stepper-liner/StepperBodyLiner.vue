<template>
	<div class="steeper-body-liner">
		<div v-for="(step, i) in steps" :key="i">
			<client-only>
				<component
					v-show="i == active"
					:ref="'step_' + i"
					:is="step.component"
					v-bind="step.props"
					:form="formData"
					:data="form"
				/>
			</client-only>
		</div>

		<div
			:class="`section-item px-5 py-4 ${steps.length == active ? '' : 'after'}`"
			v-show="steps.length == active"
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
</template>
<script>
import StepperDone from "./StepperDone.vue";
export default {
	components: {
		StepperDone,
	},
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
			default: () => {},
		},
	},
};
</script>
<style>
.steeper-body-liner {
	min-height: 200px;
}
</style>
