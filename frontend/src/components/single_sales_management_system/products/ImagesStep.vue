<template>
  <div>
    <CTitle :text="`images`" />
    <div class="w-full h-full d-flex align-center justify-center">
      <div class="w-full h-full">
        <CFileUploadCloud2
          system_name="single-sales"
          v-model="form.images.$model"
          :form="form"
          ref="uploadRefs"
        ></CFileUploadCloud2>
      </div>
    </div>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CFileItemsImage from "../../new_form_components/Inputs/CFileItemsImage.vue";
import CFileUploadCloud2 from "../../new_form_components/Inputs/CFileUploadCloud2.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
export default {
  name: "ImagesStep",
  components: {
    CFileItemsImage,
    CFileUploadCloud2,
    CTitle,
  },
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
    };
  },
  methods: {
    validate() {
      this.form.images.$touch();

      return !this.form.images.$invalid && !this.$refs.uploadRefs.uploading;
    },
  },
};
</script>
