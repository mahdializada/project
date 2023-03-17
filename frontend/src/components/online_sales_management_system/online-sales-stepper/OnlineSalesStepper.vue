<template>
  <stepper
    :title="$tr('online_sales')"
    cookieName="online_sales"
    @close="show = false"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="validateRules"
    @reset="reset"
    :submit="submit"
  />
</template>
    
    <script>
import Validations from "~/validations/validations";
import Stepper from "../../horizontal_stepper/Stepper.vue";
import ProductStep from "./ProductStep.vue";
import InfoStep from "./InfoStep.vue";

export default {
  components: { Stepper },
  data() {
    return {
      show: false,
      steps: [
        {
          title: "general_info_caps",
          component: InfoStep,
          props: {},
          hints: [],
        },
        {
          title: "item_sales",
          component: ProductStep,
          props: {},
          hints: [],
        },
      ],
      form: {
        country_id: "",
        company_id: "",
        project_id: "",
        sales_type: "Landing Page Sales",
        products: [
          {
            product_name: "",
            product_name_arabic: "",
            product_code: "",
            isDefaultProductCode: false,
          },
        ],
      },
      validateRules: {
        form: {
          country_id: Validations.requiredValidation,
          company_id: Validations.requiredValidation,
          project_id: Validations.requiredValidation,
          sales_type: Validations.requiredValidation,
          products: {
            $each: {
              product_name: Validations.name100Validation,
              product_name_arabic: Validations.name100Validation,
              product_code: Validations.name100Validation,
              isDefaultProductCode: "",
            },
          },
        },
      },
    };
  },

  methods: {
    reset() {
      this.form = {
        country_id: "",
        company_id: "",
        project_id: "",
        sales_type: "Landing Page Sales",
        products: [
          {
            product_name: "",
            product_name_arabic: "",
            product_code: "",
            isDefaultProductCode: false,
          },
        ],
      };
    },
    async submit() {
      try {
        let find = this.form.products.map((i) => i.product_code);
        var isExist = false;
        let findDuplicates = (find) =>
          find.filter((item, index) => find.indexOf(item) != index);
        findDuplicates(find).forEach((element) => {
          isExist = true;
          this.$toasterNA(
            "red",
            this.$tr("item_already_exists", this.$tr(element))
          );
        });
        if (isExist) return;
        let res = await this.$axios.post("online-sales", this.form);
        if (res.status == 201) {
          this.$emit("pushRecord");
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          return true;
        }
        if (res.status == 422) {
          this.$toasterNA(
            "red",
            this.$tr("item_already_exists", this.$tr("item_sales_code"))
          );
        }
      } catch (error) {
        const errors = error.response.data.errors;
        for (let error in errors) {
          for (let er in errors[error]) {
            this.$toasterNA(
              "red",
              this.$tr(
                "item_already_exists",
                this.$tr(
                  this.form.products[errors[error][er]?.charAt(13)]
                    ?.product_code
                )
              )
            );
          }
        }
      }
    },
    showDialog() {
      this.show = true;
    },
  },
};
</script>
    
    <style scoped>
</style>
    