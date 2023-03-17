<template>
  <div>
    <Stepper
      :title="$tr('create_item',$tr('action'))"
      cookieName="cookie_name_must_be_uniqe_across_the_app"
      @close="show = false"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      @reset="reset"
      :submit="submit"
    />
  </div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import SectionStep from "./SectionStep.vue";
import Helpers from "../../../helpers/helpers";
import GeneralInfoStep from "./GeneralInfoStep.vue";
import ActionCategoryStep from "./ActionCategoryStep.vue";
import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";

export default {
  name: "ActionStepper",
  data() {
    return {
      show: false,
      steps: [
        {
          title: "Section",
          component: SectionStep,
          props: {},
          hints: [],
        },
        {
          title: "General Info",
          component: GeneralInfoStep,
          props: {},
        },
        {
          title: "Action Category",
          component: ActionCategoryStep,
          props: {},
        },
      ],
      form: {
        type: "",
        dependable_id: "",
        goals: [],
        priority: "",
        action_category: "",
        statements: [],
        attachment: [],
        value: "",
      },
      validateRules: {
        form: {
          type: Validations.name100Validation,
          dependable_id: Validations.name100Validation,
          goals: Validations.requiredValidation,
          priority: Validations.name100Validation,
          action_category: Validations.name100Validation,
          statements: Validations.requiredValidation,
          attachment: Validations.requiredValidation,
          value: Validations.name100Validation,
        },
      },
    };
  },

  methods: {
    reset() {
      this.form = {
        type: "",
        dependable_id: "",
        goals: [],
        priority: "",
        action_category: "",
        statements: [],
        attachment: [],
        value: "",
      };
    },
    async submit(formRef, vuelidate) {
      try {
        const action = Helpers.getFormData(this.form);
        const url = "single-sales/action";
        const { data } = await this.$axios.post(url, action);
        if (data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertaction = data.action;
          this.$emit("pushRecord", insertaction);
          return true;
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

          return false;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    showDialog() {
      this.show = true;
    },
  },

  components: { Stepper },
};
</script>
