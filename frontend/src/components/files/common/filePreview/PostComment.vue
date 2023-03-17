<template>
  <v-form class="mb-2" @submit.prevent="onSubmit">
    <v-textarea
      outlined
      class="my-2 vue-shortkey-avoid text-area"
      :placeholder="$tr('write_down_your_comment')"
      maxLength="300"
      rows="4"
      dense
      :disabled="isLoading"
      v-model.trim="form.comment"
      no-resize
      :error-messages="commentErrMsg"
      @blur="handleError"
      hide-details
    >
      <template v-slot:append>
        <v-avatar color="blue lighten-4" size="22" v-if="form.comment">
          {{ form.comment.length }}
        </v-avatar>
      </template>
    </v-textarea>
    <div class="d-flex">
      <div class="w-half me-1">
        <v-btn
          small
          color="primary"
          :disabled="!isEdit && form.comment === ''"
          @click="onCancel"
          outlined
          class="rounded"
          block
        >
          {{ $tr(isEdit ? "cancel" : "clear") }}
        </v-btn>
      </div>
      <div class="w-half">
        <v-btn
          small
          color="primary"
          @click="onSubmit"
          :disabled="isLoading"
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
  name: "PostComment",
  props: {
    isLoading: Boolean,
    comment: Object,
    fileType: {
      type: String,
      default: "personal",
    },
    commentableId: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      isEdit: false,
      form: {
        comment: "",
      },
      commentErrMsg: "",
      isPosting: false,
    };
  },
  watch: {
    comment: function (val) {
      if (val && "comment" in val) {
        this.form.comment = val.comment;
        this.isEdit = true;
      } else {
        this.isEdit = false;
        this.form.comment = "";
      }
    },
  },
  methods: {
    onCancel() {
      this.form.comment = "";
      this.isEdit = this.isEdit && false;
    },
    async onSubmit() {
      try {
        if (this.form.comment) {
          this.isPosting = true;
          const body = {
            commentable_id: this.commentableId,
            text: this.form.comment,
            type: this.fileType,
            action: this.isEdit ? "update" : "store",
            comment_id: this.isEdit ? this.comment.id : null,
            action_type: "comment",
          };
          const { data } = await this.$axios.post("files/comment", body);
          if (data.store) {
            this.$emit("append-comment", data.comment);
          } else if (data.update) {
            this.$emit("update-comment", {
              ...data.comment,
              comment: body.text,
            });
          }
          this.isEdit = false;
          this.isPosting = false;
          this.form.comment = "";
          this.commentErrMsg = "";
        } else {
          this.isPosting = false;
          this.commentErrMsg = this.$tr("please_write_your_comment");
        }
      } catch (error) {
        const { response } = error;
        if (response.status == 401 || response.data.unauthorized) {
          let errorText = "unauthorized_to_do_this_operation";
          // this.$toastr.e(this.$tr(errorText));
			this.$toasterNA("red", this.$tr(errorText));

        } else {
          HandleException.handleApiException(this, error);
        }
        this.isPosting = false;
      }
    },
    handleError() {
      if (this.form.comment) {
        this.commentErrMsg = "";
      } else {
        this.commentErrMsg = this.$tr("please_write_your_comment");
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
