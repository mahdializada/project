<template>
  <v-card-text
    class="d-flex surface darken2 rounded"
    :class="{ disable__class: isDeleting }"
    style="padding: 10px !important"
  >
    <div class="me-1">
      <v-avatar size="33">
        <img :src="comment.creator.image" alt="John" />
      </v-avatar>
    </div>
    <div class="d-flex flex-column w-full">
      <div class="d-flex justify-space-between">
        <div>
          <span class="header">{{ creator }}</span>
          <span class="mx-1 text-caption text-grey">●</span>
          <span class="text-caption text-grey">{{ formatDateFromNow }}</span>
        </div>
        <div>
          <v-menu
            v-if="comment.can_delete || comment.can_edit"
            bottom
            offset-y
            offset-x
            left
          >
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                v-bind="attrs"
                v-on="on"
                style="cursor: pointer"
                color="grey"
                dense
                >mdi-dots-horizontal</v-icon
              >
            </template>
            <v-list>
              <v-list-item
                v-if="comment.can_delete"
                dense
                link
                @click="onDelete"
              >
                <v-list-item-icon>
                  <v-icon size="16" fab> mdi-delete </v-icon>
                </v-list-item-icon>
                <v-list-item-title class="font-weight-medium body-2">
                  {{ $tr("delete") }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="comment.can_edit" dense link @click="onEdit">
                <v-list-item-icon>
                  <v-icon size="16" fab>mdi-pencil</v-icon>
                </v-list-item-icon>
                <v-list-item-title class="font-weight-medium body-2">
                  {{ $tr("edit") }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>
      </div>
      <div>
        <span class="comments" style="word-break: break-all">{{
          comment.comment | capitalize
        }}</span>
      </div>

      <div
        style="margin: 4px 0 16px 0"
        v-if="comment.replies && comment.replies.length > 0"
      >
        <span
          @click="toggleReplies"
          :style="`cursor: pointer; padding: 4px 8px; background-color: ${
            $vuetify.theme.themes.light.primary + 24
          }`"
          class="rounded-lg primary--text me-1"
          >{{ hideReplies ? "Show replies" : "Hide replies" }}</span
        >
        <v-icon small color="grey">mdi-comment-text-multiple-outline</v-icon>
        <span class="text-caption text-grey" v-if="comment.replies">{{
          comment.replies.length
        }}</span>
      </div>

      <div v-if="!hideReplies">
        <CommentReply
          v-for="reply in comment.replies"
          :key="reply.id"
          @on-delete="onReplyDelete"
          @on-edit="onReplyEdit"
          :reply="reply"
        />
      </div>
      <v-text-field
        v-model="reply"
        dense
        solo
        max-length="300"
        flat
        outlined
        :label="$tr('drop_your_reply')"
        hide-details
        class="mt-1 vue-shortkey-avoid"
      >
        <template v-slot:append>
          <v-btn
            type="submit"
            :loading="isPosting"
            @click="onSubmit"
            small
            color="primary"
            >{{ $tr(isEdit ? "update" : "reply") }}</v-btn
          >
        </template>
      </v-text-field>
    </div>
  </v-card-text>
</template>
<script>
import moment from "moment-timezone";
import HandleException from "~/helpers/handle-exception";
import CommentReply from "./CommentReply.vue";
import MenuItems from "../MenuItems.vue";
export default {
  components: { CommentReply, MenuItems },
  name: "file-preview-comments",
  props: {
    comment: {
      type: Object,
      required: true,
    },
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
      reply: "",
      editId: "",
      isEdit: false,
      isPosting: false,
      hideReplies: false,
      isDeleting: false,
    };
  },

  computed: {
    formatDateFromNow() {
      return moment(this.comment.updated_at)
        .locale(this.$vuetify.lang.current)
        .fromNow(true);
    },
    creator() {
      if (this.comment.creator) {
        const { name, id } = this.comment.creator;
        if (id == this.$auth.user.id) {
          return this.$tr("owner_user_name");
        }
        return name;
      }
      return "";
    },
  },

  methods: {
    toggleReplies() {
      this.hideReplies = !this.hideReplies;
    },
    async onDelete() {
      try {
        this.isDeleting = true;
        const url = "files/comment/" + this.comment.id;
        await this.$axios.delete(url);
        this.$emit("on-delete", this.comment.id);
      } catch ({ response: { data } }) {
        if (data.unauthorized) {
          let errorText = "unauthorized_to_do_this_operation";
          // this.$toastr.e(this.$tr(errorText));
			this.$toasterNA("red", this.$tr(errorText));

        } else if (data.not_found) {
          let errorText = "record_not_found";
          // this.$toastr.e(this.$tr(errorText));
			this.$toasterNA("red", this.$tr(errorText));

        } else {
          let errorText = "something_went_wrong";
          // this.$toastr.e(this.$tr(errorText));
			this.$toasterNA("red", this.$tr(errorText));

        }
      }
      this.isDeleting = false;
    },
    onEdit() {
      this.$emit("on-edit", this.comment);
    },

    onReplyDelete(replyId) {
      this.comment.replies = this.comment.replies?.filter(
        (items) => items?.id !== replyId
      );
    },
    async onReplyEdit(reply) {
      this.reply = reply.comment;
      this.isEdit = true;
      this.editId = reply.id;
    },
    async onSubmit() {
      try {
        if (this.reply) {
          this.isPosting = true;
          const body = {
            commentable_id: this.commentableId,
            text: this.reply,
            type: this.fileType,
            action: this.isEdit ? "update" : "store",
            comment_id: this.isEdit ? this.editId : this.comment.id,
            action_type: "reply",
          };
          const { data } = await this.$axios.post("files/comment", body);
          if (data.store) {
            this.comment.replies.push(data.comment);
          } else if (data.update) {
            const { replies } = this.comment;
            const index = replies.findIndex(
              (item) => item.id == body.comment_id
            );
            if (index >= -1) {
              let item = replies[index];
              item.comment = body.text;
              item.updated_at = data.updated_at;
              replies[index] = item;
            }
          }

          this.reply = "";
          this.isEdit = false;
          this.isPosting = false;
          this.editId = null;
          this.reply = "";
        }
      } catch (error) {
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
  },
};
</script>
<style scoped>
.disable__class {
  opacity: 0.5;
  pointer-events: none;
  cursor: not-allowed;
}
.comments {
  font-size: 12px;
  margin-top: 10px;
  margin-bottom: 0px;
}
.header {
  font-size: 13px;
  font-weight: 600;
}
</style>
