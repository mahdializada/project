<template>
  <v-form v-if="model" class="mb-2" @submit.prevent="onSubmit">
    <v-textarea
      outlined
      class="my-2 vue-shortkey-avoid text-area"
      :placeholder="$tr('description')"
      maxLength="300"
      rows="5"
      dense
      :disabled="isLoading || isPosting"
      v-model.trim="descriptionText"
      no-resize
      :error-messages="descriptionErrMsg"
      @blur="handleError"
      hide-details
    >
      <template v-slot:append>
        <v-avatar color="blue lighten-4" size="22" v-if="descriptionText">
          {{ descriptionText.length }}
        </v-avatar>
      </template>
    </v-textarea>
    <div class="d-flex">
      <div class="w-half me-1">
        <v-btn
          small
          color="primary"
          :disabled="
            (!isEdit && descriptionText === '') || isLoading || isPosting
          "
          @click="onCancel"
          outlined
          class="rounded"
          block
        >
          {{ $tr("clear") }}
        </v-btn>
      </div>
      <div class="w-half">
        <v-btn
          small
          color="primary"
          @click="onSubmit"
          :disabled="isLoading || isPosting"
          :loading="isPosting"
          class="rounded"
          block
        >
          {{ $tr(isEdit ? "update" : "post") }}
        </v-btn>
      </div>
    </div>
  </v-form>
</template>
<script>
import HandleException from "~/helpers/handle-exception";
export default {
  props: {
    isLoading: { type: Boolean },
    description: { type: String },
    fileId: { type: String, required: true },
    fileType: { type: String, default: "personal" },
  },
  data() {
    return {
      model: false,
      isEdit: false,
      descriptionText: "",
      descriptionErrMsg: "",
      isPosting: false,
    };
  },

  methods: {
    toggleInput(value, modelVal) {
      if (value && value.trim()) this.isEdit = true;
      this.descriptionText = value;
      this.model = modelVal;
      this.descriptionErrMsg = "";
    },

    onCancel() {
      this.descriptionText = "";
      this.isEdit = this.isEdit && false;
    },
    async onSubmit() {
      try {
        this.isPosting = true;
        const body = {
          id: this.fileId,
          description: this.descriptionText,
          type: this.fileType,
        };
        const url = "files/description";
        await this.$axios.post(url, body);
        this.$emit("update:description", body.description);
        this.isEdit = false;
        this.isPosting = false;
        this.descriptionErrMsg = "";
        this.$emit("closeInput");
      } catch (error) {
        this.isPosting = false;
        HandleException.handleApiException(this, error);
      }
    },
    handleError() {
      if (this.descriptionText) {
        this.descriptionErrMsg = "";
      } else {
        this.descriptionErrMsg = this.$tr("please_write_your_description");
      }
    },
  },
};
</script>
<style>
.text-area .v-input__append-inner {
  position: absolute;
  bottom: 10px;
  right: 20px;
}
.text-area textarea {
  font-size: 12px !important;
  line-height: 1.6 !important;
}
</style>
