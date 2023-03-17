<template>
  <v-card flat class="rounded-0 h-auto">
    <div style="width: 400px; max-width: 100%">
      <v-tabs grow v-model="tab" :show-arrows="false" centered>
        <v-tab
          class="custom-tab-item"
          :value="item.tab"
          v-for="item in items"
          :key="item.tab"
        >
          <v-icon left small class="me-1">{{ item.icon }}</v-icon>
          {{ item.tab }}
        </v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab">
        <v-tab-item>
          <v-card flat>
            <PostComment
              :is-loading="isLoading"
              :file-type.sync="fileType"
              v-if="fileData.id"
              :comment="comment"
              @append-comment="appendComment"
              @update-comment="updateComment"
              :commentable-id="fileData.id"
            />
            <div :style="`height: ${clientHeight}px; overflow-y: auto;`">
              <ListSkeleton v-if="isLoading" :length="10" />

              <div
                v-if="fileData.comments && fileData.comments.length <= 0"
                class="text-center mt-2"
              >
                No comments found.
              </div>
              <Comment
                v-for="(comment, index) in fileData.comments"
                @on-delete="onCommentDelete"
                @on-edit="onCommentEdit"
                :key="index"
                :comment="comment"
                :commentable-id="fileData.id"
                class="mb-1"
              />
            </div>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <ListSkeleton class="mt-2" v-if="isLoading" :length="8" />
          <ListCollection v-else :fileData="fileData" :isShare="isShare" />
        </v-tab-item>
      </v-tabs-items>
    </div>
  </v-card>
</template>
<script>
import CustomInput from "../../../design/components/CustomInput.vue";
import Comment from "./Comment.vue";
import ListItem from "./ListItem.vue";
import PropertyHeader from "./PropertyHeader.vue";
import PostComment from "./PostComment.vue";
import ListCollection from "./ListCollection.vue";
import ListSkeleton from "./ListSkeleton.vue";
export default {
  name: "side-bar-drawer",
  components: {
    ListItem,
    PropertyHeader,
    CustomInput,
    Comment,
    PostComment,
    ListCollection,
    ListSkeleton,
  },
  props: {
    isShare: Boolean,
    fileData: Object,
    isLoading: Boolean,
    fileType: String,
    selectedTab: [String, Number],
    togglerFlag: Boolean,
  },

  watch: {
    "fileData.id": function (value) {
      this.comment = {};
    },
  },

  data() {
    return {
      clientHeight: document.documentElement.clientHeight - 280,
      tab: 0,
      comment: {},
      items: [
        { tab: "Comments", icon: "fa-comments", content: "Commments" },
        { tab: "Properties", icon: "fa-info-circle", content: "Properties" },
      ],
    };
  },

  methods: {
    onCommentDelete(commentId) {
      this.fileData.comments = this.fileData.comments?.filter(
        (items) => items?.id !== commentId
      );
      this.fileData.comments_count--;
    },
    onCommentEdit(comment) {
      this.comment = comment;
    },

    appendComment(comment) {
      const { comments } = this.fileData;
      if (Array.isArray(comments)) {
        const item = comments.find(({ id }) => id == comment.id);
        if (!item) {
          this.fileData.comments.unshift(comment);
          this.fileData.comments_count++;
        }
      }
    },
    updateComment({ id, comment, updated_at }) {
      this.comment = {};
      const { comments } = this.fileData;
      const index = comments.findIndex((item) => item.id == id);
      if (index >= -1) {
        let item = comments[index];
        item.comment = comment;
        item.updated_at = updated_at;
        comments[index] = item;
      }
    },
    handleResize() {
      this.clientHeight = document.documentElement.clientHeight - 280;
      this.$emit("handleResize", document.documentElement);
    },
    selectPropertyTab() {
      this.tab !== 1 && (this.tab = 1);
      // this.tab = 1;
    },
  },
  created() {
    window.addEventListener("resize", this.handleResize);
    this.handleResize();
  },
  destroyed() {
    window.removeEventListener("resize", this.handleResize);
  },
};
</script>
<style scoped>
.custom-tab-item {
  font-size: 14px;
  text-transform: capitalize;
  font-weight: 600;
  letter-spacing: 0.5px;
  height: 100%;
}
</style>
