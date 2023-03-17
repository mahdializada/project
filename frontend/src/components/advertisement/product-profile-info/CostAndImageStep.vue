<template>
  <div>
    <div class="h-full d-flex align-center-justify-center mt-5">
      <div class="w-full me-1">
        <CTextField
          :title="$tr('label_required', $tr('product_cost'))"
          :placeholder="$tr('product_cost')"
          class="me-md-2"
          type="number"
          min="0"
          rounded
          :rules="validateRule(form.prod_cost, $root, $tr('product_cost'))"
          :invalid="form.prod_cost.$invalid"
          v-model="form.prod_cost.$model"
        />
      </div>
      <div class="w-full ms-1">
        <CTextField
          :title="$tr('label_required', $tr('product_available_quantity'))"
          :placeholder="$tr('product_available_quantity')"
          type="number"
          class="me-md-2"
          rounded
          min="0"
          :rules="
            validateRule(
              form.prod_available_quantity,
              $root,
              $tr('product_available_quantity')
            )
          "
          :invalid="form.prod_available_quantity.$invalid"
          v-model="form.prod_available_quantity.$model"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-3">
      <div class="w-full">
        <CTextField
          :title="$tr('label_required', $tr('product_max_adver_cost'))"
          :placeholder="$tr('product_max_adver_cost')"
          type="number"
          class="me-md-2"
          rounded
          min="0"
          :rules="
            validateRule(
              form.prod_max_adver_cost,
              $root,
              $tr('product_max_adver_cost')
            )
          "
          :invalid="form.prod_max_adver_cost.$invalid"
          v-model="form.prod_max_adver_cost.$model"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-3">
      <div class="w-full">
        <CFileUploadCloud
          :key="key"
          py="py-3"
          system_name="advertisement"
          v-model="form.prod_image.$model"
          :rules="validateRule(form.prod_image, $root, $tr('product_image'))"
        />
      </div>
    </div>
  </div>
</template>
    
    <script>
import GlobalRules from "~/helpers/vuelidate";
import CheckBoxHorizontalItem from "../../new_form_components/cat_product_selection/CheckBoxHorizontalItem.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CFileUploadCloud from "../../new_form_components/Inputs/CFileUploadCloud.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      key: 0,
      validateRule: GlobalRules.validate,
      isFetchingProductProfitability: false,
      productProfitabilitys: [
        {
          id: "Profit",
          name: this.$tr("profit"),
          icon: "mdi-share-variant",
        },
        {
          id: "Medium profit",
          name: this.$tr("medium_profit"),
          icon: "mdi-share-variant",
        },
        {
          id: "Less profit",
          name: this.$tr("less_profit"),
          icon: "mdi-chart-line",
        },
        {
          id: "Less loss",
          name: this.$tr("less_loss"),
          icon: "mdi-share-variant",
        },
        {
          id: "Medium loss",
          name: this.$tr("medium_loss"),
          icon: "mdi-share-variant",
        },
        {
          id: "More loss",
          name: this.$tr("more_loss"),
          icon: "mdi-share-variant",
        },
      ],
    };
  },
  methods: {
    async validate() {
      this.form.prod_cost.$touch();
      this.form.prod_image.$touch();
      this.form.prod_max_adver_cost.$touch();
      this.form.prod_available_quantity.$touch();
      return (
        !this.form.prod_cost.$invalid &&
        !this.form.prod_image.$invalid &&
        !this.form.prod_max_adver_cost.$invalid &&
        !this.form.prod_available_quantity.$invalid
      );
    },
    async loaded() {
      this.key++;
    },
  },
  components: {
    CTextField,
    InputCard,
    CheckBoxHorizontalItem,
    CFileUploadCloud,
  },
};
</script>
    
    <style scoped>
</style>