/** * @author Sayed Nasim Alizai * @email Sayedhakimi449@gmail.com * @create
date 2022-05-26 11:00:51 * @modify date 2022-05-26 11:00:51 * @desc
[description] */
<template>
  <ShareListLoading v-if="isLoading" :lenght="10" />
  <v-treeview
    v-else
    v-model="tree"
    :items="folders"
    :search="searchValue"
    activatable
    item-key="id"
    class="ps-0"
    @update:active="folderClicked"
    transition
    hoverable
    selected-color="red"
    ref="treeview"
    return-object
  >
    <template v-slot:prepend="{ open }">
      <v-icon color="primary">
        {{ open ? "mdi-folder-open" : "mdi-folder" }}
      </v-icon>
    </template>
    <template v-slot:label="{ item }">
      <v-tooltip
        v-if="item.isCreateNew"
        v-model="folderNameErrorMessage"
        bottom
        color="error"
      >
        <template v-slot:activator="{}">
          <v-form @submit.prevent="onCreateFolderSubmit(item)">
            <v-text-field
              class="mt-1"
              @click.stop
              @update.stop
              :disabled="loading"
              outlined
              hide-details
              dense
              autofocus
              :error="folderNameErrorMessage"
              v-model.trim="item.name"
              @blur="onCreateFolderSubmit(item)"
              @input="() => (folderNameErrorMessage = false)"
            >
            </v-text-field>
          </v-form>
        </template>
        <span>{{ $tr("invalid_folder_name") }}</span>
      </v-tooltip>
      <div v-else>{{ item.name }}</div>
    </template>
  </v-treeview>
</template>
<script>
import ShareListLoading from "~/components/files/common/Share/ShareListLoading.vue";

export default {
  components: { ShareListLoading },
  props: {
    isLoading: {
      default: false,
    },
    searchValue: {
      default: "",
    },
    folders: {
      type: Array,
    },
  },
  data() {
    return {
      tree: [],
      folderNameErrorMessage: false,
      loading: false,
      selectedFolder: null,
    };
  },

  methods: {
    // fired when create folder blured for unfocused
    onCreateFolderSubmit(item) {
      if (this.loading) return;
      const { name } = item;
      if (name) {
        if (this.isInvalidFolderName(name)) {
          this.folderNameErrorMessage = true;
          return;
        }
        this.folderNameErrorMessage = false;
        this.createFolder(item);
        return;
      }
      this.folderNameErrorMessage = false;
      this.$emit("onBlur");
    },

    // validate folder name
    isInvalidFolderName(value) {
      let folderRegex =
        /[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
      return folderRegex.test(value);
    },

    // create or rename folder in backend
    async createFolder(item) {
      try {
        this.loading = true;
        let body = { parent_id: null };
        if (this.selectedFolder) {
          body.parent_id = this.selectedFolder.id;
        }
        body.folder_name = item.name;
        const url = "files/personal/folder/action";
        const { data } = await this.$axios.post(url, body);
        if (data.result) {
          this.$emit("folderCreated", { prevItem: item, newItem: data.folder });
        } else {
          // this.$toastr.e(this.$tr("unknown_error_try_again"));
			this.$toasterNA("red", this.$tr('unknown_error_try_again'));

        }
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

      }
      this.loading = false;
    },

    addOpen(item) {
      const tree = this.$refs.treeview;
      tree.open.push(item);
    },

    folderClicked(items) {
      const tree = this.$refs.treeview;
      if (items.length > 0) {
        this.selectedFolder = items[0];
        const { id, children } = this.selectedFolder;
        const parentIndex = tree.open.findIndex((row) => row.id == id);
        if (parentIndex != -1) {
          tree.open.splice(parentIndex, 1);
        } else if (children) {
          tree.open.push(this.selectedFolder);
        }
      } else {
        const { children, id } = this.selectedFolder;
        const parentIndex = tree.open.findIndex((row) => row.id == id);
        tree.open.splice(parentIndex, 1);
        if (Array.isArray(children)) {
          children.forEach(({ id }) => {
            const index = tree.open.findIndex((row) => row.id == id);
            if (index >= 0) {
              tree.open.splice(index, 1);
            }
          });
        }
        this.selectedFolder = null;
      }
    },
  },
};
</script>
<style>
.borderBottom:not(:last-child) {
  border-bottom: 1px solid var(--v-surface-darken1);
}
</style>
