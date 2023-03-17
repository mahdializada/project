<template>
  <v-form lazy-validation ref="skillForm">
    <Dialog @closeDialog="closeDialog">
      <CustomStepper
        :headers="steppers"
        @close="closeDialog"
        :canNext="false"
        :is-submitting="isLoading"
        @submit="submitForm"
        ref="customStepper"
        class="pa-0"
        @validate="validateStepper"
        @changeToValidate="validateStepper"
      >
        <template v-slot:step1>
          <FilterCategory
            url="landing/all-skill-categories"
            sub_url="landing/skill-sub-categories"
            ref="filterCategoryRef"
          />
        </template>
        <template v-slot:step2>
          <div>
            <CustomInput
              v-model="skill.name"
              :label="$tr('label_required', $tr('skill_name'))"
              :placeholder="$tr('skill_name')"
              type="textfield"
              class="me-md-4 mb-md-2 mb-0"
              :error-messages="skillNameErrMsg"
              @change="checkNameValidation"
              @blur="checkNameValidation"
              @keypress="checkNameValidation"
            />
            <CustomInput
              v-model="skill.description"
              :label="$tr('label_required', $tr('description'))"
              :placeholder="$tr('description')"
              type="textarea"
              class="me-md-4 mb-md-2 mb-0"
              :error-messages="descriptionErrMsg"
              @change="checkDescValidation"
              @blur="checkDescValidation"
              @keypress="checkDescValidation"
            />
          </div>
        </template>
        <template v-slot:step3>
          <DoneMessage
            :title="$tr('item_all_set', $tr('skill'))"
            :subTitle="$tr('you_can_access_your_item', $tr('skill'))"
          />
        </template>
      </CustomStepper>
    </Dialog>
  </v-form>
</template>

<script>
import DoneMessage from "../../design/components/DoneMessage.vue";
import CustomStepper from "../../design/FormStepper/CustomStepper.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import TextButton from "../../common/buttons/TextButton.vue";
import GeneralStepper from "../registerCategoryAndSubCategory/GeneralStepper.vue";
import FilterCategory from "../common/FilterCategory.vue";
import CustomInput from "../../design/components/CustomInput.vue";
import helpers from "../../../helpers/helpers";
import handleException from "../../../helpers/handle-exception";
export default {
  components: {
    CustomStepper,
    DoneMessage,
    Dialog,
    TextButton,
    GeneralStepper,
    FilterCategory,
    CustomInput,
  },
  data() {
    return {
      isLoading: false,
      steppers: [
        {
          icon: "fas fa-sort-amount-down",
          title: "general",
          slotName: "step1",
        },
        {
          icon: "mdi-lock",
          title: "work_area",
          slotName: "step2",
        },
        { icon: "fa-thumbs-up", title: "done", slotName: "step3" },
      ],
      skill: {
        name: null,
        description: null,
      },
      skillNameErrMsg: "",
      descriptionErrMsg: "",
    };
  },
  methods: {
    closeDialog() {
      this.$emit("closeDialog");
    },
    filterCategoryRef() {
      return this.$refs?.filterCategoryRef;
    },
    prepareForm() {
      return helpers.getFormData({
        ...this.skill,
        sub_category_id: this.filterCategoryRef()?.selectedSubCategory?.id,
      });
    },
    async submitForm() {
      if (this.validateForm()) {
        const formData = this.prepareForm();
        try {
          this.isLoading = true;
          const response = await this.$axios.post("landing/skills", formData);
          if (response?.data?.result) {
            this.isLoading = false;
            this.clearForm();
          }
        } catch (error) {
          this.isLoading = false;
          handleException.handleApiException(this, error);
        }
        this.forceNext();
      }
    },
    clearForm() {
      this.filterCategoryRef()?.clearForm();
      this.skill = {
        name: null,
        description: null,
      };
    },
    validateForm() {
      return (
        this.filterCategoryRef()?.checkValidation() && this.checkValidation()
      );
    },
    checkNameValidation() {
      // validate name
      const skillNameRequired = this.$tr(
        "item_is_required",
        this.$tr("skill_name")
      );
      this.skillNameErrMsg = this.skill.name ? "" : skillNameRequired;
    },
    checkDescValidation() {
      // validate description
      const skillDescRequired = this.$tr(
        "item_is_required",
        this.$tr("description")
      );
      this.descriptionErrMsg = this.skill.description ? "" : skillDescRequired;
    },
    checkValidation() {
      this.checkNameValidation();
      this.checkDescValidation();
      // check final result
      let isValid =
        (this.skill.name ? true : false) &&
        (this.skill.description ? true : false);
      return isValid;
    },
    forceNext() {
      this.$refs.customStepper?.forceNext();
    },
    validateStepper() {
      if (this.filterCategoryRef()?.checkValidation()) {
        this.forceNext();
      }
    },
  },
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
