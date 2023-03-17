<template>
  <div class="pa-2">
    <FileBreadCrumb
      :items="currentPage.breadcrumb"
      @shortkey.native="theAction"
      v-shortkey="{
        up: ['arrowup'],
        down: ['arrowdown'],
        right: ['arrowright'],
        left: ['arrowleft'],
        ctrlA: ['ctrl', 'a'],
      }"
    />

    <FilePreview
      :key="filePreviewKey"
      :showPreviewDialog="showPreviewDialog"
      :file-ids="currentPage.files.map(({ id }) => id)"
      ref="filePreviewRef"
      preview-link="files/personal/details"
      file-type="personal"
      slug-name="my_shares"
      :is-root="currentPage.parent == null"
      :is-deleting.sync="isDeletingFromPreview"
      @on-close="onPreviewClose"
      @on-delete="onPreviewDelete"
      @on-download="onDownload"
      @on-leave-pip="showPreviewDialog = true"
    />

    <TopSelectActionMenu
      :isMyShare="true"
      :parent="currentPage.parent"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @selectAll="selectAll"
      :selectedCount="selectedCount"
      :selected="getSelected()"
      :isAllSelected="
        selectedCount == currentPage.folders.length + currentPage.files.length
      "
    />

    <RightClickMenu
      v-if="layout != 'list'"
      @onOpen="openFolder"
      @onRename="onItemRename"
      @onDelete="deleteFiles"
      @onProperty="onProperty"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @emptySelected="emptySelected"
      @onRefresh="
        () => {
          $emit('onRefresh');
          selectedCount = 0;
        }
      "
      :activeChildItem="currentPage.sortBy"
      :selected="getSelected()"
      :showShareDelete="
        selectedItem ? selectedItem.user_id == this.$auth.user.id : false
      "
    />

    <div class="content-section" v-if="layout == 'list'">
      <ListView
        :isPaginating="isPaginating"
        @onDelete="deleteFiles"
        @updateItem="updatePersonalFile"
        @onDownload="onDownload"
        @onMultiDownload="onMultiDownload"
        link="drive"
        :currentPage="
          currentPage.folders && {
            ...currentPage,
            items: [...currentPage.folders, ...currentPage.files],
          }
        "
        @onClicked="
          (selected) => {
            if (selected) {
              selectedCount++;
            } else {
              selectedCount--;
            }
          }
        "
      />
    </div>
    <div
      v-else
      ref="container"
      :class="`file-content pa-2 bg-light-gray rounded-lg mt-2 ${
        selectedCount > 0 ? 'show-top-bar' : ''
      }`"
      @contextmenu.stop="onRightClick"
    >
      <div class="content-section" v-if="loading">
        <SectionTitle title="Folders" />
        <div class="d-flex flex-wrap px-2">
          <v-skeleton-loader
            v-for="item in 8"
            :key="item"
            style="width: 280px"
            class="ma-1"
            type="list-item-avatar, divider, table-heading"
          ></v-skeleton-loader>
        </div>

        <SectionTitle title="Files" />
        <div class="d-flex flex-wrap px-2">
          <v-skeleton-loader
            v-for="item in 8"
            :key="item"
            style="width: 280px"
            class="ma-1 custom_skeleton"
            type="image, list-item-avatar, divider, table-heading"
          ></v-skeleton-loader>
        </div>
      </div>
      <div v-else class="h-full">
        <div
          v-if="currentPage.folders && currentPage.folders.length > 0"
          class="content-section"
        >
          <SectionTitle title="Folders" />
          <div class="d-flex flex-wrap px-2">
            <Folder
              v-for="(folder, folderIndex) in currentPage.folders"
              :key="folder.id"
              :ref="folder.id"
              :folder="folder"
              :index="folderIndex"
              :link="`/files/personal/my_shares/${folder.id}`"
              actionUrl="files/personal/folder/action"
              :parent_id="currentPage.parent && currentPage.parent.id"
              @onRightClick="onItemRightClick"
              @onClicked="
                (selected) => {
                  if (selected) {
                    selectedCount++;
                  } else {
                    selectedCount--;
                  }
                }
              "
              :showCreatedBy="true"
            />
          </div>
        </div>
        <div v-else-if="not_found" style="height: 100%" class="content-section">
          <FolderNotFound />
        </div>
        <div
          v-else-if="
            currentPage.files &&
            currentPage.files.length < 1 &&
            currentPage.folders &&
            currentPage.folders.length < 1
          "
          style="height: 100%"
          class="content-section"
        >
          <NoFilesComponent />
        </div>
        <div
          v-if="currentPage.files && currentPage.files.length > 0"
          class="content-section"
        >
          <SectionTitle title="Files" />
          <div class="d-flex flex-wrap px-2">
            <File
              v-for="file in currentPage.files"
              :key="file.id"
              :file="file"
              :ref="file.id"
              actionUrl="files/personal/files/rename"
              :parent_id="currentPage.parent && currentPage.parent.id"
              @itemRenamed="({ name }) => (file.name = name)"
              @onRightClick="onItemRightClick"
              @dbClick="dbClickHandler"
              @onClicked="
                (selected) => {
                  if (selected) {
                    selectedCount++;
                  } else {
                    selectedCount--;
                  }
                }
              "
              :showCreatedBy="true"
            />
          </div>
        </div>
      </div>
    </div>

    <v-navigation-drawer
      v-model="properties"
      absolute
      temporary
      right
      width="450"
    >
      <Properties
        :isMyShare="true"
        @close="properties = false"
        @removeFromShare="removeFromShare"
        :items="propertiesData"
      ></Properties>
    </v-navigation-drawer>

    <!-- <ToggleListGrid /> -->
    <FilePasswordModel setPasswordUrl="files/shared/files/password" />
  </div>
</template>
<script>
import RightClickMenu from "~/components/files/common/RightClickMenu.vue";
import FileBreadCrumb from "~/components/files/common/FileBreadCrumb.vue";
import SectionTitle from "~/components/files/common/SectionTitle.vue";
import Folder from "~/components/files/common/Folder.vue";
import File from "~/components/files/common/File.vue";
import ToggleListGrid from "~/components/files/common/ToggleListGrid.vue";
import FilePasswordModel from "~/components/files/common/FilePasswordModel.vue";
import ListView from "~/components/files/common/ListView.vue";
import { mapMutations, mapState } from "vuex";
import FilePreview from "~/components/files/common/filePreview/FilePreview.vue";
import Properties from "~/components/files/common/Properties.vue";
import TopSelectActionMenu from "~/components/files/common/TopSelectActionMenu.vue";
import FileManagement from "~/utils/file_management/FileManagement";
import Alert from "~/helpers/alert";
import NoFilesComponent from "../../../../components/files/common/NoFilesComponent.vue";
import FolderNotFound from "../../../../components/files/common/FolderNotFound.vue";

export default {
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
    not_found: {
      type: Boolean,
      default: false,
    },
    currentPage: {
      type: Object,
      default: () => ({
        files: [],
        folders: [],
      }),
    },
  },

  data() {
    return {
      filePreviewKey: 0,
      isDeletingFromPreview: false,
      showPreviewDialog: false,
      isPaginating: false,
      selectedItem: null,
      properties: false,
      propertiesData: null,
      itemsToShare: [],
      selectedCount: 0,
      newWindowPreview: false,
      newWindowPreviewItemId: null,
    };
  },

  computed: {
    ...mapState("files", ["layout"]),
  },

  mounted() {
    this.emptySelected();
    this.$root.$on("removeFromShare", this.removeFromShare);
    this.newWindowPreview = this.$route.query.preview == "true";
    this.newWindowPreviewItemId = this.$route.query?.preview_id;
    this.$nextTick(function () {
      this.newWindowPreview &&
        this.dbClickHandler({ id: this.newWindowPreviewItemId });
    });
  },

  beforeDestroy() {
    this.emptySelected();
  },

  methods: {
    ...mapMutations("files", ["pushZippingFile"]),

    /** =======> Common Operation Methods <====== */

    onPreviewDelete(deletedItem) {
      this.selectedItem = deletedItem;
      this.deleteFiles(null);
    },

    async deleteFiles(deletedItems) {
      let isMulti = Array.isArray(deletedItems);
      let isFolder = !isMulti ? this.selectedItem.type == "folder" : false;
      let items = this.getDeletedItems(isMulti ? deletedItems : null);
      let phrase = isMulti
        ? this.$tr("items")
        : isFolder
        ? this.$tr("folder")
        : this.$tr("file");
      if (this.currentPage.parent == null) {
        Alert.shareRemoveAlert(
          this,
          this.$tr("remove_shared_item", phrase),
          `${this.$tr("would_you_like_to_unshare_selected_item", phrase)}`,
          async () => {
            // root multi

            await this.unShareSubmit(items);
          },
          () => {},
          this.$tr("yes_unshare"),
          "",
          false
        );
      }
    },

    getDeletedItems(deletedItems) {
      return deletedItems
        ? deletedItems.map((item) => ({
            id: item.id,
            type: item.type,
            path: item.path,
            parent_id: item.parent_id,
            user_id: item.user_id,
          }))
        : [
            {
              id: this.selectedItem.id,
              type: this.selectedItem.type,
              path: this.selectedItem.path,
              parent_id: this.selectedItem.parent_id,
              user_id: this.selectedItem.user_id,
            },
          ];
    },

    async unShareSubmit(items) {
      let deletedItems = this.removeDeletedItems(items);
      try {
        const res = await this.$axios.delete(
          "/files/personal/my_shares/delete",
          {
            data: {
              items,
            },
          }
        );
        if (res.data.result) {
          // this.$toastr.s(this.$tr("selected_items_unshared_successfully"));
			this.$toasterNA("green", this.$tr('selected_items_unshared_successfully'));

        }
      } catch (e) {
        this.addDeletedItems(deletedItems);
        // this.$toastr.e(this.$tr("could_not_unshare"));
        this.$toasterNA("red",this.$tr("could_not_unshare"));

      }
    },

    removeDeletedItems(items) {
      const fileManagement = new FileManagement(this);
      let { files, folders } = this.currentPage;
      let removeResult = fileManagement.removeItemsAndReturn({
        items,
        files,
        folders,
      });
      this.currentPage.files = removeResult.files;
      this.currentPage.folders = removeResult.folders;
      return removeResult.removedItems;
    },

    addDeletedItems(items) {
      const fileManagement = new FileManagement(this);
      let { files, folders } = this.currentPage;
      let pushResult = fileManagement.pushItemsAndReturn({
        items,
        files,
        folders,
      });
      this.currentPage.files = pushResult.files;
      this.currentPage.folders = pushResult.folders;
    },

    removeFromShare(info, item) {
      Alert.removeDialogAlert(
        this,
        async () => {
          let fileManagement = new FileManagement(this);
          let { length, res } = await fileManagement.removeFromShare(
            info,
            item,
            [...this.currentPage.folders, ...this.currentPage.files]
          );
          if (res.status == 200 && length == 0) {
            this.currentPage[item.type + "s"] = this.currentPage[
              item.type + "s"
            ].filter((item2) => item.id !== item2.id);
          }
        },
        "",
        "yes_delete"
      );
    },

    onMultiDownload(items) {
      const url = "files/personal/files/download";
      this.pushZippingFile({ url, items: items, context: this });
    },

    onPreviewClose(isInPipMode) {
      this.showPreviewDialog = false;
      this.newWindowPreview = false;
      !isInPipMode && this.filePreviewKey++;
      if (this.$route.query.preview) {
        this.$router.push("/files/personal/my_shares?preview=false");
      }
    },

    onDownload(item) {
      const url = "files/personal/files/download";
      if (item) {
        this.pushZippingFile({ url, item: item, context: this });
      } else if (this.selectedItem) {
        this.pushZippingFile({ url, item: this.selectedItem, context: this });
      }
    },

    updatePersonalFile(item, propertyKey) {
      const { id, type } = item;
      const property = item[propertyKey];
      if (type == "folder") {
        const { folders } = this.currentPage;
        const itemIndex = folders.findIndex((folder) => folder.id == id);
        if (itemIndex != -1) {
          folders[itemIndex].favorites = property;
        }
      } else {
        const { files } = this.currentPage;
        const itemIndex = files.findIndex((file) => file.id == id);
        if (itemIndex != -1) {
          files[itemIndex].favorites = property;
        }
      }
    },

    // rename items
    onItemRename() {
      const { id } = this.selectedItem;
      const selectedItem = this.$refs[id];
      if (Array.isArray(selectedItem)) {
        selectedItem[0].toggleNameInput();
      } else if (selectedItem) {
        selectedItem.toggleNameInput();
      }
    },

    //  Open Folder in the current Page or In New Tab
    openFolder({ isNewWindow, item }) {
      item = item || this.selectedItem;
      let { id, type } = item;
      if (type === "file" && !isNewWindow) return this.dbClickHandler(item);
      if (id) {
        const url =
          type === "file"
            ? `${this.$route.path}?preview=true&preview_id=${id}`
            : `/files/personal/drive/${id}`;
        isNewWindow ? window.open(url, "_blank") : this.$router.push(url);
      }
    },

    /** =======> Common Operation Methods <====== */
    onProperty() {
      if (this.selectedItem) {
        this.properties = true;
        this.propertiesData = this.selectedItem;
      }
    },

    dbClickHandler(file) {
      this.fileIds = this.currentPage.files.map(({ id }) => id);
      this.showPreviewDialog = true;
      this.$refs.filePreviewRef.fetchFileInfo(file?.id);
    },

    //  Display context menu on folder right clicked
    onItemRightClick({ event, item }) {
      this.selectedItem = item;
      const options = {
        items: [
          "open",
          "open_in_new_window",
          "download",
          this.currentPage.parent == null ? "unshare" : "nothing",
          "properties",
        ],
        showMenu: true,
        positionX: event.clientX,
        positionY: event.clientY,
      };
      this.$root.$emit("toggleContextMenu", options);
    },

    /** =======> Current Page Methods <====== */

    // display context menu on current page right clicked
    onRightClick(event) {
      event.preventDefault();
      const options = {
        items: ["sort_by", "thumbnail", "list", "refresh"],
        showMenu: true,
        positionX: event.clientX,
        positionY: event.clientY,
      };
      this.$root.$emit("toggleContextMenu", options);
    },

    theAction(event) {
      if (event.srcKey == "ctrlA") {
        this.selectAll(true);
        return;
      }
      let selects = this.getSelected();
      const element = document.querySelector(".file-content");
      if (selects?.length == 1) {
        let container_width = this.$refs.container.offsetWidth - 64;
        let items = [...this.currentPage.folders, ...this.currentPage.files];
        let index = items.findIndex((item) => item.id == selects[0].id);
        if (index != -1) {
          items[index].isSelected = false;
          if (this.layout == "grid") {
            let step = Math.floor(container_width / 296);
            switch (event.srcKey) {
              case "up":
                if (index - step >= 0) {
                  items[index - step].isSelected = true;
                } else {
                  items[0].isSelected = true;
                }
                break;
              case "down":
                if (index + step < items.length) {
                  items[index + step].isSelected = true;
                } else {
                  items[items.length - 1].isSelected = true;
                }
                break;
              case "right":
                if (index + 1 < items.length) {
                  items[index + 1].isSelected = true;
                } else {
                  items[items.length - 1].isSelected = true;
                }
                break;
              case "left":
                if (index - 1 >= 0) {
                  items[index - 1].isSelected = true;
                } else {
                  items[0].isSelected = true;
                }
                break;
            }
          } else if (this.layout == "list") {
            switch (event.srcKey) {
              case "up":
                if (index - 1 >= 0) {
                  items[index - 1].isSelected = true;
                } else {
                  items[0].isSelected = true;
                }
                break;
              case "down":
                if (index + 1 < items.length) {
                  items[index + 1].isSelected = true;
                } else {
                  items[items.length - 1].isSelected = true;
                }
                break;
            }
          }
        }
        if (element) {
          let elToScroll = this.$refs[this.getSelected()[0].id][0].$el;
          element.scrollTo({
            top:
              elToScroll.offsetTop -
              (element.offsetHeight / 2 - elToScroll.offsetHeight / 2),
          });
        }
      }
    },

    selectAll(isSelected) {
      if (isSelected) {
        let { folders, files } = this.currentPage;
        let arrLength = folders.length;
        for (let i = 0; i < arrLength; i++) {
          if (folders[i].isSelected == false) {
            this.selectedCount++;
            folders[i].isSelected = true;
          }
        }
        let arrLength2 = files.length;
        for (let i = 0; i < arrLength2; i++) {
          if (files[i].isSelected == false) {
            this.selectedCount++;
            files[i].isSelected = true;
          }
        }
      } else {
        this.emptySelected();
      }
    },

    emptySelected() {
      this.selectedCount = 0;
      let { folders, files } = this.currentPage;
      let arrLength = folders?.length;
      for (let i = 0; i < arrLength; i++) {
        if (folders[i].isSelected == true) {
          folders[i].isSelected = false;
        }
      }
      let arrLength2 = files?.length;
      for (let i = 0; i < arrLength2; i++) {
        if (files[i].isSelected == true) {
          files[i].isSelected = false;
        }
      }
    },

    getSelected() {
      let files = this.currentPage.files.filter((item) => {
        if (item.isSelected) {
          return item;
        }
      });
      let folders = this.currentPage.folders.filter((item) => {
        if (item.isSelected) {
          return item;
        }
      });
      return [...files, ...folders];
    },
  },
  components: {
    FileBreadCrumb,
    SectionTitle,
    Folder,
    File,
    ToggleListGrid,
    ListView,
    RightClickMenu,
    FilePreview,
    Properties,
    TopSelectActionMenu,
    FilePasswordModel,
    NoFilesComponent,
    FolderNotFound,
  },
};
</script>
<style lang="scss">
.file-content {
  width: calc(100% - 32px);
  transition: top 0.4s;
}
.show-top-bar {
  top: 130px !important;
}
.custom_skeleton .v-skeleton-loader__image {
  height: 100px !important;
  border-radius: 0;
}
.select-all__checkbox {
  position: absolute;
  left: 16px;
  top: 4px !important;
}
.select-action-menu {
  height: 38px;
  padding-top: 8px;
  padding-bottom: 8px;
  transition: all 0.4s;
}
.custom-h-0 {
  height: 0px !important;
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  margin: 0px !important;
  overflow: hidden;
}
</style>
