<template>
  <div>
    <Stepper
      :title="$tr('create_item',$tr('product_study'))"
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
import ProductStep from "./ProductStep.vue";
import StudyInfoStep from "./StudyInfiStep.vue";

export default {
  name: "ProductStudyStepper",
  components: { Stepper },
  data() {
    return {
      show: false,
      steps: [
        {
          title: "Product",
          component: ProductStep,
          props: {},
          hints: [],
        },
        {
          title: "Study Info",
          component: StudyInfoStep,
          props: {},
        },
      ],
      form: {
        name: "",
        // product_id: "",
        category_id: "",
        study_language_id: "",
        study_reason: "",
      },
      validateRules: {
        form: {
          name: Validations.name100Validation,
          category_id: Validations.requiredValidation,
          // product_id: Validations.requiredValidation,
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
        // product_id: "",
        category_id: "",
        study_language_id: "",
        study_reason: "",
      };
    },

    async submit(formRef, vuelidate) {
      try {
        const productStudy = Helpers.getFormData(this.form);
        const url = "single-sales/product-study";
        const { data } = await this.$axios.post(url, productStudy);
        if (data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertedRecord = data.data;
          this.$emit("pushRecord", insertedRecord);
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
};
</script>
