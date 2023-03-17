<template>
  <Stepper
    :title="$tr('update_item',$tr('quantity_reservation_request'))"
    cookieName="cookie_name_must_be_uniqe_across_the_app"
    @close="show = false"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="validateRules"
    @reset="resetForm"
    :submit="submit"
  />
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "../../../validations/validations";
import RequestInfo from "./RequestInfo";
import HandleException from "../../../helpers/handle-exception";

export default {
  name: "StepperComponent",
  components: { Stepper },
  data() {
    return {
      show: false,
      category_id: "",
      steps: [
     
        {
          title: "REQUEST INFO",
          skip: true,
          component: RequestInfo,
          hints: [],
        },
      ],
      form: {
  
        quantity: null,
        shipping_method: null,
        receiving_country_id: null,
        purchase_order_number: null,
        arrival_time: null,
        order_note: null,
      },

      validateRules: {
        form: {

          quantity: Validations.requiredValidation,
          shipping_method: Validations.requiredValidation,
          receiving_country_id: Validations.requiredValidation,
          purchase_order_number: Validations.requiredValidation,
          order_note: Validations.requiredValidation,
          arrival_time: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    resetForm() {
      this.form = {
        quantity: null,
        shipping_method: null,
        receiving_country_id: null,
        purchase_order_number: null,
        arrival_time: null,
        order_note: null,
      };
    },
     
    async submit(formRef, vuelidate) {
      vuelidate.$touch();

        const url = "single-sales/quantity/reservation/"+1;
        const  data  = await this.$axios.put(url, vuelidate.$model);
          if (data?.status === 200) {
          this.$toasterNA("green", this.$tr('successfully_updated'));
          const datas = data.data;
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
      this.form=items[0];
      this.show = true;
    },
  },
};
</script>

<style scoped></style>
