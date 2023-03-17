<template>
  <div>
    <Stepper
      :title="$tr('update_item',$tr('product_study'))"
      cookieName="create_product_study"
      @close="show = false"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      @reset="resetForm"
      :submit="submit"
    />
  </div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Helpers from "../../../helpers/helpers";
import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import StudyInfoStep from "./StudyInfiStep.vue";

export default {
  name: "ProductStudyStepper",
  components: { Stepper },
  data() {
    return {
      show: false,
      steps: [
        {
          title: "Study Info",
          component: StudyInfoStep,
          props: {},
        },
      ],
      form: {
        name: "",
        study_language_id: "",
        study_reason: "",
      },
      validateRules: {
        form: {
          name: Validations.name100Validation,
          study_language_id: Validations.requiredValidation,
          study_reason: Validations.requiredValidation,
        },
      },
    };
  },

  methods: {
    resetForm() {
      this.form = {
        name: "",
        study_language_id: "",
        study_reason: "",
      };
    },

    async submit(formRef, vuelidate) {
      const response = await this.$axios.put(
        "single-sales/product-study/" + 1,
        this.form
      );
      if (response?.status === 200) {
        this.$toasterNA("green", this.$tr('successfully_updated'));
        const datas = response.data;
        this.$emit("updateRecord", datas);

        this.resetForm();

        return true;
      } else {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        return false;
      }
    },
    showDialog(items) {
      if (items && Array.isArray(items)) {
        this.form = items[0];
      }
      this.show = true;
    },
  },
};
</script>
