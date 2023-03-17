<template>
  <div>
    <Stepper
      ref="Stepper"
      title="Columns Categories"
      cookieName="column_category"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      :submit="submit"
      :skipStep="skip"
      @close="reset(), (show = false)"
      @reset="reset()"
    />
  </div>
</template>
<script>
import Validations from "~/validations/validations";

import Stepper from "../../horizontal_stepper/Stepper.vue";
import StepperOne from "./StepperOne.vue";
import StepperTwo from "./StepperTwo.vue";

export default {
  data() {
    return {
      show: false,
      skip: [2],
      duplicateError: null,
      steps: [
        {
          id: "category",
          title: "Categories",
          component: StepperOne,
        },
        {
          id: "system",
          title: "System & SubSystem",
          component: StepperTwo,
        },
      ],
      form: {
        subsystem_ids: [],
        category_ids: [],
      },
      validateRules: {
        form: {
          subsystem_ids: Validations.requiredValidation,
          category_ids: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    reset() {
      this.form = {
        subsystem_ids: [],
        category_ids: [],
      };
    },
    delay(delayInms) {
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve(2);
        }, delayInms);
      });
    },
    async submit(formRef, vuelidate) {
      try {
        const data = await this.$axios.post("column_category", this.form);

        if (data.data.result) {
          this.reset();
          return true;
        } else if (data.data.duplicate_error) {
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr("something_went_wrong"));

          return false;
        }
      } catch (error) {
      }
    },
    showDialog() {
      this.show = !this.show;
    },
  },
  watch: {
    ["form.itemType"]: function (value) {
      if (value == "sub_category") {
        this.skip = [];
      } else {
        this.skip = [1];
      }
    },
  },
  components: { Stepper },
};
</script>
