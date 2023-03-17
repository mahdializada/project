<template>
  <Stepper
      :title="$tr('update_item',$tr('product'))"
      cookieName="product_insertion_stepper"
      @close="show = false"
      :show="show"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      :submit="submit"
  />
</template>

<script>
import Stepper from "../../../horizontal_stepper/Stepper.vue";
import Validations from "../../../../validations/validations";
import HandleException from "../../../../helpers/handle-exception";
import CategoryStep from "../CategoryStep";
import GeneralInfoStep from "../GeneralInfoStep";
import ImagesUpdateStep from "../updateSteps/ImagesUpdateStep";
import AttrInfoStep from "../updateSteps/AttrInfoUpdateStep.vue";


export default {
  name: "StepperComponent",
  components:{
    Stepper
  },
  data(){
    return {
      show: false,
      steps: [
     
    
        {
          title: "GENERAL INFO",
          skip: true,
          component: GeneralInfoStep,
       
        },
        {
          title: "IMAGES",
          skip: true,
          component: ImagesUpdateStep,
         
        },
        {
          title: "ATTRIBUTE INFO",
          skip: true,
          component: AttrInfoStep,
        
        },
      ],



      form: {
        is_published: 0,
        parent_sku: null,
        pcode: null,
        name: null,
        product_img: [],
        description: null,
        VAT_tax: null,
        unit: null,
        number_of_sales: null,
        category_id: null,
        supplier_id: null,
        brand_id: null,
      },



      validateRules: {
        form: {
          parent_sku: Validations.requiredValidation,
          pcode: Validations.requiredValidation,
          name: Validations.requiredValidation,
          product_img: Validations.requiredValidation,
          description: Validations.requiredValidation,
          VAT_tax: Validations.requiredValidation,
          unit: Validations.requiredValidation,
          number_of_sales: Validations.requiredValidation,
          is_published: Validations.requiredValidation,
          category_id: Validations.requiredValidation,
          supplier_id: Validations.requiredValidation,
          brand_id: Validations.requiredValidation,
        },
      },
    }
  },
  methods: {
    async submit(formRef, vuelidate) {
      vuelidate.$touch();
      if (!vuelidate.$invalid) {

      }
    },
    showDialog() {
      this.show = true;
    }
,

    onEdit(editData){
     console.log('edit product',editData);
    

 
        this.form.is_published=editData.is_published;
        this.form.parent_sku=editData.parent_sku;
        this.form.pcode=editData.pcode;
        this.form.name=editData.name;
        this.form.product_img=[editData.product_image];
        this.form.description=editData.description;
        this.form.VAT_tax=editData.VAT_tax;
        this.form.unit=editData.unit;
        this.form.number_of_sales=editData.number_of_sales;
        this.form.category_id=editData.category_id;
        this.form.supplier_id=editData.supplier_id;
        this.form.brand_id=editData.brand_id;
      
    }
  }
}
</script>

<style scoped>

</style>