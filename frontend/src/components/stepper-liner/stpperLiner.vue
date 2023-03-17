<template>
  <v-form lazy-validation ref="form" @submit.prevent="submitInside">
    <v-dialog v-model="show" width="800" persistent :key="key">
      <div class="white position-relative">
        <StepperHeaderLiner
          ref="StepperHeader"
          :activeStepper="active"
          @onClose="close"
          :steppers="steps"
          :showTitle="false"
          :title="title"
        ></StepperHeaderLiner>
        <div style="min-height: 550px !important">
          <StepperBodyLiner
            ref="StepperBody"
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
          ></StepperBodyLiner>
        </div>
        <div class="section-footer py-2 px-5 d-flex justify-space-between">
          <div>
            <v-btn
              v-if="showBack && !isDone && active != 0"
              color="primary"
              outlined
              dark
              @click="
                () => {
                  submitting || validating ? null : prev();
                }
              "
            >
              <v-icon left dark>
                {{ `mdi-chevron-${$vuetify.rtl ? "right" : "left"}` }}
              </v-icon>
              {{ $tr("back") }}
            </v-btn>

            <v-btn
              v-if="isDone && showStartOver"
              color="primary"
              dark
              @click="close"
            >
              {{ $tr("close") }}
            </v-btn>
          </div>
          <div>
            <v-btn
              v-if="
                active < steps.length - 1 && (steps[active].skip ? true : false)
              "
              color="primary"
              text
              dark
              @click="
                () => {
                  submitting || validating ? null : skip();
                }
              "
            >
              {{ $tr("skip") }}
            </v-btn>
            <v-btn
              v-if="active < steps.length - 1"
              :loading="validating"
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
              color="primary"
              dark
              @click="submitInside"
            >
              {{ $tr(submitText) }}
            </v-btn>
            <v-btn
              v-if="isDone && showStartOver"
              color="primary"
              dark
              @click="tryMore"
            >
              {{ $tr("start_over") }}
              <v-icon right small>fa-redo</v-icon>
            </v-btn>
            <v-btn v-else-if="isDone" color="primary" dark @click="close">
              {{ $tr("done") }}
            </v-btn>
          </div>
        </div>
      </div>
    </v-dialog>
  </v-form>
</template>

<script>
import StepperBodyLiner from "./StepperBodyLiner.vue";
import StepperHeaderLiner from "./StepperHeaderLiner.vue";

export default {
  name: "stpperLiner",
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
    submit: {
      type: Function,
    },
    showStartOver: {
      type: Boolean,
      default: false,
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
      key: 0,
    };
  },
  watch: {
    show: function (value) {
      if (value) this.key++;
    },
  },
  methods: {
    tryMore() {
      (this.active = 0), (this.progress = 0), (this.isDone = false), this.key++;
      this.$refs.StepperHeader.reset();
      this.$emit("startOver");
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
    async next() {
      let step = this.$refs.StepperBody.$refs[`step_${this.active}`][0];

      this.validating = true;
      this.$refs.form.validate();
      let isValid = await step.validate();
      if (!isValid) {
        if (!this.invalidSteps.includes(this.active)) {
          this.invalidSteps.push(this.active);
        }
      } else if (this.active < this.steps.length && isValid) {
        this.$refs.StepperHeader.nextStep();
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
    callLoadedFunction() {
      let step_refs = this.$refs.StepperBody.$refs;
      if (step_refs[`step_${this.active}`][0].loaded) {
        step_refs[`step_${this.active}`][0].loaded(); // call loaded function
      }
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
    prev() {
      let step = this.$refs.StepperBody.$refs[`step_${this.active}`][0];
      if (this.skipStep.includes(this.active - 1)) {
        this.$refs.StepperHeader.prevStep();
        this.$refs.StepperHeader.prevStep();
        this.active -= 2;
        return;
      }
      if (this.active > 0) {
        this.$refs.StepperHeader.prevStep();

        this.active--;
      }
    },
    removeItemFromInvalid(index) {
      let itemIndex = this.invalidSteps.findIndex((item) => item == index);
      if (itemIndex >= 0) {
        this.invalidSteps.splice(itemIndex, 1);
      }
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
    async submitInside() {
      this.submitting = true;
      this.$refs.form.validate();
      await this.validateStep(this.steps.length - 1); // validate last step for performance
      if (this.invalidSteps.length == 0) {
        let result = await this.submit(this.$refs.form, this.$v.form);
        if (result) {
          this.active = this.steps.length;
          this.progress = this.steps.length;
          this.$refs.StepperHeader.nextStep();
          this.isDone = true;
          this.invalidSteps = [];
          this.$refs.form.resetValidation();
          this.$refs.form.reset();
          this.$v.form.$reset();
        }
      }
      this.submitting = false;
    },
    skip() {},

    // back() {
    // 	console.log("back");
    // },
  },

  components: { StepperHeaderLiner, StepperBodyLiner },
  validations() {
    return this.validateRules;
  },
};
</script>

<style>
.section-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  /* background-color: aquamarine !important; */
  background-color: white;
  z-index: 20000;
}
</style>
