<template>
  <div>
    <Stepper
      :title="$tr('create_item', $tr('landing_page'))"
      cookieName="cookie_name_must_be_uniqe_across_the_app"
      @close="show = false"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      @reset="reset"
      :submit="submit"
    />
  </div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import GeneralInfoStep from "./GeneralInfoStep.vue";
import ProductInfoStep from "./ProductInfoStep.vue";
import ProductImageStep from "./ProductImageStep.vue";
import Validations from "../../../validations/validations";
import { requiredIf } from "vuelidate/lib/validators";

export default {
  name: "ActionStepper",
  data() {
    return {
      show: false,
      steps: [
        {
          title: "General Info",
          component: GeneralInfoStep,
          props: {},
        },
        {
          title: "Product Info",
          component: ProductInfoStep,
          props: {},
        },
        {
          title: "Product Image",
          component: ProductImageStep,
          props: {},
        },
      ],
      form: {
        country_id: "",
        company_id: "",
        page_Type: "",
        template_id: [],
        landingPage_image: [],
        landing_language: "",
        landingPage_image2: [],
        product_Type: "",
        discount_type: "",
        product_code: "",
        product_title_ar: "",
        product_title_en: "",
        product_note_ar: "",
        product_note_en: "",
        tranfer_link: "",
        transfar_message_en: "",
        transfar_message_ar: "",
        discount_amount: "",
        language_type: "",
        product_collection_items: [],
        landing_detail: [
          {
            id: "",
            price: "",
            quantity: "",
          },
        ],
      },
      validateRules: {
        form: {
          country_id: Validations.requiredValidation,
          company_id: Validations.requiredValidation,
          page_Type: Validations.requiredValidation,
          template_id: Validations.requiredValidation,
          landingPage_image: Validations.imageValidation,
          landing_language: Validations.name100Validation,
          product_Type: Validations.name100Validation,
          discount_type: Validations.name100Validation,
          landingPage_image2: Validations.imageValidation,
          product_code: Validations.name100Validation,
          language_type: "",
          product_title_ar: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "Arabic"
                ? Validations.name100Validation
                : "";
            }),
          },
          product_title_en: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "English"
                ? Validations.name100Validation
                : "";
            }),
          },
          product_note_ar: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "Arabic"
                ? Validations.name100Validation
                : "";
            }),
          },
          product_note_en: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "English"
                ? Validations.name100Validation
                : "";
            }),
          },
          transfar_message_ar: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "Arabic"
                ? Validations.name100Validation
                : "";
            }),
          },
          transfar_message_en: {
            required: requiredIf((value) => {
              return this.form.language_type == "Both" ||
                this.form.language_type == "English"
                ? Validations.name100Validation
                : "";
            }),
          },
          tranfer_link: Validations.urlValidationRequired,
          discount_amount: Validations.numberValidation,
          product_collection_items: {
            required: requiredIf((value) => {
              return this.form.product_Type == "Collection"
                ? Validations.requiredValidation
                : "";
            }),
          },
          landing_detail: {
            $each: {
              id: {},
              quantity: {
                required: requiredIf((value) => {
                  return this.form.product_Type == "Collection"
                    ? Validations.numberValidation
                    : "";
                }),
              },
              price: {
                required: requiredIf((value) => {
                  return this.form.product_Type == "Collection"
                    ? Validations.numberValidation
                    : "";
                }),
              },
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
        page_Type: "",
        template_id: [],
        landingPage_image: [],
        landing_language: "",
        landingPage_image2: [],
        product_Type: "",
        discount_type: "",
        product_code: "",
        product_title_ar: "",
        product_title_en: "",
        product_note_ar: "",
        product_note_en: "",
        tranfer_link: "",
        transfar_message_en: "",
        transfar_message_ar: "",
        discount_amount: "",
        language_type: "",
        product_collection_items: [],
        landing_detail: [
          {
            id: "",
            price: "",
            quantity: "",
          },
        ],
      };
    },
    async submit(formRef, vuelidate) {
      console.log("submit", this.form);
      return true;
    },
    showDialog() {
      this.show = true;
    },
  },

  components: { Stepper },
};
</script> 
