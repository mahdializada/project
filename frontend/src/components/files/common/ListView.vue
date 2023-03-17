<template>
  <div
    @contextmenu.stop="(event) => $emit('onRightClick', event)"
    class="overflow-x-auto mt-2 file-list-rows"
  >
    <div class="pe-2">
      <v-row :class="`ma-0 align-center background`" style="min-width: 900px">
        <v-col cols="4" class="py-0 ps-1">
          <div class="d-flex align-center">
            <div
              class="list-view-header-item nowrap text-uppercase cursor-pointer ps-6"
            >
              {{ $tr("name") }}
              <v-icon
                @click="handleSortBy('name')"
                size="small"
                class="ms-1"
                :color="
                  currentPage.sortBy == 'name' && isSortedByName
                    ? 'primary'
                    : ''
                "
              >
                fa-sort
              </v-icon>
            </div>
          </div>
        </v-col>
        <v-col cols="8" class="py-0 px-0">
          <v-row class="ma-0">
            <v-col cols="4" class="d-flex align-center py-0">
              <div
                class="list-view-header-item nowrap text-uppercase cursor-pointer"
              >
                {{ $tr("last_modified_by") }}
                <v-icon
                  @click="handleSortBy('date_modified')"
                  size="small"
                  class="ms-1"
                  :color="
                    currentPage.sortBy == 'date_modified' ? 'primary' : ''
                  "
                  >fa-sort</v-icon
                >
              </div>
            </v-col>
            <v-col cols="8" class="py-0">
              <v-row class="ma-0">
                <v-col class="py-0 d-flex align-center" cols="4">
                  <div
                    class="list-view-header-item nowrap text-uppercase cursor-pointer"
                  >
                    {{ $tr("size") }}
                    <v-icon
                      @click="handleSortBy('size')"
                      size="small"
                      class="ms-1"
                      :color="currentPage.sortBy == 'size' ? 'primary' : ''"
                      >fa-sort</v-icon
                    >
                  </div>
                </v-col>
                <v-col class="py-0 d-flex align-center" cols="4">
                  <div
                    class="list-view-header-item nowrap text-uppercase cursor-pointer"
                  >
                    {{ $tr("type") }}
                    <!-- <v-icon size="small" class="ms-1">fa-sort</v-icon> -->
                  </div>
                </v-col>
                <v-col class="py-0 d-flex align-center" cols="4">
                  <div
                    class="list-view-header-item nowrap text-uppercase cursor-pointer"
                  >
                    {{ $tr("actions") }}
                  </div>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
    <RightClickMenu
      @onOpen="openFolder"
      @onOpenFile="onOpenFile"
      @onRename="onItemRename"
      @onFavorites="addToFavorites"
      @onDelete="deleteFile"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @onCopy="onCopy"
      @onRestore="onRestore"
      @onMove="onMove"
      @onProperty="onProperty"
      :activeChildItem="$attrs.activeChildItem"
      :selected="getSelected()"
    />

    <div v-if="loading">
      <div v-for="item in 10" :key="item" class="px-3 my-1 skeleton__container">
        <v-row :class="`ma-0 align-center background`" style="min-width: 900px">
          <v-col cols="4" class="py-0 ps-1">
            <div class="d-flex align-center">
              <v-skeleton-loader
                class="name__loader"
                type="avatar, chip"
              ></v-skeleton-loader>
            </div>
          </v-col>
          <v-col cols="8" class="py-0 px-0">
            <v-row class="ma-0">
              <v-col cols="4" class="d-flex align-center py-0">
                <v-skeleton-loader
                  class="modifiy__loader"
                  type="chip"
                ></v-skeleton-loader>
              </v-col>
              <v-col cols="8" class="py-0">
                <v-row class="ma-0">
                  <v-col class="py-0 d-flex align-center" cols="4">
                    <v-skeleton-loader
                      class="size__loader"
                      type="chip"
                    ></v-skeleton-loader>
                  </v-col>
                  <v-col class="py-0 d-flex align-center" cols="4">
                    <v-skeleton-loader
                      class="type__loader"
                      type="chip"
                    ></v-skeleton-loader>
                  </v-col>
                  <v-col class="py-0 d-flex align-center" cols="4">
                    <v-skeleton-loader
                      class="action__loader"
                      type="avatar, avatar,avatar"
                    ></v-skeleton-loader>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </div>
    </div>

    <v-virtual-scroll
      v-else
      :bench="2"
      :items="currentPage.items"
      :height="this.$vuetify.breakpoint.height - 340"
      item-height="70"
      @scroll="onScroll"
      style="min-width: 900px"
      :key="changeListKey"
    >
      <template v-slot:default="{ index, item }">
        <div>
          <Folder
            v-if="item.activeCreateFolder"
            :renameFolderProps="true"
            :hide-details="true"
            actionUrl="files/personal/folder/action"
            :parent_id="currentPage.parent && currentPage.parent.id"
            @folderCreated="folderCreated"
          />
          <v-row
            v-else
            @click="selectFileOrFolder(item.id)"
            @contextmenu.stop="(event) => onItemRightClick(event, item)"
            :class="`file-list-row ma-0 align-center cursor-pointer ${
              index % 2 > 0 ? 'background' : ''
            }`"
            style="min-width: 800px"
          >
            <v-col cols="4" class="py-0 ps-1">
              <div class="d-flex align-center">
                <div>
                  <Folder
                    :ref="`folder` + item.id"
                    v-if="item.type == 'folder'"
                    :folder="item"
                    :hide-details="true"
                    :link="`/files/personal/${link}/${item.id}`"
                    actionUrl="files/personal/folder/action"
                    :parent_id="currentPage.parent && currentPage.parent.id"
                    @folderCreated="({ name }) => (item.name = name)"
                    @onClicked="(value) => $emit('onClicked', value)"
                  />
                  <File
                    :ref="`file` + item.id"
                    v-else
                    :file="item"
                    :hide-details="true"
                    actionUrl="files/personal/files/rename"
                    :parent_id="currentPage.parent && currentPage.parent.id"
                    @itemRenamed="({ name }) => (item.name = name)"
                    @dbClick="onOpenFile"
                    @onClicked="(value) => $emit('onClicked', value)"
                  />
                </div>
              </div>
            </v-col>
            <v-col cols="8" class="py-0 px-0">
              <v-row class="ma-0">
                <v-col cols="4" class="d-flex align-center">
                  <div v-if="item.created_by" class="nowrap">
                    {{ item.created_by.firstname }}
                    {{ item.created_by.lastname }}
                  </div>
                </v-col>
                <v-col cols="8">
                  <v-row class="ma-0">
                    <v-col class="py-0 d-flex align-center" cols="4">
                      <div class="nowrap">
                        {{
                          getFileSize(
                            item.type == "folder" ? item.info.size : item.size
                          )
                        }}
                      </div>
                    </v-col>

                    <v-col class="py-0 d-flex align-center" cols="4">
                      <div class="nowrap text-capitalize">
                        {{
                          item.type == "folder" ? $tr("folder") : item.extension
                        }}
                      </div>
                    </v-col>

                    <v-col class="py-0 d-flex align-center" cols="4">
                      <FileActions
                        :item="item"
                        :trash="trash"
                        :isShared="isShared"
                        @restoreFile="onRestore"
                        @deleteTrash="deleteFile"
                        @toggleFavorite="toggleFavorite"
                        @deleteFile="deleteFile"
                      />
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </div>
      </template>
    </v-virtual-scroll>

    <v-navigation-drawer
      v-model="properties"
      absolute
      temporary
      right
      width="450"
    >
      <Properties
        @close="properties = false"
        :items="propertiesData"
      ></Properties>
    </v-navigation-drawer>
  </div>
</template>
<script>
import Folder from "./Folder.vue";
import File from "./File.vue";
import FileActions from "./FileActions.vue";
import RightClickMenu from "~/components/files/common/RightClickMenu.vue";
import Properties from "~/components/files/common/Properties.vue";
import { mapState } from "vuex";

export default {
  props: {
    isShared: false,
    link: String,
    loading: {
      type: Boolean,
      default: false,
    },
    currentPage: {
      type: Object,
      default: () => ({
        sortBy: "name",
        files: [],
        folders: [],
        items: [],
      }),
    },
    trash: {
      type: Boolean,
      default: false,
    },
    trashChildMenu: {
      type: Object,
    },
  },
  data() {
    return {
      selectAllModel: false,
      changeListKey: 0,
      isSortedByName: false,
      selectedItem: null,
      properties: false,
      propertiesData: null,
    };
  },

  watch: {
    createFolder: function (value) {
      if (value) {
        this.currentPage.items.unshift({ activeCreateFolder: true });
        const element = document.querySelector(".v-virtual-scroll");
        if (element) {
          element.scrollTo({ top: 0, duration: 300, behavior: "smooth" });
        }
      } else {
        if (this.currentPage?.items?.length) {
          const item = this.currentPage.items[0];
          if (item.activeCreateFolder) {
            this.currentPage.items.shift();
          }
        }
      }
      this.changeListKey++;
    },
  },

  computed: {
    ...mapState("files", ["createFolder"]),
  },

  methods: {
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
    onProperty() {
      this.properties = true;
      this.propertiesData = this.selectedItem;
    },

    onRestore(selectedItems) {
      let items = [];
      if (Array.isArray(selectedItems)) {
        items = selectedItems;
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        items = [{ id, type }];
      }
      this.$emit("onRestore", items);
    },
    onCopy(selectedItems) {
      let items = [];
      if (Array.isArray(selectedItems)) {
        items = selectedItems;
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        items = [{ id, type }];
      }
      this.$emit("onCopy", items);
    },

    onMove(selectedItems) {
      let items = [];
      if (Array.isArray(selectedItems)) {
        items = selectedItems;
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        items = [{ id, type }];
      }
      this.$emit("onMove", items);
    },
    handleSortBy(title) {
      if (title == "name") {
        this.isSortedByName = true;
      } else {
        this.isSortedByName = false;
      }
      this.$root.$emit("onSortChanged", { title });
    },
    onMultiDownload(items) {
      this.$emit("onMultiDownload", items);
    },

    onDownload(item) {
      if (item) {
        this.$emit("onDownload", item);
      } else if (this.selectedItem) {
        this.$emit("onDownload", this.selectedItem);
      }
    },

    addToFavorites(items) {
      if (items) {
        this.$emit("onFavorites", items);
      } else {
        const { id, name, type, favorites } = this.selectedItem;
        this.$emit("onFavorites", [{ id, name, type, favorites }]);
      }
    },

    // rename items
    onItemRename() {
      const { id, type } = this.selectedItem;
      let itemRef = "file" + id;
      if (type == "folder") itemRef = "folder" + id;
      const selectedItem = this.$refs[itemRef];
      if (Array.isArray(selectedItem)) {
        selectedItem[0].toggleNameInput();
      } else if (selectedItem) {
        selectedItem.toggleNameInput();
      }
    },

    //  Open Folder in the current Page or In New Tab
    openFolder({ isNewWindow }) {
      const item = this.selectedItem;
      this.$emit("on-open-file-folder", { isNewWindow, item });
      // if (id) {
      // 	const url = `/files/personal/drive/${id}`;
      // 	if (isNewWindow) {
      // 		window.open(url, "_blank").focus();
      // 	} else {
      // 		this.$router.push(url);
      // 	}
      // }
    },

    onOpenFile(item) {
      item = item || this.selectedItem;
      this.$emit("onOpenFile", item);
    },

    // delete file or filder
    async deleteFile(items) {
      if (Array.isArray(items)) {
        this.$emit("onDelete", items);
      } else if (this.selectedItem) {
        const { id, type } = this.selectedItem;
        this.$emit("onDelete", [{ id, type }]);
      }
    },

    folderCreated(folder) {
      if (Array.isArray(this.currentPage.items)) {
        this.currentPage.items[0] = folder;
      } else {
        this.currentPage.items = [folder];
      }
      this.$emit("folderCreated", folder);
    },

    toggleFavorite(body) {
      this.$emit("onFavorites", body);
    },

    onScroll(event) {
      this.$emit("onScroll", event);
    },

    //  Display context menu on folder right clicked
    onItemRightClick(event, item) {
      if (event && item) {
        event.preventDefault();
        this.selectedItem = item;
        const favorites = item.favorites
          ? "remove_from_favorites"
          : "add_to_favorites";

        const openItem = item.type == "folder" ? "open" : "open_file";

        const options = this.isShared
          ? {
              items:
                this.selectedItem.type == "folder"
                  ? [
                      openItem,
                      "open_in_new_window",
                      "download",
                      this.selectedItem.user_id == this.$auth.user.id ||
                      this.currentPage.parent == null
                        ? "rename"
                        : "nothing",
                      "copy_to",
                      this.selectedItem.user_id == this.$auth.user.id ||
                      this.currentPage.parent == null
                        ? "delete"
                        : "nothing",
                      "properties",
                    ]
                  : [
                      "download",
                      this.selectedItem.user_id == this.$auth.user.id ||
                      this.currentPage.parent == null
                        ? "rename"
                        : "nothing",
                      "copy_to",
                      this.selectedItem.user_id == this.$auth.user.id &&
                      this.currentPage.parent !== null
                        ? "delete"
                        : "nothing",
                      "properties",
                    ],
              showMenu: true,
              positionX: event.clientX,
              positionY: event.clientY,
            }
          : {
              items: this.trash
                ? ["restore", "delete", "properties"]
                : [
                    openItem,
                    "open_in_new_window",
                    "download",
                    favorites,
                    "share_to",
                    "rename",
                    "copy_to",
                    "move_to",
                    "move_to_trashed",
                    "properties",
                  ],
              showMenu: true,
              positionX: event.clientX,
              positionY: event.clientY,
              favorites: item.favorites,
            };
        this.$root.$emit("toggleContextMenu", options);
      }
    },

    selectFileOrFolder(id) {
      if (this.$refs["file" + id]) {
        this.$refs["file" + id].onFileClicked();
      } else if (this.$refs["folder" + id]) {
        this.$refs["folder" + id].onFolderClicked();
      }
    },

    getFileSize(totalSize) {
      let sizes = ["Bytes", "KB", "MB", "GB", "TB"];
      if (totalSize == 0) return "0 Byte";
      let i = parseInt(Math.floor(Math.log(totalSize) / Math.log(1024)));
      return Math.round(totalSize / Math.pow(1024, i), 2) + " " + sizes[i];
    },
  },
  components: { Folder, File, FileActions, RightClickMenu, Properties },
};
</script>

<style lang="scss">
.rtl {
  .skeleton__container {
    .name__loader {
      .v-skeleton-loader__avatar {
        margin-left: 20px;
      }
    }
    .action__loader {
      .v-skeleton-loader__avatar {
        margin-left: 5px;
      }
    }
  }
}
.skeleton__container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  .name__loader {
    display: flex;
    align-items: center;
    .v-skeleton-loader__avatar {
      margin-right: 20px;
    }
  }
  .action__loader {
    display: flex;
    align-items: center;
    .v-skeleton-loader__avatar {
      margin-right: 5px;
    }
  }
  .v-skeleton-loader__avatar {
    height: 40px;
    width: 40px;
  }
  .v-skeleton-loader__chip {
    height: 25px;
  }
}
.nowrap {
  white-space: nowrap;
  text-overflow: ellipsis;
}
.list-view-header-item {
  font-weight: 500;
  padding: 8px 0px;
}
</style>
