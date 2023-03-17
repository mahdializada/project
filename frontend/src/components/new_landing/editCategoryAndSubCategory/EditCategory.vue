<template>
  <v-form lazy-validation ref="categoryForm">
    <Dialog @closeDialog="closeDialog">
      <CustomStepper
        @onNext="changeStepper"
        @onChangeTo="onChangeTo"
        :headers="steppers"
        @close="closeDialog"
        :canNext="false"
        :is-submitting="isLoading"
        @submit="submitForm"
        ref="customStepper"
        @validate="validateStepper"
        @changeToValidate="changeToValidate"
        class="pa-0"
        :isSubmitting="is_submitting"
      >
        <template v-slot:step1>
          <EditGeneralStepper
            ref="EditGeneralStepper"
            :category_type.sync="category_type"
            :store_url="store_url"
            :api_url="api_url"
          />
        </template>

        <template v-slot:step2>
          <DoneMessage
            :title="$tr('item_all_set', $tr('label'))"
            :subTitle="$tr('you_can_access_your_item', $tr('label'))"
          />
        </template>
      </CustomStepper>
    </Dialog>
  </v-form>
</template>

<script>
import GlobleRules from "~/helpers/vuelidate";
import DoneMessage from "../../design/components/DoneMessage.vue";
import CustomStepper from "../../design/FormStepper/CustomStepper.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import TextButton from "../../common/buttons/TextButton.vue";
import CustomInput from "../../design/components/CustomInput.vue";
import EditGeneralStepper from "./EditGeneralStepper.vue";

export default {
  components: {
    CustomStepper,
    DoneMessage,
    Dialog,
    TextButton,
    CustomInput,
    EditGeneralStepper,
  },
  props: {
    type: { type: String, default: "main" },
    store_url: { type: String, required: true },
    api_url: { type: String, required: true },
  },
  data() {
    return {
      isLoading: false,
      categories: [
        { name: "category", icon: "/new_landing/category-icon.svg" },
        { name: "sub_category", icon: "/new_landing/subcategory-icon.svg" },
      ],

      steppers: [
        { icon: "fa-lock", title: "category_area", slotName: "step1" },
        { icon: "fa-lock", title: "general", slotName: "step2" },
        { icon: "fa-thumbs-up", title: "done", slotName: "step3" },
      ],

      category_type: "",
      is_submitting: false,
    };
  },
  methods: {
    closeDialog() {
      this.$emit("closeDialog");
    },

    async submitForm() {
      this.is_submitting = true;
      const result = await this.$refs.EditGeneralStepper.submit();
      this.is_submitting = false;
      if (result) {
        this.$refs.customStepper.setCurrent(this.steppers.length);
      }
    },
    selectCategory(category_name) {
      this.category_type = category_name;
      this.$refs.customStepper.next();
    },

    changeStepper(step) {
      switch (step) {
        case 2:
          break;
        case 3:
          break;
        default:
          break;
      }
    },
    onChangeTo(step) {
    },

    validateStepper(currentStep) {
      this.$refs.customStepper?.forceNext();
    },

    changeToValidate(changeTo) {
      let isValid = true;
      if (isValid) {
        this.$refs.customStepper.changeTo(changeTo);
      }
    },
  },
  created() {},
};
</script>

<style>
.category-btn {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
</style>