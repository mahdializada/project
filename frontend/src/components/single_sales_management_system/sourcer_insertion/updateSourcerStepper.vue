<template>
  <div>
    <Stepper
      :title="$tr('update_item',$tr('sourcing_request'))"
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
import StepperStepTwo from "./StepperStepTwo.vue";
import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import Helpers from "../../../helpers/helpers";
export default {
  name: "SourcerStepper",
  data() {
    return {
      show: false,
      // category_id: "",
      steps: [
       
        {
          title: "GENEERAL INFO",
          component: StepperStepTwo,
          props: {},
        },
      ],
      form: {
  
        sourcing_type: "",
        importing_country_id: "",
        receiving_country_id: "",
        approx_quantity: "",
        target_cost: "",
        required_shipping_method: "",
        reason_for_search: "",
      },
      validateRules: {
        form: {
          sourcing_type: Validations.name100Validation,
          importing_country_id: Validations.name100Validation,
          receiving_country_id: Validations.name100Validation,
          approx_quantity: Validations.name100Validation,
          target_cost: Validations.name100Validation,
          required_shipping_method: Validations.name100Validation,
          reason_for_search: Validations.requiredValidation,
        },
      },
    };
  },

  methods: {
    reset(){
       this.form = {
    
        product_id: "",
        sourcing_type: "",
        importing_country_id: "",
        receiving_country_id: "",
        approx_quantity: "",
        target_cost: "",
        required_shipping_method: "",
        reason_for_search: "",
      };
    },
    async submit(formRef, vuelidate) {
     
      const response = await this.$axios.put(
          "single-sales/sourcer/"+1, this.form
        );
        if (response?.status === 200) {
          this.$toasterNA("green", this.$tr('successfully_updated'));
          const datas = response.data;
          this.$emit("updateRecord", datas);
       
          this.reset();

          return true;

        } else {
				this.$toasterNA("red", this.$tr('something_went_wrong'));

          return false;
          // this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
     
    },
    showDialog(items) {
        console.log("update item sources",items)
        if (items && Array.isArray(items)) {
        this.form = items[0];
      }
      this.show = true;
      
    },
  },

  components: { Stepper },
};
</script>
