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
      :is-deleting.sync="isDeletingFromPreview"
      @on-close="onPreviewClose"
      @on-delete="deleteFiles"
      @on-copy="onCopy"
      @on-move="onMove"
      @on-share="onShare"
      @on-favorites="addToFavorites"
      @on-download="onDownload"
      @on-leave-pip="showPreviewDialog = true"
    />

    <ShareModal
      v-if="shareModal"
      :dialog="shareModal"
      :items="itemsToShare"
      @toggleShareModal="toggleShareModal"
      @shareSuccess="shareSuccess"
    />

    <TopSelectActionMenu
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @selectAll="selectAll"
      @onShare="onShare"
      :selectedCount="selectedCount"
      :selected="getSelected()"
      :isAllSelected="
        selectedCount == currentPage.folders.length + currentPage.files.length
      "
      @shortkey.native="onCustomShortKeys"
      v-shortkey="{
        ctrlC: ['ctrl', 'c'],
        ctrlX: ['ctrl', 'x'],
        refresh_page: ['ctrl', 'alt', 'r'],
      }"
    />
    <RightClickMenu
      v-if="layout != 'list'"
      @onOpen="openFolder"
      @onOpenFile="onOpenFile"
      @onRename="onItemRename"
      @onFavorites="addToFavorites"
      @onDelete="deleteFiles"
      @onProperty="onProperty"
      @onCopy="onCopy"
      @onMove="onMove"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @onShare="onShare"
      @emptySelected="emptySelected"
      @onRefresh="
        () => {
          $emit('onRefresh');
          selectedCount = 0;
        }
      "
      :activeChildItem="currentPage.sortBy"
      :selected="getSelected()"
    />
    <div class="content-section" v-if="layout == 'list'">
      <ListView
        :isPaginating="isPaginating"
        :loading="loading"
        @onRightClick="onRightClick"
        @onRefresh="$emit('onRefresh')"
        :activeChildItem="currentPage.sortBy"
        @onDelete="deleteFiles"
        @folderCreated="folderCreated"
        @onFavorites="addToFavorites"
        @updateItem="updatePersonalFile"
        @onDownload="onDownload"
        @onMultiDownload="onMultiDownload"
        @onCopy="onCopy"
        @onMove="onMove"
        @onOpenFile="onOpenFile"
        @on-open-file-folder="openFolder"
        @onClicked="
          (selected) => {
            if (selected) {
              selectedCount++;
            } else {
              selectedCount--;
            }
          }
        "
        link="recent"
        :currentPage="
          currentPage.folders && {
            ...currentPage,
            items: [...currentPage.folders, ...currentPage.files],
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
              :link="`/files/personal/recent/${folder.id}`"
              actionUrl="files/personal/folder/action"
              :parent_id="currentPage.parent && currentPage.parent.id"
              showFavorite
              @onRightClick="onItemRightClick"
              @folderCreated="({ name }) => (folder.name = name)"
              @onFavorites="addToFavorites"
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
              @onFavorites="addToFavorites"
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
        @close="properties = false"
        @removeFromShare="removeFromShare"
        :items="propertiesData"
      ></Properties>
    </v-navigation-drawer>

    <!-- <ToggleListGrid /> -->
    <CopyModal
      @onCopyingStarted="onCopyingStarted"
      ref="copyDialog"
    ></CopyModal>
    <FilePasswordModel
      setPasswordUrl="files/personal/files/password"
      @onSuccess="passwordChanged"
    />
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
import CopyModal from "~/components/files/common/CopyModal.vue";
import { mapMutations, mapState } from "vuex";
import FilePreview from "~/components/files/common/filePreview/FilePreview.vue";
import Properties from "~/components/files/common/Properties.vue";
import TopSelectActionMenu from "~/components/files/common/TopSelectActionMenu.vue";
import ShareModal from "../../../../components/files/common/Share/ShareModal.vue";
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
      shareModal: false,
      itemsToShare: [],
      selectedCount: 0,
      isFavorite: false,
      newWindowPreview: false,
      newWindowPreviewItemId: null,
    };
  },

  watch: {},

  computed: {
    ...mapState("files", ["layout"]),
  },

  mounted() {
    this.$root.$on("removeFromShare", this.removeFromShare);
    this.emptySelected();
    this.newWindowPreview = this.$route.query.preview == "true";
    this.newWindowPreviewItemId = this.$route.query?.preview_id;
    this.$nextTick(function () {
      this.newWindowPreview &&
        this.dbClickHandler({ id: this.newWindowPreviewItemId });
    });
  },

  beforeDestroy() {
    // this.emptySelected();
  },

  methods: {
    ...mapMutations("files", ["pushZippingFile"]),
    ...mapMutations("files_select_copy_move", ["changeSelected"]),

    onCustomShortKeys({ srcKey }) {
      if (this.$refs.copyDialog && this.$refs.copyDialog.model) return;
      if (this.properties) return;
      if (this.showPreviewDialog) return;

      if (srcKey == "refresh_page") {
        this.$root.$emit("onRefreshPage");
        return;
      }
      const selectedItems = this.getSelected();
      if (selectedItems && selectedItems.length > 0) {
        let mapped = selectedItems.map(({ id, type }) => ({ id, type }));
        if (srcKey == "ctrlC") {
          this.onCopy(mapped);
        } else if (srcKey == "ctrlX") {
          this.onMove(mapped);
        }
      }
    },

    async onCopyingStarted(body) {
      const fileManagement = new FileManagement(this);
      try {
        this.$refs.copyDialog.setLoader(true);
        const parentName = body.parent ? body.parent.name : "Recent Files";
        const url = "files/personal/auth/relocation";
        const requestBody = { ...body, parentName, url };
        const onSuccess = ({ items }) => {
          this.$emit("onCopySuccess", {
            items,
            parent: body.parent,
            action: body.action,
            actualPrevItems: body.items,
          });
        };
        await fileManagement.pasteItems(requestBody, onSuccess);
        this.$refs.copyDialog.toggleDialog(null);
        this.$refs.copyDialog.setLoader(false);
      } catch (error) {
        this.$refs.copyDialog.setLoader(false);
      }
    },

    /** =======> Common Operation Methods <====== */
    async deleteFiles(deletedItems) {
      const fileManagement = new FileManagement(this);
      try {
        let items = [];
        if (Array.isArray(deletedItems)) items = deletedItems;
        else if (this.selectedItem) {
          const { id, type, parent_id } = this.selectedItem;
          items = [{ id, type, parent_id }];
        } else return;

        const url = `files/personal/delete`;
        let { files, folders } = this.currentPage;
        let removeResult = fileManagement.removeItemsAndReturn({
          items,
          files,
          folders,
        });
        deletedItems = removeResult.removedItems;
        this.currentPage.files = removeResult.files;
        this.currentPage.folders = removeResult.folders;
        await fileManagement.deleteFiles(url, items);
        this.emptySelected();
        this.$refs.filePreviewRef?.onDeleteSuccess();
        this.isDeletingFromPreview = false;
      } catch (error) {
        let { files, folders } = this.currentPage;
        let pushResult = fileManagement.pushItemsAndReturn({
          items: deletedItems,
          files,
          folders,
        });
        this.currentPage.files = pushResult.files;
        this.currentPage.folders = pushResult.folders;
        this.isDeletingFromPreview = false;
      }
    },
    onPreviewClose(isInPipMode) {
      this.showPreviewDialog = false;
      this.newWindowPreview = false;
      !isInPipMode && this.filePreviewKey++;
      if (this.$route.query.preview) {
        this.$router.push("/files/personal/recent?preview=false");
      }
    },
    onCopy(selectedItems) {
      let items = [];
      if (Array.isArray(selectedItems)) {
        items = selectedItems;
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        items = [{ id, type }];
      }
      this.$refs.copyDialog.toggleDialog({ action: "copy", items });
    },

    onMove(selectedItems) {
      let items = [];
      if (Array.isArray(selectedItems)) {
        items = selectedItems;
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        items = [{ id, type }];
      }
      this.$refs.copyDialog.toggleDialog({ action: "move", items });
    },

    onMultiDownload(items) {
      const url = "files/personal/files/download";
      this.pushZippingFile({ url, items: items, context: this });
    },

    onDownload(item) {
      const url = "files/personal/files/download";
      if (item) {
        this.pushZippingFile({ url, item: item, context: this });
      } else if (this.selectedItem) {
        this.pushZippingFile({ url, item: this.selectedItem, context: this });
      }
    },

    async addToFavorites(items) {
      try {
        if (!items) {
          const { id, name, type, favorites } = this.selectedItem;
          items = [{ id, name, type, favorites }];
        }
        items.forEach((item) => {
          item = { ...item, favorites: !item.favorites };
          this.updatePersonalFile(item, "favorites");
        });
        const url = "files/personal/actions/activities";
        const fileManagement = new FileManagement(this);
        await fileManagement.toggleFavorites(url, items);
      } catch (error) {
        items.forEach((item) => this.updatePersonalFile(item, "favorites"));
      }
    },

    passwordChanged(item) {
      this.updatePersonalFile(item, "is_protected");
    },

    updatePersonalFile(item, propertyKey) {
      const { id, type } = item;
      const property = item[propertyKey];
      if (type == "folder") {
        const { folders } = this.currentPage;
        const itemIndex = folders.findIndex((folder) => folder.id == id);
        if (itemIndex != -1) {
          folders[itemIndex][propertyKey] = property;
        }
      } else {
        const { files } = this.currentPage;
        const itemIndex = files.findIndex((file) => file.id == id);
        if (itemIndex != -1) {
          files[itemIndex][propertyKey] = property;
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

    onOpenFile(item) {
      if (item.title == "open") {
        item = this.selectedItem;
      }
      this.dbClickHandler(item);
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
            : `/files/personal/recent/${id}`;

        isNewWindow ? window.open(url, "_blank") : this.$router.push(url);
      }
    },

    /** =======> Common Operation Methods <====== */
    onProperty() {
      this.properties = true;
      this.propertiesData = this.selectedItem;
    },

    dbClickHandler(item) {
      this.showPreviewDialog = true;
      this.$refs.filePreviewRef.fetchFileInfo(item.id);
    },
    //  Display context menu on folder right clicked
    onItemRightClick({ event, item }) {
      this.selectedItem = item;
      const options = {
        items:
          // this.selectedItem.type == "folder"
          //   ?
          [
            "open",
            "open_in_new_window",
            "download",
            `${
              this.selectedItem.favorites
                ? "remove_from_favorites"
                : "add_to_favorites"
            }`,
            "share_to",
            "rename",
            "copy_to",
            "move_to",
            "move_to_trashed",
            "properties",
          ],
        // : [
        //     "download",
        //     `${
        //       this.selectedItem.favorites
        //         ? "remove_from_favorites"
        //         : "add_to_favorites"
        //     }`,
        //     "share_to",
        //     "rename",
        //     "copy_to",
        //     "move_to",
        //     "move_to_trashed",
        //     "properties",
        //   ],
        showMenu: true,
        positionX: event.clientX,
        positionY: event.clientY,
        favorites: item.favorites,
      };
      this.$root.$emit("toggleContextMenu", options);
    },

    folderCreated(folder) {
      this.currentPage?.folders?.unshift(folder);
    },

    /** =======> Current Page Methods <====== */

    // display context menu on current page right clicked
    onRightClick(event) {
      event.preventDefault();
      const options = {
        showMenu: true,
        items: ["sort_by", "thumbnail", "list", "refresh"],
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

    toggleShareModal() {
      this.shareModal = !this.shareModal;
    },

    onShare(selected) {
      this.shareModal = true;
      if (selected?.length > 0) {
        this.itemsToShare = selected;
      } else {
        this.itemsToShare = [this.selectedItem.id];
      }
    },

    shareSuccess(share_items, file_shares) {
      let items = [...this.currentPage.folders, ...this.currentPage.files];
      items.forEach((item) => {
        if (share_items.includes(item.id)) {
          var foundShares = [];
          file_shares.forEach((_item) => {
            if (_item.shareable_id == item.id) {
              foundShares.push(_item);
            }
          });
          if (item?.file_shares?.length > 0) {
            foundShares.forEach((_item2) => {
              let foundIndex = item.file_shares.findIndex(
                (_item3) => _item3.shared_to.id == _item2.shared_to.id
              );
              if (foundIndex == -1) {
                item.file_shares.push(_item2);
              } else {
                item.file_shares[foundIndex] = _item2;
              }
            });
          } else {
            item.file_shares = foundShares;
          }
        }
      });
    },

    removeFromShare(info, item) {
      Alert.removeDialogAlert(
        this,
        () => {
          let fileManagement = new FileManagement(this);
          fileManagement.removeFromShare(info, item, [
            ...this.currentPage.folders,
            ...this.currentPage.files,
          ]);
        },
        "",
        "yes_delete"
      );
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
    CopyModal,
    ToggleListGrid,
    ListView,
    RightClickMenu,
    FilePreview,
    Properties,
    TopSelectActionMenu,
    ShareModal,
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
