<template>
  <v-container>
    <v-row dense>
      <v-col>
        <CFileUpload v-model="form.product_img.$model"  
         :rules="validateRule(form.product_img, $root, $tr('image'))"
         :attachments="form.product_img.$model" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CFileUpload from "../../../new_form_components/Inputs/CFileUpload.vue";
export default {
    props: {
    form: Object,
  },
  name: "ImagesStep",
  components:{
    CFileUpload
  },
  data(){
    return {
      validateRule: GlobalRules.validate,
      media:[],
    }
  },
  methods:{
    validate() {
      // validate function must validate this step here and return true of false
      return  true;
    },
  }
}
</script>

<style scoped>

</style>