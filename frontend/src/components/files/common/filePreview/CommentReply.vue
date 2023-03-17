<template>
  <div :class="{ disable__class: isDeleting }" class="d-flex mb-1">
    <div class="me-1">
      <v-avatar size="33">
        <img :src="reply.creator.image" alt="User Image" />
      </v-avatar>
    </div>
    <div class="d-flex flex-column w-full">
      <div class="d-flex justify-space-between">
        <div>
          <span class="header">{{ creator }}</span>
          <span class="mx-1 text-caption text-grey">●</span>
          <span class="text-caption text-grey">{{
            formatDateFromNow(reply.updated_at)
          }}</span>
        </div>
        <div>
          <v-menu
            v-if="reply.can_delete || reply.can_edit"
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
              <v-list-item v-if="reply.can_delete" dense link @click="onDelete">
                <v-list-item-icon>
                  <v-icon size="16" fab> mdi-delete </v-icon>
                </v-list-item-icon>
                <v-list-item-title class="font-weight-medium body-2">
                  {{ $tr("delete") }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="reply.can_edit" dense link @click="onEdit">
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
          reply.comment
        }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment-timezone";
import HandleException from "~/helpers/handle-exception";
export default {
  name: "CommentReply",
  props: {
    reply: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {
      isDeleting: false,
    };
  },

  computed: {
    formatDateFromNow() {
      return (date) =>
        moment(date).locale(this.$vuetify.lang.current).fromNow(true);
    },
    creator() {
      if (this.reply.creator) {
        const { name, id } = this.reply.creator;
        if (id == this.$auth.user.id) {
          return this.$tr("owner_user_name");
        }
        return name;
      }
      return "";
    },
  },

  methods: {
    async onDelete() {
      try {
        this.isDeleting = true;
        const url = "files/comment/" + this.reply.id;
        const response = await this.$axios.delete(url);
        if (response?.status === 200 && response?.data?.result) {
          this.$emit("on-delete", this.reply.id);
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isDeleting = false;
    },
    onEdit() {
      this.$emit("on-edit", this.reply);
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
