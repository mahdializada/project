<template>
  <Stepper
    :title="$tr('create_item',$tr('quantity_reservation_request'))"
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
import Product from "./ProductStep";
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
          title: "PRODUCT",
          component: Product,
          props: {},
          hints: [],
        },
        {
          title: "REQUEST INFO",
          skip: true,
          component: RequestInfo,
          hints: [],
        },
      ],
      form: {
        product_id: null,
        quantity: null,
        shipping_method: null,
        receiving_country_id: null,
        purchase_order_number: null,
        arrival_time: null,
        order_note: null,
      },

      validateRules: {
        form: {
          product_id: Validations.requiredValidation,
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
        product_id: null,
        quantity: null,
        shipping_method: null,
        receiving_country_id: null,
        purchase_order_number: null,
        arrival_time: null,
        order_note: null,
      };
    },
    async submit(formRef, vuelidate) {
      console.log("submit", this.form);
      vuelidate.$touch();
      try {
        const url = "single-sales/quantity/reservation";
        const { data } = await this.$axios.post(url, vuelidate.$model);
        console.log("data",data.result);
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

<style scoped></style>
