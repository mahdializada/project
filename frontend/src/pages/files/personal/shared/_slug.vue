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
      slug-name="shared"
      :is-root="currentPage.parent == null"
      :is-deleting="isDeletingFromPreview"
      @on-close="onPreviewClose"
      @on-delete="onPreviewDelete"
      @on-copy="onCopy"
      @on-download="onDownload"
      @on-leave-pip="showPreviewDialog = true"
    />

    <TopSelectActionMenu
      :parent="currentPage.parent"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @selectAll="selectAll"
      :isShare="true"
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
      @onCopy="onCopy"
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
        @folderCreated="folderCreated"
        @updateItem="updatePersonalFile"
        @onDownload="onDownload"
        @onMultiDownload="onMultiDownload"
        @onCopy="onCopy"
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
        :isShared="true"
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
              v-if="createFolder"
              :renameFolderProps="true"
              actionUrl="files/personal/folder/action"
              :parent_id="currentPage.parent && currentPage.parent.id"
              @folderCreated="folderCreated"
            />
            <Folder
              v-for="(folder, folderIndex) in currentPage.folders"
              :key="folder.id"
              :ref="folder.id"
              :folder="folder"
              :index="folderIndex"
              :link="`/files/personal/shared/${folder.id}`"
              actionUrl="files/personal/folder/action"
              :parent_id="currentPage.parent && currentPage.parent.id"
              @onRightClick="onItemRightClick"
              @folderCreated="
                ({ name, updated_at }) => {
                  folder.name = name;
                  folder.updated_at = updated_at;
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
              :showCreatedBy="true"
              :isShare="true"
            />
          </div>
        </div>
        <div v-else-if="createFolder" class="content-section">
          <SectionTitle title="Folders" />
          <div class="d-flex flex-wrap px-2">
            <Folder
              v-if="createFolder"
              :renameFolderProps="true"
              actionUrl="files/personal/folder/action"
              :parent_id="currentPage.parent && currentPage.parent.id"
              @folderCreated="folderCreated"
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
              :isShare="true"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- <ToggleListGrid /> -->
    <CopyModal
      @onCopyingStarted="onCopyingStarted"
      ref="copyDialog"
    ></CopyModal>
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
import CopyModal from "~/components/files/common/CopyModal.vue";
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

  watch: {
    createFolder: function (value) {
      if (value) {
        const element = document.querySelector(".file-content");
        if (element) {
          element.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        }
      }
    },
  },

  computed: {
    ...mapState("files", ["createFolder", "layout"]),
  },

  mounted() {
    this.emptySelected();
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
    ...mapMutations("files_select_copy_move", ["changeSelected"]),

    /** =======> Common Operation Methods <====== */

    onPreviewDelete(deletedItem) {
      this.selectedItem = deletedItem;
      this.deleteFiles(null);
    },

    async deleteFiles(deletedItems) {
      let isMulti = Array.isArray(deletedItems);
      let type = isMulti ? "multi" : this.selectedItem?.type;
      var items = this.getDeletedItems(isMulti ? deletedItems : null);
      if (this.currentPage.parent == null) {
        if (isMulti || this.selectedItem?.type == "folder") {
          // root multi
          Alert.shareRemoveAlert(
            this,
            this.$tr("remove_shared_item", this.$tr(isMulti ? "items" : type)),
            `<small class='d-block' style='color: var(--v-error-base);white-space: nowrap;'>
                ${this.$tr(
                  "only_removes_item_from_shared_drive",
                  this.$tr(isMulti ? "items" : type)
                )}
              </small>
              ${this.$tr(
                "would_you_like_to_remove_files_created_inside_item",
                this.$tr(isMulti ? "items" : type)
              )}`,
            () => {
              this.rootDelete(items, false);
            },
            () => {
              this.rootDelete(items, true);
            },
            this.$tr("dont_delete_my_files"),
            this.$tr("delete_my_files")
          );
        } else if (this.selectedItem) {
          // root single delete file
          Alert.shareRemoveAlert(
            this,
            this.$tr("remove_shared_item", this.$tr("file")),
            this.$tr("only_removes_item_from_shared_drive", this.$tr("file")),
            async () => {
              this.rootDelete(items, true);
            },
            () => {},
            this.$tr("yes_delete"),
            "",
            false
          );
        }
      } else {
        // inside delete
        Alert.shareRemoveAlert(
          this,
          this.$tr("remove_item", this.$tr(isMulti ? "files" : type)),
          this.$tr(
            "permanently_delete_item_for_everyone",
            this.$tr(isMulti ? "files" : type)
          ),
          async () => {
            this.insideDelete(items, type);
          },
          () => {},
          this.$tr("yes_delete"),
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

    async deleteSubmit(body) {
      const res = await this.$axios.post("/files/personal/share/delete", body);
      return res;
    },

    async rootDelete(items, delete_files) {
      let removedItems = [];
      try {
        removedItems = this.removeDeletedItems(items);
        this.emptySelected();
        await this.deleteSubmit({
          action: "remove-root",
          delete_files,
          items,
        });
        				this.$toasterNA("green", this.$tr('successfully_deleted'));
;
        this.$refs.filePreviewRef?.onDeleteSuccess();
        this.isDeletingFromPreview = false;
      } catch (e) {
        this.addDeletedItems(removedItems);
        // this.$toastr.e(this.$tr("can't_delete_file"));
        this.$toasterNA("red",this.$tr("can't_delete_file"));

        this.isDeletingFromPreview = false;
      }
      // root multi remove without delete self files
    },

    async insideDelete(items, type) {
      let removedItems = [];
      try {
        let res = await this.deleteSubmit({
          action: "remove-inside",
          items: items,
        });

        let nonDeletableFoldersRes = res?.data?.nonDeletableFolders; // server side deletable items
        if (nonDeletableFoldersRes?.length > 0) {
          // this.$toastr.e(
          //   type == "multi"
          //     ? this.$tr(
          //         "could_not_delete_items_because",
          //         this.$tr("some files")
          //       )
          //     : type == "folder"
          //     ? this.$tr(
          //         "could_not_delete_items_because",
          //         this.$tr("selected_item", this.$tr("folder"))
          //       )
          //     : type == "file"
          //     ? this.$tr("could_not_delete_item_because", this.$tr("file"))
          //     : ""
          // );
        this.$toasterNA("red",type == "multi"
              ? this.$tr(
                  "could_not_delete_items_because",
                  this.$tr("some files")
                )
              : type == "folder"
              ? this.$tr(
                  "could_not_delete_items_because",
                  this.$tr("selected_item", this.$tr("folder"))
                )
              : type == "file"
              ? this.$tr("could_not_delete_item_because", this.$tr("file"))
              : "");

        }
        // filter client side deletable items from the server side non deleteable items
        let deletedItems = items?.filter(
          (item) =>
            nonDeletableFoldersRes?.find((item2) => item.id == item2.id) ==
            undefined
        );
        removedItems = this.removeDeletedItems(deletedItems); // remove finale deletable Items from dom
        this.emptySelected();
        this.$refs.filePreviewRef?.onDeleteSuccess();
        this.isDeletingFromPreview = false;
        				this.$toasterNA("green", this.$tr('successfully_deleted'));
;
      } catch (e) {
        this.isDeletingFromPreview = false;
        // this.$toastr.e(this.$tr("can't_delete_file"));
        this.$toasterNA("red",this.$tr("can't_delete_file"));

        this.addDeletedItems(removedItems); // if exception add remvoed files back to dom
      }
    },

    async onCopyingStarted(body) {
      const fileManagement = new FileManagement(this);
      try {
        this.$refs.copyDialog.setLoader(true);
        const parentName = body.parent ? body.parent.name : "My Drive";
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

    onMultiDownload(items) {
      const url = "files/personal/files/download";
      this.pushZippingFile({ url, items: items, context: this });
    },

    onPreviewClose(isInPipMode) {
      this.showPreviewDialog = false;
      this.newWindowPreview = false;
      !isInPipMode && this.filePreviewKey++;
      if (this.$route.query.preview == "true") {
        this.$router.push("/files/personal/shared?preview=false");
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
      item.seen = true;
      if (type === "file" && !isNewWindow) return this.dbClickHandler(item);
      if (id) {
        const url =
          type === "file"
            ? `${this.$route.path}?preview=true&preview_id=${id}`
            : `/files/personal/shared/${id}`;
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
      file.seen = true;
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
          this.selectedItem.user_id == this.$auth.user.id &&
          this.currentPage.parent !== null
            ? "rename"
            : "nothing",
          "copy_to",
          this.selectedItem.user_id == this.$auth.user.id ||
          this.currentPage.parent == null
            ? "delete"
            : "nothing",
          "properties",
        ],
        showMenu: true,
        positionX: event.clientX,
        positionY: event.clientY,
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
        items: [
          this.currentPage.parent &&
          this.currentPage.parent.permission == "read_&_write"
            ? "create_folder"
            : "nothing",
          this.currentPage.parent &&
          this.currentPage.parent.permission == "read_&_write"
            ? "upload_file"
            : "nothing",
          this.currentPage.parent &&
          this.currentPage.parent.permission == "read_&_write"
            ? "upload_folder"
            : "nothing",
          "sort_by",
          "thumbnail",
          "list",
          "refresh",
        ],
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
    CopyModal,
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
