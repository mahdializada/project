<template>
  <div>
    <Stepper title="update Attribute" cookieName="cookie_name_must_be_uniqe_across_the_app" @close="show = false"
             :show="show" :isEdit="isEdit" :steps="steps" :form="form" :validateRules="validateRules" :submit="submit"
             @reset="clearForm"/>
  </div>
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Helpers from "../../../helpers/helpers";

import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import {required} from "vuelidate/lib/validators";
import SectionStep from "./SectionStep";
import GeneralInfoStep from "./GeneralInfoStep";
import ActionCategoryStep from "./ActionCategoryStep";

export default {
  name: "updateActionStepper",
  data() {
    return {
      show: false,
      isEdit: false,
      id:null,
      steps:  [
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
    clearForm() {
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
    async submit() {

      const response = await this.$axios.put(
          "single-sales/action/" + this.id, this.form
      );
      if (response?.status === 200) {
        this.$toasterNA("green", this.$tr('successfully_updated'));
        const datas = response.data;
        this.$emit("updateRecord", datas);

        this.clearForm();

        return true;

      } else {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        return false;

      }

    },
    showDialog(items) {
      this.form = items;
      this.id = items.id;
      this.form.attachment=[];
      console.log('attachments',items.attachments)
      this.steps[2].props.attachments=items.attachments
      this.show = true;

    },
  },

  components: {Stepper},
};
</script>