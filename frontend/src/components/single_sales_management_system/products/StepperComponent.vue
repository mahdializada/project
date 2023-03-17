<template>
  <Stepper
    :title="$tr('create_item', $tr('product'))"
    cookieName="product_insertion_stepper"
    @close="close"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="validateRules"
    :submit="submit"
    @reset="reset"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "../../../validations/validations";
import CategoryStep from "./CategoryStep";
import SupplierStep from "./SupplierStep";
import GeneralInfoStep from "./GeneralInfoStep";
import ImagesStep from "./ImagesStep";
import InventoryStepVue from "./InventoryStep.vue";
import CountryStep from "./CountryStep.vue";
import { requiredIf } from "vuelidate/lib/validators";
export default {
  name: "StepperComponent",
  components: {
    Stepper,
  },
  data() {
    return {
      show: false,
      submited: false,
      steps: [
        {
					id: "Country",
          title: this.$tr("country"),
          component: CountryStep,
        },
        {
          title: this.$tr("categroy"),
          component: CategoryStep,
          props: {},
        },
        {
          title: this.$tr("supplier"),
          component: SupplierStep,
        },
        {
          title: this.$tr("general_info_caps"),
          component: GeneralInfoStep,
        },
      ],

      form: {
        products:[
          {
            name: null,
            arabic_name: null,
            description: null,
            arabic_description: null,
            brand_id: null,
            pcode: null,
            parent_sku: null,
            isDefaultProductCode:false,
            isDefaultSKU:false,
            quantity: "",
            cost_per_unit: "",
            images: [],
            text_value: {
              id:'',
              value:''
            },
            file_value:  {
              id:'',
              value:''
            },
            number_value:  {
              id:'',
              value:''
            },
            link_value:  {
              id:'',
              value:''
            },
            attribute: ''
          }
        ],
        country_id: "",
        companyIds: [],
        category_id: null,
        supplier_id: null,
        supplier_name: '',
        
        
      },

      validateRules: {
        form: {
          country_id: Validations.requiredValidation,
          companyIds: Validations.requiredValidation,
          images: Validations.requiredValidation,
          category_id: Validations.requiredValidation,
          supplier_id: Validations.requiredValidation,
          supplier_name:'',
					
          products:{
            $each:{
              parent_sku: Validations.numberValidationNew,
              name: Validations.requiredValidation,
              images: Validations.requiredValidation,
              description: Validations.requiredValidation,
              quantity: Validations.requiredValidation,
              cost_per_unit: Validations.requiredValidation,
              brand_id: Validations.requiredValidation,
              pcode: Validations.requiredValidation,
              images: Validations.requiredValidation,
              isDefaultProductCode:'',
              isDefaultSKU:'',
              text_value: {
                  id:'',
                  value:''
                },
              file_value:  {
                  id:'',
                  value:''
                },
              link_value:  {
                  id:'',
                  value:''
                },
              number_value:  {
                  id:'',
                  value:''
                },
              attribute: '',
              arabic_name: {
                required: requiredIf((value) => {
                  return value.arabic_name;
                }),
              },
              arabic_description: {
                required: requiredIf((value) => {
                  return value.arabic_description;
                }),
              },
            },
          },
        },
      },
    };
  },
  watch: {
    show(value) {
      if (value) {
        this.submited = false;
      }
    },
  },
  methods: {
    async submit(formRef, vuelidate) {
      try {
          let res = await this.$axios.post(
            "single-sales/products-ssp",
            this.form
          );
				  form.attribute = form.attribute.map((attr) => attr.id);

          this.submited = true;
          if (res.data.result) {
            this.$emit("pushRecord", res.data.product);
            this.reset();
            return true;
          } else {
            return false;
          }
      } catch (e) {}
    },
    showDialog() {
      this.show = true;
    },
    close() {
      this.show = false;
      if (this.submited == false) {
        for (let i = 0; i < this.form?.images?.length; i++) {
          this.deleteFileBackend(this.form.images[i]);
        }
      }
    },
    async deleteFileBackend(item) {
      if (item && item.source) {
        item.source.cancel("file-cancled");
        delete item.source;
      }
      await this.$axios.post("product-management/pr-products/images", {
        file_path: item.path,
      });
    },
    reset() {
      this.form = {
        country_id: "",
        companyIds: [],
        products:[
          {
            parent_sku: null,
            name: null,
            arabic_name: null,
            description: null,
            arabic_description: null,
            brand_id: null,
            pcode: null,
            isDefaultProductCode:false,
            isDefaultSKU:false,
            quantity: "",
            cost_per_unit: "",
            images: [],
            attributes: [
              {
                attribute_id: "",
                value: [],
              },
            ],

          }
        ],
        // images: [],
        supplier_name: '',

        category_id: null,
        supplier_id: null,
        // inventroy: [
        //   {
        //     sku: "",
        //     quantity: "",
        //     cost_per_unit: "",
        //     attributes: [
        //       {
        //         attribute_id: "",
        //         value: [],
        //       },
        //     ],
        //   },
        // ],
      };
    },
  },
};
</script>
