<template>
	<v-form lazy-validation ref="form" @submit.prevent="submitInside">
		<div
			:class="`stepper-container background d-flex ${!show ? 'hide' : 'show'} ${
				fullScreen ? 'full-screen' : ''
			}`"
		>
			<StepperSidebar
				:key="stepperSidebarKey + sidebarKey"
				ref="StepperSidebar"
				:active="active"
				:progress="progress"
				:steps="steps"
				:isDone="isDone"
				:fullScreen="fullScreen"
				:invalidSteps="invalidSteps"
				@close="close"
				@toggleMaximize="toggleMaximize"
				@shortkey.native="theAction"
				@changeTo="changeTo"
				v-shortkey="{
					esc: ['esc'],
					up: ['arrowup'],
					down: ['arrowdown'],
					enter: ['enter'],
				}"
			/>
			<StepperBody
				ref="StepperBody"
				@next="next"
				@prev="prev"
				@skip="skip"
				@submit="submitInside"
				@close="close"
				:active="active"
				:isDone="isDone"
				:steps="steps"
				:title="title"
				:submitting="submitting"
				:validating="validating"
				:formData="$v.form"
				:form="form"
				:submitText="submitText"
				:showBack="showBack"
				:doneStepper="doneStepper"
			/>
			<StepperHint
				:items="steps[active] ? steps[active].hints : []"
				:show="showHint"
				@close="closeHint"
			/>
			<transition name="fade">
				<div class="stepper-hint-backdrop" v-if="showHint"></div>
			</transition>
		</div>
	</v-form>
</template>
<script>
import StepperSidebar from "./sidebar/StepperSidebar.vue";
import StepperBody from "./body/StepperBody.vue";
import StepperHint from "./body/StepperHint.vue";

export default {
	props: {
		submitText: {
			type: String,
			default: "submit",
		},
		skipStep: {
			type: Array,
			default: () => [],
		},
		title: {
			type: String,
			required: true,
		},
		cookieName: {
			type: String,
			required: true,
		},
		steps: {
			type: Array,
			required: true,
		},
		submit: {
			type: Function,
		},
		show: {
			type: Boolean,
			default: false,
		},
		validateRules: {
			type: Object,
		},
		form: {
			type: Object,
		},
		showBack: {
			type: Boolean,
			default: true,
		},
		sidebarKey: {
			type: Number,
			default: 0,
		},
		doneStepper: {
			type: Object,
		},
	},
	data() {
		return {
			active: 0,
			progress: 0,
			isDone: false,
			fullScreen: false,
			invalidSteps: [],
			showHint: true,
			hideAllHints: false,
			submitting: false,
			validating: false,
			stepperSidebarKey: 0,
		};
	},
	mounted() {
		if (!this.steps[0].hints || this.steps[0].hints?.length == 0) {
			this.showHint = false;
		}
	},
	methods: {
		async next(step) {
			this.validating = true;
			this.$refs.form.validate();
			let isValid = await step.validate();
			if (!isValid) {
				if (!this.invalidSteps.includes(this.active)) {
					this.invalidSteps.push(this.active);
				}
			} else if (this.active < this.steps.length && isValid) {
				this.removeItemFromInvalid(this.active);
				if (this.skipStep.includes(this.active + 1)) {
					this.active += 2;
				} else {
					this.active++;
				}
				this.callLoadedFunction();
				this.$refs.form.resetValidation();
				if (
					this.active == this.progress + 1 ||
					this.active == this.progress + 2
				) {
					if (this.active == this.progress + 1) {
						this.progress++;
					} else if (this.active == this.progress + 2) {
						this.progress += 2;
					}
					if (this.steps[this.active].hints?.length && !this.hideAllHints) {
						this.showHint = true;
					}
				}
			}
			this.validating = false;
		},
		skip() {
			if (this.active < this.steps.length) {
				this.removeItemFromInvalid(this.active);
				this.active++;
				if (this.active == this.progress + 1) {
					this.progress++;
					this.callLoadedFunction();
					if (this.steps[this.active].hints?.length && !this.hideAllHints) {
						this.showHint = true;
					}
				}
			}
		},
		prev(step) {
			if (this.skipStep.includes(this.active - 1)) {
				this.active -= 2;
				return;
			}
			if (this.active > 0) {
				this.active--;
			}
		},
		async changeTo(index) {
			if (!this.skipStep.includes(index)) {
				if (index !== this.active) {
					this.$refs.form.validate();
					if (index <= this.progress) {
						let promises = [];
						for (let i = 0; i <= index; i++) {
							if (!this.skipStep.includes(i)) {
								promises.push(await this.validateStep(i));
							}
						}
						Promise.all(promises).then((res) => {
							if (
								this.invalidSteps.length == 0 ||
								this.isIndexSmallerThanError(index)
							) {
								this.active = index;
								this.callLoadedFunction();
								this.$refs.form.resetValidation();
							}
						});
					}
				}
			}
		},
		isIndexSmallerThanError(index) {
			for (let i = 0; i < this.invalidSteps.length; i++) {
				if (index > this.invalidSteps[i]) {
					return false;
				}
			}
			return true;
		},
		removeItemFromInvalid(index) {
			let itemIndex = this.invalidSteps.findIndex((item) => item == index);
			if (itemIndex >= 0) {
				this.invalidSteps.splice(itemIndex, 1);
			}
		},
		async submitInside() {
			this.submitting = true;
			this.$refs.form.validate();
			await this.validateStep(this.steps.length - 1); // validate last step for performance
			if (this.invalidSteps.length == 0) {
				let result = await this.submit(this.$refs.form, this.$v.form);
				if (result) {
					this.active = this.steps.length;
					this.progress = this.steps.length;
					this.isDone = true;
					this.invalidSteps = [];
					this.$refs.form.resetValidation();
					this.$refs.form.reset();
					this.$v.form.$reset();
					this.setCookie(
						this.cookieName,
						JSON.stringify({ show_hints: false }),
						365
					);
				}
			}
			this.submitting = false;
		},
		close() {
			this.$emit("close");
			this.$emit("reset");
			this.active = 0;
			this.progress = 0;
			this.isDone = false;
			this.showHint = true;
			this.invalidSteps = [];
			this.$refs.form.resetValidation();
			this.$refs.form.reset();
			this.$v.form.$reset();
		},
		async validateStep(index) {
			let refs = this.$refs.StepperBody.$refs;
			let res = await refs[`step_${index}`][0].validate();
			if (!res) {
				if (!this.invalidSteps.includes(index)) {
					this.invalidSteps.push(index);
				}
			} else {
				this.removeItemFromInvalid(index);
			}
			return res;
		},
		toggleMaximize() {
			this.fullScreen = !this.fullScreen;
		},
		theAction(event) {
			let action = event.srcKey;
			let refs = this.$refs.StepperBody.$refs;
			switch (action) {
				case "esc":
					this.close();
					break;
				case "up":
					this.prev(refs[`step_${this.active}`][0]);
					break;
				case "down":
					if (this.active < this.steps.length - 1) {
						this.next(refs[`step_${this.active}`][0]);
					}
					break;
				case "enter":
					if (this.active < this.steps.length - 1) {
						this.next(refs[`step_${this.active}`][0]);
					} else if (this.active == this.steps.length - 1) {
						this.submitInside();
					} else {
						this.close();
					}
					break;
			}
		},
		closeHint() {
			this.showHint = false;
		},
		callLoadedFunction() {
			let step_refs = this.$refs.StepperBody.$refs;
			if (step_refs[`step_${this.active}`][0].loaded) {
				step_refs[`step_${this.active}`][0].loaded(); // call loaded function
			}
		},
		setCookie(cname, cvalue, exdays) {
			const d = new Date();
			d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
			let expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		},
		getCookie(cname) {
			let name = cname + "=";
			let decodedCookie = decodeURIComponent(document.cookie);
			let ca = decodedCookie.split(";");
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == " ") {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		},
		renderSidebar() {
			this.progress = 0;
			this.stepperSidebarKey++;
		},
	},
	watch: {
		show(value) {
			if (value) {
				this.callLoadedFunction();
			}
			if (!this.steps[0].hints || this.steps[0].hints?.length == 0) {
				this.showHint = false;
			}
			let cookie = this.getCookie(this.cookieName);
			if (cookie) {
				cookie = JSON.parse(cookie);
				this.hideAllHints = true;
				this.showHint = false;
			}
		},
	},
	components: {
		StepperSidebar,
		StepperBody,
		StepperHint,
	},
	validations() {
		return this.validateRules;
	},
};
</script>

<style scoped>
.stepper-container {
	z-index: 10000;
	width: 1304px;
	height: 100%;
	position: fixed;
	right: 0;
	bottom: 0;
	border-radius: 30px 0 0 30px;
	transition: all 0.5s;
}
.v-application--is-rtl .stepper-container {
	right: unset;
	left: 0;
}
.stepper-container.hide {
	transform: translateX(110%);
}
.v-application--is-rtl .stepper-container.hide {
	transform: translateX(-110%);
}
@media only screen and (max-width: 1304px) {
	.stepper-container {
		width: 100%;
	}
}
.stepper-container.full-screen {
	width: 100% !important;
}
.stepper-hint-backdrop {
	position: absolute;
	top: 0;
	right: 0;
	left: 0;
	bottom: 0;
	z-index: 99;
	background: rgba(0, 0, 0, 0.4);
	border-radius: 30px 0 0 30px;
	transition: all 0.4s;
}
.v-application--is-rtl .stepper-hint-backdrop {
	border-radius: 0 30px 30px 0;
}
</style>
