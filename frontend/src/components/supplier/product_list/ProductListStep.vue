<template>
  <div>
    <MultiInputItem
      :items="form.products.$each.$iter"
      @addMore="addMore"
      @removeItem="removeItem"
      :title="'product_list'"
    >
      <template v-slot:content="{ item }">
        <div class="d-flex flex-column flex-md-row">
          <CTextField
            py="py-3"
            v-model="item.product_name.$model"
            @blur="item.product_name.$touch()"
            :rules="validateRule(item.product_name, $root, $tr('product_name'))"
            px="pe-1"
            :invalid="item.product_name.$invalid"
            :title="$tr('label_required', $tr('product_name'))"
            :placeholder="$tr('product_name')"
            prepend-inner-icon="fa fa-atom"
          />
          <CTextField
            py="py-3"
            v-model="item.product_code.$model"
            @blur="item.product_code.$touch()"
            :rules="validateRule(item.product_code, $root, $tr('product_code'))"
            px="ps-1"
            :invalid="item.product_code.$invalid"
            :title="$tr('label_required', $tr('product_code'))"
            :placeholder="$tr('product_code')"
            prepend-inner-icon="fa fa-atom"
          />
        </div>
        <div class="d-flex flex-column flex-md-row">
          <CFileUploadCloud
            :key="i"
            py="py-3"
            :="form.isEdit.$model"
            system_name="supplier-management"
            :prev_file="$_.cloneDeep(form.prev_image.$model)"
            v-model="item.product_image.$model"
            :rules="validateRule(item.product_image, $root, $tr('attachment'))"
          />
        </div>
      </template>
    </MultiInputItem>
  </div>
</template>

<script>
import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import GlobalRules from "~/helpers/vuelidate";
import CFileUploadCloud from "../../new_form_components/Inputs/CFileUploadCloud.vue";

export default {
  components: { MultiInputItem, CTextField, CFileUploadCloud },
  props: {
    form: Object,
  },
  data() {
    return {
      i: 0,
      validateRule: GlobalRules.validate,
    };
  },
  methods: {
    async validate() {
      this.form.products.$touch();
      return !this.form.products.$invalid;
    },
    async loaded() {
      this.i++;
    },
    addMore() {
      this.form.products.$model.push({
        product_name: "",
        product_code: null,
        product_image: [],
      });
    },
    removeItem(item) {
      if (this.form.products.$model.length > 1)
        this.form.products.$model.splice(item, 1);
    },
  },
};
</script>

<style>
</style>