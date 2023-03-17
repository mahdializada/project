<template>
  <v-card class="rounded-0" elevation="1" width="100%">
    <v-divider />
    <v-container class="pt-0 pb-1">
      <FileUploadError v-if="isErrorOrWarning"/>
      <div class="d-flex justify-center" v-else>
        <i class="fas fa-exclamation-triangle fa-9x mt-5 mb-5" style="color: #FFCC00;"></i>
      </div>
      <p :class="`pb-2 px-5 text-center ${isErrorOrWarning} ? 'mt-4' : 'mt-4' `">
        {{$tr(error_text)}}
      </p>
    </v-container>
  </v-card>
</template>

<script>
import CustomButton from "../design/components/CustomButton";
import CustomTextfield from "../design/components/CustomTextfield";
import TextButton from "../common/buttons/TextButton.vue";
import FileUploadError from './FileUploadError';
export default {
  name: "file-error-user",
  components: {
    CustomButton,
    CustomTextfield,
    TextButton,
    FileUploadError,
  },
  props: {
    isErrorOrWarning: {
      type: Boolean, 
      default: true,
    }, 
    error_text: {
      type: String, 
      default: 'file_upload_extension_conflict'
    }
  },
  data() {
    return { files: null };
  },
  methods: {
    closeDialog() {
      this.$emit("closeDialog");
    },
    onDownload() {
      this.$emit("onDownload");
    },
    onUpload(){
      this.$emit("on-upload");
    }
  },
};
</script>
