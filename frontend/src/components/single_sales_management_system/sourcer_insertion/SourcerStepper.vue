<template>
  <div>
    <Stepper
      :title="$tr('sourcers')"
      cookieName="cookie_name_must_be_uniqe_across_the_app"
      @close="show = false"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      :showBack="showBack"
      @reset="reset"
      :submit="submit"
    />
  </div>
</template>
<script>  
import Stepper from "../../horizontal_stepper/Stepper.vue";
import StepperStepOne from "./StepperStepOne.vue";
import StepperStepTwo from "./StepperStepTwo.vue";
import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import Helpers from "../../../helpers/helpers";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
export default {
  name: "SourcerStepper",
  data() {
    return {
      show: false,
      showBack: true,
      countries:[],
      steps: [
        {
          title: "GENEERAL INFO",
          component: StepperStepTwo,
          props: {},
        },
      ],
      form: {
        country_id: "",
        companyIds: [],
        
      },
      
      validateRules: {
        form: {
          country_id: Validations.requiredValidation,
          companyIds: Validations.requiredValidation,
        },
      },
    };
  },

  methods: {
    reset(){
       this.form = {
        country_id: "",
        companyIds: [],
      };
    },
    async submit(formRef, vuelidate) {
      try {
        const sourcer = Helpers.getFormData(this.form);
        const url = "single-sales/sourcer";
        const { data } = await this.$axios.post(url, sourcer);
        if (data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertSourcer = data.data;
          this.$emit("pushRecord", insertSourcer);
          return true;
        } else {
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

  components: { Stepper, CTitle },
};
</script>
