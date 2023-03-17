<template>
  <stepper
    ref="productProfileInfoRefs"
    :title="$tr('product_profile_info')"
    cookieName="create_product_profile_info"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="formRules"
    :submit="validateForm"
    @close="onClose"
    @reset="reset"
  />
</template>

<script>
import ProductInfoStep from "./ProductInfoStep.vue";
import CostAndImageStep from "./CostAndImageStep.vue";
import Validations from "~/validations/validations";
import Stepper from "../../horizontal_stepper/Stepper.vue";

export default {
  components: { Stepper },

  data() {
    return {
      show: false,
      steps: [
        {
          title: this.$tr("product_info"),
          component: ProductInfoStep,
        },
        {
          title: this.$tr("cost_image"),
          component: CostAndImageStep,
        },
      ],
      form: {
        item_code: "",
        prod_source: "",
        prod_sale_goal: "",
        prod_style: "",
        prod_section: "",
        prod_new_product_source: [],
        prod_work_type: "",
        prod_import_source: [],
        prod_production_type: [],

        prod_cost: null,
        prod_available_quantity: null,
        prod_max_adver_cost: null,
        prod_image: [],
      },

      formRules: {
        form: {
          item_code: Validations.nameValidation,
          prod_source: Validations.nameValidation,
          prod_sale_goal: Validations.nameValidation,
          prod_style: Validations.nameValidation,
          prod_section: Validations.nameValidation,
          prod_new_product_source: Validations.requiredValidation,
          prod_work_type: Validations.nameValidation,
          prod_import_source: Validations.requiredValidation,
          prod_production_type: Validations.requiredValidation,
          prod_cost: Validations.decimalValidation,
          prod_available_quantity: Validations.numberValidation,
          prod_max_adver_cost: Validations.decimalValidation,
          prod_image: Validations.requiredValidation,
        },
      },
    };
  },

  methods: {
    reset() {
      this.form = {
        item_code: "",
        prod_source: "",
        prod_sale_goal: "",
        prod_style: "",
        prod_section: "",
        prod_new_product_source: [],
        prod_work_type: "",
        prod_import_source: [],
        prod_production_type: [],
        prod_cost: null,
        prod_available_quantity: null,
        prod_max_adver_cost: null,
        prod_image: [],
      };
    },
    onClose() {
      this.show = false;
      this.reset();
    },
    openDialog(item) {
      console.log("item code", item);
      this.form.item_code = item;
      this.show = true;
    },

    async validateForm() {
      try {
        const response = await this.$axios.post(
          "advertisement/product-profile-info",
          this.form
        );
        if (response.status == 201) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          this.$emit("addProductProfile",response.data);
          return true;
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
  },
};
</script>

<style></style>
