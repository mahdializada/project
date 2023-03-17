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
        @onStartOver="startOver"
        :isSubmitting="is_submitting"
      >
        <template v-slot:step1>
          <div class="d-flex justify-center align-center flex-sm-wrap">
            <v-card
              width="240px"
              height="280px"
              class="d-flex flex-column justify-space-between mx-2 mt-2"
              v-for="(category, index) in categories"
              :key="index"
            >
              <span>
                <div class="pt-3 pb-2">
                  <img
                    :src="category.icon"
                    alt=""
                    height="120px"
                    class="d-flex mx-auto"
                  />
                </div>
                <p class="mb-0 text-center text-h5 font-weight-medium">
                  {{ $tr(category.name) }}
                </p>
              </span>

              <v-btn
                class="category-btn primary mx-2 mb-2"
                @click="selectCategory(category.name)"
              >
                <v-icon left x-small> fa-plus</v-icon>
                {{ $tr("create") }}
              </v-btn>
            </v-card>
          </div>
        </template>
        <template v-slot:step2>
          <GeneralStepper
            ref="GeneralStepper"
            :category_type.sync="category_type"
            :store_url="store_url"
            :api_url="api_url"
          />
        </template>

        <template v-slot:step3>
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
import GeneralStepper from "./GeneralStepper.vue";

export default {
  components: {
    CustomStepper,
    DoneMessage,
    Dialog,
    TextButton,
    CustomInput,
    GeneralStepper,
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
      const result = await this.$refs.GeneralStepper.submit();
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
      if (currentStep == 1) {
        if (this.category_type != "") {
          this.$refs.customStepper?.forceNext();
        } else {
          // this.$toastr.e(this.$tr("please_select_category_or_subcategory"));
				this.$toasterNA("red", this.$tr('please_select_category_or_subcategory'));

        }
      }
    },

    changeToValidate(changeTo) {
      let isValid = false;
      if (changeTo == 2) {
        isValid = this.category_type != "" ? true : false;
      }
      if (changeTo == 3) {
        isValid = this.category_type != "" ? true : false;
      }

      if (isValid) {
        this.$refs.customStepper.changeTo(changeTo);
      }
    },
    startOver() {
      if (this.type != "sub") {
        this.category_type = "";
      }
      this.$refs.GeneralStepper.startOver();
    },
  },
  created() {
    if (this.type == "sub") {
      this.steppers = [
        { icon: "fa-lock", title: "general", slotName: "step2" },
        { icon: "fa-thumbs-up", title: "done", slotName: "step3" },
      ];

      this.category_type = "sub_category";
    }
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