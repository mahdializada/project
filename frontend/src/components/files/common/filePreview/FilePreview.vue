<template>
  <v-dialog
    persistent
    v-model="showPreviewDialog"
    fullscreen
    style="overflow: hidden"
  >
    <v-card class="d-flex overflow-hidden position-relative">
      <v-card
        :color="`${$vuetify.theme.dark ? 'dark' : 'surface darken2'}`"
        :class="`w-full rounded-0 first ${
          $vuetify.breakpoint.width < 1100 ? 'pe-4' : ''
        }`"
      >
        <TopHeader
          :is-loading.sync="isFetchingDetails"
          :is-deleting="isDeleting"
          :file-type="fileType"
          :is-root="isRoot"
          :file-data="fileData"
          :slug-name="slugName"
          :is-favorite.sync="isFavorite"
          @on-property="onProperty"
          @on-copy="onCopy"
          @on-move="onMove"
          @on-share="onShare"
          @on-delete="onDelete"
          @on-download="onDownload"
          @on-favorites="onFavorites"
          @closeDialog="onCloseDialog"
        />

        <MainBody
          ref="mainBodyRef"
          :is-loading.sync="isFetchingDetails"
          :details.sync="mainBodyData"
          :has-next.sync="hasNext"
          :has-prev.sync="hasPrev"
          @on-next="onNext"
          @on-prev="onPrev"
          @on-leave-pip="onLeavePipMode"
        />
      </v-card>
      <!-- <v-card v-show="!togglerFlag">
				<v-avatar
					@click="toggleDrawer"
					color="surface darken2"
					size="32"
					class="toggle-drawer"
				>
					<v-avatar size="23" :color="`${$vuetify.theme.dark ? '' : 'white'}`">
						<v-icon size="20" v-if="togglerFlag" dense color="primary">
							mdi-chevron-right
						</v-icon>
						<v-icon size="20" dense v-else color="primary">
							mdi-chevron-left
						</v-icon>
					</v-avatar>
				</v-avatar>
			</v-card> -->
      <v-card
        :class="`second ${togglerFlag ? 'show' : ''} px-2 ${
          $vuetify.breakpoint.width < 1100 ? 'absolute' : ''
        }`"
        style="max-height: 100vh"
      >
        <v-avatar
          @click="toggleDrawer"
          color="surface darken2"
          size="32"
          class="toggle-drawer"
        >
          <v-avatar size="23" :color="`${$vuetify.theme.dark ? '' : 'white'}`">
            <v-icon size="20" dense color="primary">
              {{ togglerFlag ? "mdi-chevron-right" : "mdi-chevron-left" }}
            </v-icon>
          </v-avatar>
        </v-avatar>
        <SideBarDrawer
          :isShare="this.slugName === 'shared'"
          ref="previewSidebarDrawerRef"
          :file-type.sync="fileType"
          :fileData="fileData"
          :isLoading.sync="isFetchingDetails"
          @handleResize="onHandleResize"
          @toggleHandler="toggleDrawer"
        />
      </v-card>
    </v-card>
  </v-dialog>
</template>
<script>
import TopHeader from "./TopHeader.vue";
import MainBody from "./MainBody.vue";
import SideBarDrawer from "./SideBarDrawer.vue";
import handleException from "../../../../helpers/handle-exception";

export default {
  components: {
    TopHeader,
    MainBody,
    SideBarDrawer,
  },
  props: {
    isDeleting: Boolean,
    showPreviewDialog: Boolean,
    fileIds: Array,
    previewLink: String,
    fileType: String,
    slugName: String,
    isRoot: Boolean,
  },
  data() {
    return {
      togglerFlag: true,
      isFetchingDetails: false,
      fileData: {},
      currentFileIndex: "",

      isFavorite: false,

      // MainBody
      hasNext: true,
      hasPrev: true,
      mainBodyData: {
        path: "",
        mime_type: "",
        extension: "",
      },
    };
  },

  mounted() {
    if (this.$vuetify.breakpoint.width < 1100) {
      this.togglerFlag = false;
    }
  },

  watch: {
    showPreviewDialog: function () {
      this.togglePrevNext();
    },

    fileIds: function () {
      this.togglePrevNext();
    },
  },

  methods: {
    // Top Header
    onDownload() {
      this.fileData.downloads_count++;
      this.$emit("on-download", this.fileData);
    },
    onProperty() {
      this.$refs.previewSidebarDrawerRef.selectPropertyTab();
    },
    onCopy() {
      this.$emit("on-copy", this.getItemDetails());
    },
    onMove() {
      this.$emit("on-move", this.getItemDetails());
    },
    onShare() {
      this.$emit("on-share", [this.fileData.id]);
    },
    onFavorites() {
      const { id, name, type, favorites } = this.fileData;
      this.$emit("on-favorites", [{ id, name, type, favorites }]);
      this.isFavorite = !this.isFavorite;
    },
    getItemDetails() {
      const { id, type } = this.fileData;
      return [{ id, type }];
    },

    onToggleHandler(flag) {
      setTimeout(() => {
        this.togglerFlag = !this.togglerFlag;
      }, 400);
    },
    toggleDrawer() {
      this.togglerFlag = !this.togglerFlag;
    },
    onHandleResize(clientHeightWidth) {
      this.$refs.mainBodyRef.handleResize(clientHeightWidth);
    },

    onLeavePipMode() {
      this.$emit("on-leave-pip");
    },

    togglePrevNext() {
      const currentFileIndexInList = this.getCurrentFileIndex();
      this.hasPrev = currentFileIndexInList > 0;
      this.hasNext = currentFileIndexInList + 1 < this.fileIds.length;
    },

    onDelete(val = true) {
      val && this.$emit("update:is-deleting", true);
      const { id, type, parent_id } = this.fileData;
      const param =
        this.slugName === "shared" ? this.fileData : [{ id, type, parent_id }];
      this.$emit("on-delete", param);
    },

    onDeleteSuccess() {
      if (this.getCurrentFileIndex() < this.fileIds.length - 1)
        return this.onNext();
      else if (this.getCurrentFileIndex() > 0) return this.onPrev();
      else this.onCloseDialog();
    },

    getCurrentFileIndex() {
      return this.fileIds.findIndex((id) => id === this.currentFileIndex);
    },

    onNext() {
      const currentFileIndexInList = this.getCurrentFileIndex();
      const nextFileId =
        currentFileIndexInList + 1 < this.fileIds.length &&
        this.fileIds[currentFileIndexInList + 1];
      if (nextFileId) {
        this.$echo.leave(`file_comment.${this.fileData.id}`);
        this.hasPrev = true;
        this.fetchFileInfo(nextFileId);
        this.updateUrl(nextFileId);
      }
      this.hasNext = currentFileIndexInList + 2 < this.fileIds.length;
    },
    onPrev() {
      const currentFileIndexInList = this.getCurrentFileIndex();
      if (currentFileIndexInList !== 0) {
        const prevFileId = this.fileIds[currentFileIndexInList - 1];
        if (prevFileId) {
          this.$echo.leave(`file_comment.${this.fileData.id}`);
          this.hasNext = true;
          this.fetchFileInfo(prevFileId);
          this.updateUrl(prevFileId);
        }
      }
      this.hasPrev = currentFileIndexInList > 1;
    },

    updateUrl(id) {
      this.$route.query?.preview &&
        history.replaceState(
          {},
          null,
          this.$route.path + (id ? "?preview=true&preview_id=" + id : "")
        );
    },

    onCloseDialog() {
      this.$echo.leave(`personal_file_comment.${this.fileData.id}`);
      const isInPipMode = this.$refs.mainBodyRef?.stopMedia();
      this.$emit("on-close", isInPipMode);
      this.updateUrl(false);
    },

    async fetchFileInfo(file_id) {
      document.pictureInPictureElement && document.exitPictureInPicture();
      this.currentFileIndex = file_id;
      try {
        this.isFetchingDetails = true;
        const response = await this.$axios.get(
          `${this.previewLink}/${file_id}`
        );
        if (response?.status === 200 && response?.data?.result) {
          const data = response?.data?.file;
          this.fileData = data;
          this.isFavorite = data?.favorites;
          this.mainBodyData = {
            id: data?.id,
            path: data?.path,
            mime_type: data?.mime_type,
            extension: data?.extension,
          };
          this.listenRealTime(this.fileData?.id);
        }
        this.isFetchingDetails = false;
      } catch (error) {
        this.isFetchingDetails = false;
        const { response } = error;
        if (response.data.unauthorized) {
          // this.$toastr.w(this.$tr("unauthorized_to_page"));
        this.$toasterNA("orange", this.$tr("unauthorized_to_page"));

          this.onCloseDialog();
        } else {
          handleException.handleApiException(this, error);
        }
      }
    },
    listenRealTime(id) {
      this.$echo
        .private(`personal_file_comment.${id}`)
        .listen("PersonalFileComment", async (e) => {
          const findFunc = (item) => item.id == comment_id;
          const findFuncReply = (item) => item.id == reply.id;

          const cmMapFunc = (item) => {
            return item.id == id ? { ...item, ...comment } : item;
          };
          const rpMapFunc = (item) => {
            return item.id == id ? { ...item, ...reply } : item;
          };
          const filterFunc = (item) => item.id !== id;
          switch (e.action) {
            case "comment-new":
              var comment = e.data.comment;
              var comment_id = comment.id;
              if (!this.fileData.comments.find(findFunc)) {
                this.fileData.comments_count++;
                this.fileData.comments.unshift(comment);
              }

              break;
            case "comment-update":
              var comment = e.data.comment;
              var id = comment.id;
              this.fileData.comments = this.fileData.comments.map(cmMapFunc);
              break;
            case "comment-delete":
              var id = e.data.id;
              this.fileData.comments =
                this.fileData.comments.filter(filterFunc);
              break;
            case "reply-new":
              var reply = e.data.reply;
              var id = reply.id;
              var comment_id = reply.comment_id;
              this.commentMap(comment_id, (item) => {
                if (!item.replies.find(findFuncReply)) {
                  item.replies.push(reply);
                }
              });
            case "reply-update":
              var reply = e.data.reply;
              var id = reply.id;
              var comment_id = reply.comment_id;
              this.commentMap(comment_id, (item) => {
                item.replies = item.replies.map(rpMapFunc);
              });
              break;
            case "reply-delete":
              var id = e.data.id;
              var comment_id = e.data.comment_id;
              this.commentMap(comment_id, (item) => {
                item.replies = item.replies.filter(filterFunc);
              });
              break;
            default:
              break;
          }
        });
    },
    commentMap(comment_id, callback) {
      this.fileData.comments = this.fileData.comments.map((item) => {
        if (item.id == comment_id) {
          callback(item);
        }
        return item;
      });
    },
  },
};
</script>
<style scoped>
.show-enter-active,
.show-leave-enter {
  transform: translateX(0);
  transition: all 0.4s;
}
.show-enter,
.show-leave-to {
  transform: translateX(100%);
  width: 0px !important;
  padding: 0px !important;
  transition: all 0.4s;
}

.second {
  position: relative;
  width: 16px;
  padding-left: 32px !important;
  padding-right: 0px !important;
  transition: all 0.4s;
}
.second.show {
  width: 432px;
  max-width: 95%;
  padding-left: 16px !important;
  padding-right: 24px !important;
}
.second.absolute {
  position: absolute !important;
  top: 0;
  right: 0;
  bottom: 0;
}
.second.absolute.show {
  padding-right: 16px !important;
  max-width: 95%;
  width: 416px !important;
}
.toggle-drawer {
  position: absolute;
  top: 18px;
  left: -18px;
  z-index: 10;
  cursor: pointer;
}
</style>
