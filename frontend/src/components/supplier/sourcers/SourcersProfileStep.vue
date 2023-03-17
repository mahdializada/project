<template>
  <div>
    <CFileUploadCloud
    :key="key"
    py="py-3"
    :prev_file="$_.cloneDeep(form.image.$model)"
    system_name="supplier"
    v-model="form.image.$model"
    :rules="validateRule(form.image, $root, $tr('profile'))"
  />
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CFileUploadCloud from "../../new_form_components/Inputs/CFileUploadCloud.vue";

export default {
  components: {  CFileUploadCloud },
  props: {
		form: Object,
	},
  data(){
    return{
        validateRule: GlobalRules.validate,
        key:0,
    }
  },
  methods: {
    async validate() {
      this.form.image.$touch();
      return !this.form.image.$invalid;
    },
    async loaded() {
      this.key++; 
    },
  },
};
</script>

<style></style>
