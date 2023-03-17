<template>
  <Stepper
    :title="title"
    cookieName="brand_insertion_stepper"
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
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "~/validations/validations";

import BrandInfoStep from "./BrandInfoStep.vue";
import Helpers from "../../../helpers/helpers";
import HandleException from "../../../helpers/handle-exception";

export default {
  name: "CategoryStepper",
  components: { Stepper },
  data() {
    return {
      show: false,
      steps: [
        {
          title: "Brand info",
          component: BrandInfoStep,
          props: {},
          hints: []
        }
      ],
      id:'',
      title:'Brand Insertion',
      form: {
        brands: [
          {
            logo: "",
            name: "",
            arabic_name: "",
          }
        ]
      },
      validateRules: {
        form: {
          brands: {
            $each: {
              name: Validations.name100Validation,
              arabic_name: Validations.name100Validation,
              logo: Validations.imageValidation
            }
          }
        }
      }
    };
  },

  methods: {
    reset() {
      this.form.brands = [
        {
          logo: "",
          name: "",
          arabic_name: "",
        }
      ];
    },

    async submit(formRef, vuelidate) {
      let brands = this.form.brands;
      const toBase64 = file =>
        new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = () => resolve(reader.result);
          reader.onerror = error => reject(error);
        });
      const tempBrands = [];
      for (let index = 0; index < brands.length; index++) {
        const item = brands[index];
        const tempItem = {
          logo: await toBase64(item.logo),
          name: item.name
        };
        tempBrands.push(tempItem);
      }
      try {
        const url = "single-sales/brand-ssp";

        let formData = new FormData();
        
        brands.forEach(brand => {
          formData.append("brands[]", brand.name);
          formData.append("arabic_name[]", brand.arabic_name);
          formData.append("logos[]", brand.logo);
        });

        // formData.append("brands", JSON.stringify(brands));
        if(this.form.id){
          formData.append('id',this.form.id);
          console.log(this.form);

        }
        const data = await this.$axios.post(url, formData);
        if (data.data.result) {
          if(this.form.id)
          {
            this.$toasterNA("green", this.$tr("successfully_updated"));
            this.$emit("updateRecord", data?.data?.brand);
          }
          else{
            this.$toasterNA("green", this.$tr("successfully_inserted"));
            this.$emit("pushRecord", data?.data?.brand);
          }
          return true;

        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
          this.$toasterNA("red", this.$tr("something_went_wrong"));

          return false;
        }
      } catch (error) {
        console.log("error:", error);
        HandleException.handleApiException(this, error);
      }
    },

    showDialog() {
      this.show = true;
      if(this.form.id){
        delete this.form['id'];
      }
      // console.log(this.form, this.validateRules);
    },
    showEditDialog(parsedItems) {
      console.log("item:",parsedItems);
      this.show = true;
      this.form.brands[0].name=parsedItems.name;
      this.form.brands[0].arabic_name=parsedItems.arabic_name;
      this.form.id=parsedItems.id;
      this.id=parsedItems.id;
      this.title="Update Brand"
    },
  }
};
</script>

<style scoped>
</style>
