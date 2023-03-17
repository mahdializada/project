<template>
  <Stepper
    ref="hello"
    :title="$tr('create_new') + ' ' + $tr('product_list')"
    cookieName="create_product_list"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="productRules"
    :submit="validateForm"
    @close="onClose"
    @reset="reset"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import ProductListStep from "./ProductListStep.vue";
import Validations from "~/validations/validations";

export default {
  components: { Stepper },

  data() {
    return {
      show: false,
      steps: [
        {
          title: this.$tr("product_list"),
          component: ProductListStep,
        },
      ],
      isSubmitting: false,
      form: {
        isEdit: false,
        prev_image: [],
        products: [
          {
            product_name: "",
            product_code: null,
            product_image: [],
          },
        ],
      },

      productRules: {
        form: {
          isEdit: {},
          prev_image: {},
          products: {
            $each: {
              product_name: Validations.name100Validation,
              product_code: Validations.requiredValidation,
              product_image: Validations.requiredValidation,
            },
          },
        },
      },
    };
  },

  methods: {
    reset() {
      this.form = {
        isEdit: false,
        prev_image: [],
        products: [
          {
            id: 1,
            product_name: "",
            product_code: null,
            product_image: [],
          },
        ],
      };
    },
    onClose() {
      this.show = false;
      this.reset();
    },
    showDialog(item) {
      this.show = !this.show;
      if (item != null) {
        this.form = {
          isEdit: true,
          prev_image: this.$_.cloneDeep(item.attachments),
          products: [
            {
              id: item.id,
              product_name: item.product_name,
              product_code: item.product_code,
              product_image: item.attachments,
            },
          ],
        };
      }
    },

    async validateForm() {
      let data = {
        products: this.form.products,
      };
      try {
        if (this.form.isEdit) {
          console.log(data.products[0].product_image);
          delete this.form.prev_image;
          let id = this.form.products[0].id;
          const result = await this.$axios.put(
            `product_list/${id}`,
            data.products[0]
          );
          if (result?.status == 200) {
            this.$emit("updateItem", result?.data?.data);
            this.$toasterNA("green", this.$tr("successfully_updated"));
            this.form.isEdit = false;
            return true;
          }
        } else {
          const response = await this.$axios.post(`product_list`, data);
          if (response.status == 201) {
            this.$emit("addNewItem", response?.data?.data);
            this.$toasterNA("green", this.$tr("successfully_inserted"));
            return true;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style></style>
