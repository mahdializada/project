<template>
  <v-dialog v-model="model" persistent width="550">
    <v-card>
      <v-card-title
        class="d-flex align-center justify-space-between py-1 px-2 mb-2"
      >
        <p class="grey--text lighten-1" style="margin-bottom: 0 !important">
          {{
            copyableData.action == "copy"
              ? $tr("copy_file_to")
              : $tr("move_file_to")
          }}
        </p>
        <v-btn @click="toggleDialog" icon> <v-icon>mdi-close</v-icon> </v-btn>
      </v-card-title>
      <div class="px-2 d-flex flex-xs-wrap flex-md-nowrap mb-2 align-center">
        <v-form class="w-full mx-1">
          <v-text-field
            hide-details
            rounded
            background-color="surface "
            class="pa-0 ma-0 custom-search white"
            v-model="searchData"
            color="white"
            :placeholder="$tr('search') + '...'"
          >
          </v-text-field>
        </v-form>
        <v-btn icon class="surface">
          <v-icon color="primary" @click.stop="createNewFolder">
            mdi-folder-plus
          </v-icon>
        </v-btn>
      </div>
      <div style="height: 3px; min-width: 100%" class="grey lighten-2"></div>
      <v-card-text class="pa-0">
        <div
          style="height: 500px"
          v-if="!isFetchingFolders && folders.length < 1"
          class="d-flex align-center justify-center flex-column h-full"
        >
          <div class="mb-1 text--disabled">
            <svg
              fill="var(--v-primary-base)"
              width="60"
              height="60"
              viewBox="0 0 576 512"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M572.6 270.3l-96 192C471.2 473.2 460.1 480 447.1 480H64c-35.35 0-64-28.66-64-64V96c0-35.34 28.65-64 64-64h117.5c16.97 0 33.25 6.742 45.26 18.75L275.9 96H416c35.35 0 64 28.66 64 64v32h-48V160c0-8.824-7.178-16-16-16H256L192.8 84.69C189.8 81.66 185.8 80 181.5 80H64C55.18 80 48 87.18 48 96v288l71.16-142.3C124.6 230.8 135.7 224 147.8 224h396.2C567.7 224 583.2 249 572.6 270.3z"
              />
            </svg>
          </div>
          <div style="max-width: 250px" class="text-center">
            <p class="ma-0 text--secondary" style="font-size: 14px">
              {{ $tr("no_folders_found") }}
            </p>
            <p class="ma-0 text--secondary" style="font-size: 14px">
              {{ $tr("to_create_new_folder_click_on_icon_folder_near_search") }}
            </p>
          </div>
        </div>

        <div
          v-else
          style="height: 500px"
          class="overflow-auto folder__tree__container"
        >
          <Personalfolder
            :folders="folders"
            :searchValue="searchData"
            @folderCreated="onFolderCreated"
            @onBlur="onBlur"
            :isLoading="isFetchingFolders && folders.length < 1"
            ref="treeview"
            @shortkey.native="onPasteShortKey"
            v-shortkey="{
              ctrlV: ['ctrl', 'v'],
            }"
          ></Personalfolder>
        </div>
      </v-card-text>
      <v-card-actions class="justify-end">
        <v-btn @click="toggleDialog" elevation="0" class="primary--text">
          {{ $tr("cancel") }}
        </v-btn>
        <v-btn
          :loading="isCopying"
          @click="pasteItems"
          elevation="0"
          class="primary"
        >
          {{ $tr("paste") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
import Personalfolder from "../personal/Personalfolder.vue";

export default {
  components: {
    Personalfolder,
  },
  data() {
    return {
      model: false,
      copyableData: {
        items: [],
        action: "copy",
      },
      isCopying: false,

      isFetchingFolders: false,
      errorMessage: null,
      searchData: "",
      folders: [],
      newFolderItem: null,
    };
  },
  methods: {
    toggleDialog(data) {
      if (!data) {
        this.copyableData.items = [];
        this.copyableData.coy = "copy";
        this.model = false;
        this.onBlur();
        return;
      }
      if (this.model) {
        if (!this.isCopying) {
          this.copyableData.items = [];
          this.copyableData.coy = "copy";
        }
        this.model = false;
        this.onBlur();
      } else {
        this.copyableData = data;
        this.fetchAuthFolders();
        this.model = true;
      }
    },

    onPasteShortKey({ srcKey }) {
      if (srcKey == "ctrlV") {
        this.pasteItems();
      }
    },

    async pasteItems() {
      const parent = this.$refs?.treeview?.selectedFolder;
      if (parent) {
        const body = {
          ...this.copyableData,
          parent_id: parent ? parent.id : null,
          parent: parent,
        };
        this.$emit("onCopyingStarted", body);
      } else {
        // this.$toastr.i(this.$tr("please_select_your_destination"));
				this.$toasterNA("primary",this.$tr('please_select_your_destination'));

      }
    },

    setLoader(value) {
      this.isCopying = value;
    },

    async fetchAuthFolders() {
      try {
        this.isFetchingFolders = true;
        let url = this.foldersUrl || "files/personal/auth/folders";
        const { items } = this.copyableData;
        const ignore_item_ids = items
          .filter(({ type }) => type == "folder")
          .map(({ id }) => id);
        const params = { ignore_item_ids };
        const { data } = await this.$axios.get(url, { params });
        if (data.result) {
          this.folders = data.folders;
        } else {
          this.errorMessage = "something went wrong";
        }
      } catch (error) {
        this.errorMessage = "something went wrong";
      }
      this.isFetchingFolders = false;
    },

    createNewFolder() {
      if (this.newFolderItem) return;
      let id = new Date().getTime();
      let parent = this.$refs?.treeview?.selectedFolder;
      if (!parent) {
        parent = {
          id: null,
          name: "Drive",
          parent_id: null,
          children: [],
        };
      }
      const rawFolder = { isCreateNew: true, name: "", id };
      if (parent) {
        const param = { parent, newItem: rawFolder };
        this.addNodeToTree(this.folders, param);
      } else {
        this.folders.unshift(rawFolder);
        const treeElement = document.querySelector(".folder__tree__container");
        if (treeElement) {
          treeElement.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        }
      }
      this.newFolderItem = rawFolder;
    },

    addNodeToTree(folders, { parent, newItem }) {
      for (let index = 0; index < folders.length; index++) {
        const element = folders[index];
        if (element.id == parent.id) {
          this.$refs.treeview.addOpen(element);
          if (element.children) {
            folders[index].children.unshift(newItem);
          } else {
            this.$set(folders[index], "children", []);
            folders[index].children.unshift(newItem);
          }
          return folders;
        }
        if (folders[index]?.children?.length) {
          this.addNodeToTree(folders[index]?.children, {
            parent,
            newItem,
          });
        }
      }
      return folders;
    },

    removeNodeFromTree(folders, removableItem) {
      for (let index = 0; index < folders.length; index++) {
        const element = folders[index];
        if (element.id == removableItem.id) {
          folders.splice(index, 1);
          return folders;
        }
        if (folders[index]?.children?.length) {
          this.removeNodeFromTree(folders[index]?.children, removableItem);
        }
      }
      return folders;
    },

    replaceRecursive(folders, { prevItem, newItem }) {
      for (let index = 0; index < folders.length; index++) {
        const element = folders[index];
        if (element.id == prevItem.id) {
          folders[index] = { ...newItem, children: [] };
          return folders;
        }
        if (folders[index]?.children?.length) {
          this.replaceRecursive(folders[index]?.children, {
            prevItem,
            newItem,
          });
        }
      }
      return folders;
    },

    onFolderCreated({ newItem }) {
      this.$emit("onFolderCreated", newItem);
      newItem = {
        id: newItem.id,
        name: newItem.name,
      };
      this.onBlur();
      let parent = this.$refs?.treeview?.selectedFolder;
      if (!parent) {
        parent = {
          id: null,
          name: "Drive",
          parent_id: null,
          children: [],
        };
      }
      if (parent) {
        const param = { parent, newItem: { ...newItem, children: [] } };
        this.addNodeToTree(this.folders, param);
      } else {
        this.folders.unshift({ ...newItem, children: [] });
      }
    },

    onBlur() {
      if (this.newFolderItem) {
        this.removeNodeFromTree(this.folders, this.newFolderItem);
        this.newFolderItem = null;
      }
    },
  },
};
</script>
