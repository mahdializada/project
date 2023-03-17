<template>
  <div class="file__topbar-item d-flex align-center">
    <div v-if="!hideUploadBtns" class="file__topbar-left">
      <form ref="fileInputForm" class="d-none">
        <input
          ref="file_uploader"
          class="d-none"
          type="file"
          multiple
          @change="onFileChanged"
        />
        <input
          ref="folder_uploader"
          class="d-none"
          type="file"
          multiple
          directory=""
          webkitdirectory=""
          moxdirectory=""
          @change="
            (e) => {
              onFileChanged(e, true);
            }
          "
        />
        <input ref="reset" type="reset" value="reset" class="d-none" />
      </form>

      <MenuItems
        v-if="showNewButton"
        text="new"
        icon="mdi-plus"
        :items="createMenuItems"
      />
    </div>

    <div class="file__topbar-right d-flex align-center">
      <v-divider style="border-width: thin" vertical class="mx-1" />
      <div class="right__container">
        <v-menu offset-y :close-on-content-click="false" offset-x>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-if="showFilterBtn"
              @click="
                changeFilter({
                  key: 'is_opened',
                  value: !filters.is_opened,
                })
              "
              color="primary"
              icon
              v-bind="attrs"
              v-on="on"
            >
              <v-icon> {{ iconFilter }} </v-icon>
            </v-btn>
          </template>

          <file-filters @submitFilter="submitFilter" @clearFilter="clearFilter">
          </file-filters>
        </v-menu>

        <MenuItems
          v-if="showSortBtn"
          :activeItem="currentSortItem"
          icon="mdi-sort-bool-descending-variant"
          :items="sortMenuItems"
        />
        <MenuItems v-if="showMore" icon="mdi-dots-vertical" :items="optionMenuItems" />
      </div>
    </div>
    <SelectedFileModal ref="SelectedFileModal" />
  </div>
</template>

<script>
import MenuItems from "./MenuItems.vue";

import { mapMutations, mapState } from "vuex";
import SelectedFileModal from "./SelectedFileModal.vue";
import FileFilters from "./FileFilters.vue";
export default {
  components: { MenuItems, SelectedFileModal, FileFilters },
  props: {
    showNewButton: {
      type: Boolean,
      default: true,
    },
    showMore: {
      type: Boolean,
      default: true,
    },
    hideUploadBtns: {
      type: Boolean,
      default: false,
    },
    currentSortItem: {
      type: String,
      default: "name",
    },
    showFilterBtn: {
      type: Boolean,
      default: true,
    },
    showSortBtn: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      /** OPTION MENU DATA */
      optionMenuItems: [
        {
          icon: "mdi-refresh",
          title: "refresh",
          divider: true,
          click: this.onRefresh,
        },
        {
          icon: "mdi-grid",
          title: "thumbnail",
          click: this.onchangeLayout,
        },
        {
          icon: "mdi-text-box-outline",
          title: "list",
          click: this.onchangeLayout,
        },
      ],
      /** SORT MENU DATA */
      sortMenuItems: [
        {
          icon: "mdi-sort-alphabetical-descending",
          title: "name",
          click: this.handleSortBy,
        },
        {
          icon: "mdi-scale",
          title: "size",
          click: this.handleSortBy,
        },
        {
          icon: "mdi-sort-calendar-ascending",
          title: "date_modified",
          click: this.handleSortBy,
        },
      ],

      /** CREATE MENU DATA */
      uploadFile: false,
      createMenuItems: [
        {
          icon: "mdi-folder-plus",
          title: "create_folder",
          click: this.handleCreateFolder,
          divider: true,
        },
        {
          icon: "mdi-tray-arrow-up",
          title: "upload_file",
          click: this.handleFileImport,
        },
        {
          icon: "mdi-folder-upload",
          title: "upload_folder",
          click: this.handleFolderImport,
        },
      ],

      foldersInside: [],
      filterItems: null,
      iconFilter: "mdi-filter",
    };
  },

  mounted() {
    this.$root.$on("onCreateMenuClicked", (title) => {
      if (title == "create_folder") this.handleCreateFolder();
      else if (title == "upload_file") this.handleFileImport();
      else if (title == "upload_folder") this.handleFolderImport();
    });
  },

  computed: {
    ...mapState("files", ["filters", "selectedFiles", "folders"]),
  },

  watch: {
    "filterItems.selectedfileTypesId": function (val) {
      if (val == "any") {
        this.iconFilter = "mdi-filter";
      } else {
        this.iconFilter = "mdi-filter-check";
      }
    },
    "filterItems.selecteddateModifiedsId": function (val) {
      if (val == "any_time") {
        this.iconFilter = "mdi-filter";
      } else {
        this.iconFilter = "mdi-filter-check";
      }
    },
  },

  methods: {
    ...mapMutations("files", [
      "changeFilter",
      "addSelectedFile",
      "toggleCreateFolder",
      "addFolder",
      "changeLayout",
    ]),

    onchangeLayout({ title }) {
      if (title == "list") {
        this.changeLayout("list");
      } else if (title == "thumbnail") {
        this.changeLayout("grid");
      }
    },

    onRefresh() {
      this.$root.$emit("onRefreshPage");
    },

    /** OPTION MENU METHODS */

    submitFilter(item) {
      this.$emit("submitFilter", item);
      this.filterItems = item;
    },

    clearFilter(item) {
      this.$emit("clearFilter");
      this.filterItems = item;
    },

    onDownloadAll() {
      this.$root.$emit("onDownloadAll");
    },
    onPinClickAll() {
      this.$root.$emit("onPinClickAll");
    },

    onRestoreAll() {
      this.$root.$emit("onRestoreAll");
    },
    onDeleteAll() {
      this.$root.$emit("onDeleteAll");
    },

    onFavoritesAll() {
      this.$root.$emit("onFavoritesAll");
    },

    onShareClick() {
      this.$root.$emit("onShare");
    },
    onCut() {
      this.$root.$emit("onCut");
    },
    onCopy() {
      this.$root.$emit("onCopy");
    },
    onSelect() {
      this.$root.$emit("onSelect");
    },

    /** SORT MENU METHODS */

    handleSortBy(item) {
      this.$root.$emit("onSortChanged", item);
    },

    /** CREATE MENU METHODS */
    handleCreateFolder() {
      this.toggleCreateFolder(true);
    },

    handleFileImport() {
      this.uploadFile = true;
      if (this.$refs.file_uploader) {
        this.$refs.file_uploader.click();
      }
    },
    handleFolderImport() {
      this.uploadFile = false;
      this.$refs.folder_uploader.click();
    },

    isInFolderArr(name, path, arr) {
      let res = arr.find((item) => {
        return item.name == name && item.path == path;
      });
      return res;
    },

    onFileChanged(event, isFolder) {
      let foldersArrId = this.generateID();
      if (event && event.target.files.length > 0) {
        for (let i = 0; i < event.target.files.length; i++) {
          let path = event.target.files[i].webkitRelativePath;
          let file = event.target.files[i];
          if (isFolder) {
            let upload_folders = path.split("/");
            for (let i = 0; i < upload_folders.length - 1; i++) {
              let name = upload_folders[i];
              var folder = {
                id: this.generateID(),
                name,
                path: upload_folders.slice(0, i + 1).join("/"),
              };
              var res = this.isInFolderArr(
                folder.name,
                folder.path,
                this.foldersInside
              );
              if (res == undefined) {
                let temp_path = folder.path.split("/");
                let temp_name = upload_folders[i - 1];
                let parent = this.isInFolderArr(
                  temp_name,
                  temp_path.slice(0, temp_path.length - 1).join("/"),
                  this.foldersInside
                );
                if (parent !== undefined) {
                  folder.parent_temp_id = parent.id;
                }
                res = folder;
                this.foldersInside.push(res);
              }
            }
          }
          let temp_file = {
            id: this.generateID(),
            file,
            name: file.name,
            status: null,
            showFormat: file.type.indexOf("video") == 0,
            size: file.size,
            quality: "auto",
            progress: 0,
            folders: event.target.files[i].webkitRelativePath.split("/"),
            path: path.substr(0, path.lastIndexOf("/")),
            parent_id: this.$route.params.slug ? this.$route.params.slug : null,
            parent_temp_id: res ? res.id : null,
            folder_chunk_id: foldersArrId,
          };
          this.addSelectedFile(temp_file);
        }
      }
      this.addFolder({ id: foldersArrId, folders: this.foldersInside });
      this.foldersInside = [];
      this.$refs.SelectedFileModal.toggleModal();
      this.$refs.reset.click();
    },

    generateID() {
      return (
        "Id" +
        Math.floor(
          (Date.now() *
            Math.floor(
              Math.random() * Math.floor(Math.random() * Date.now())
            )) /
            (Math.random() * 1000000000)
        )
      );
    },
  },
};
</script>

<style></style>
