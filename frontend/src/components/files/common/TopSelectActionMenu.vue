<template>
  <div
    :class="`w-full d-flex align-center justify-end justify-md-center primary mt-1 px-2 rounded position-relative select-action-menu ${
      selectedCount > 0 ? '' : 'custom-h-0'
    }`"
  >
    <v-checkbox
      :dark="!$vuetify.theme.isDark"
      color="#fff"
      v-model="selectAllModel"
      class="ma-0 select-all__checkbox"
      @change="selecAll"
      hide-details
      :label="
        selected.length == 1
          ? $tr('one_item_selected', selectedCount)
          : $tr('number_items_selected', selectedCount)
      "
    />
    <v-btn
      v-if="!trash && !isShare && !isMyShare"
      outlined
      dark
      style="height: 30px"
      @click="onShareClick"
      class="px-2 font-weight-medium text-uppercase d-none d-sm-block"
    >
      <v-icon left size="18px"> mdi-share </v-icon>
      {{ $tr("share") }}
    </v-btn>
    <v-btn
      v-if="!trash"
      outlined
      dark
      @click="onMultiDownload"
      style="height: 30px"
      class="ms-1 px-2 font-weight-medium text-uppercase d-none d-sm-block"
    >
      <v-icon left size="18px"> mdi-download </v-icon>
      {{ $tr("download") }}
    </v-btn>

    <v-btn
      @click.stop="(event) => onSelectActionMenuClicked(event)"
      outlined
      dark
      style="height: 30px"
      class="ms-1 px-1 min-w-0"
    >
      <v-icon size="18px">mdi-dots-horizontal</v-icon>
    </v-btn>
  </div>
</template>
<script>
export default {
  data() {
    return {
      selectAllModel: false,
      empty: [],
    };
  },

  props: {
    isAllSelected: {
      type: Boolean,
    },
    selectedCount: {
      type: Number,
    },
    selected: {
      type: Array,
    },
    isShare: {
      type: Boolean,
      default: false,
    },
    isMyShare: {
      type: Boolean,
      default: false,
    },
    trash: {
      type: Boolean,
      default: false,
    },
    parent: {
      type: Object,
    },
    trashChildMenu: {
      type: Object,
    },
  },

  methods: {
    onMultiDownload() {
      const selectedItems = this.selected;
      if (selectedItems.length) {
        const map = ({ id, type, name }) => ({ id, type, name });
        const mappedItems = selectedItems.map(map);
        if (selectedItems.length > 1) {
          this.$emit("onMultiDownload", mappedItems);
        } else {
          this.$emit("onDownload", mappedItems[0]);
        }
      }
    },

    onSelectActionMenuClicked(event) {
      if (this.selected.length > 1) {
        const items = this.selected;

        let favoritesCount = 0;
        let removeFavoritesCount = 0;
        items.forEach(({ favorites }) => {
          if (favorites) {
            removeFavoritesCount++;
          } else {
            favoritesCount++;
          }
        });

        const items_menu = this.trash
          ? ["multi_restore", "multi_delete"]
          : this.isShare
          ? [
              "multi_download",
              "multi_copy_to",
              items.findIndex((item) => item.user_id == this.$auth.user.id) >=
                0 || this.parent == null
                ? "multi_delete"
                : "nothing",
            ]
          : this.isMyShare
          ? [
              "multi_download",
              this.parent == null ? "multi_unshare" : "nothing",
            ]
          : [
              "multi_download",
              "multi_share_to",
              "multi_copy_to",
              "multi_move_to",
              "multi_move_to_trashed",
            ];
        if (!this.trash && !this.isShare && !this.isMyShare) {
          if (favoritesCount > 0) {
            items_menu.splice(1, 0, "multi_add_to_favorites");
          }
          if (removeFavoritesCount > 0) {
            items_menu.splice(
              favoritesCount > 0 ? 2 : 1,
              0,
              "multi_remove_from_favorites"
            );
          }
        }
        const options = {
          items: items_menu,
          showMenu: true,
          positionX: event.clientX,
          positionY: event.clientY + 10,
        };
        this.$root.$emit("toggleContextMenu", options);
      } else {
        const items = this.selected;
        if (items.length) {
          const item = items[0];
          const items_menu = this.trash
            ? ["multi_restore", "multi_delete", "properties"]
            : this.isShare
            ? [
                "open",
                "open_in_new_window",
                "multi_download",
                "rename",
                "nothing",
                "multi_delete",
                "properties",
              ]
            : this.isMyShare
            ? [
                "open",
                "open_in_new_window",
                "multi_download",
                this.parent == null ? "multi_unshare" : "nothing",
                "properties",
              ]
            : this.selected.type == "folder"
            ? [
                "open",
                "open_in_new_window",
                "multi_download",
                "multi_share_to",
                "rename",
                "multi_copy_to",
                "multi_move_to",
                "multi_move_to_trashed",
                "properties",
              ]
            : [
                "multi_download",
                "multi_share_to",
                "rename",
                "multi_copy_to",
                "multi_move_to",
                "multi_move_to_trashed",
                "properties",
              ];

          if (!this.trash && !this.isShare && !this.isMyShare) {
            if (item.favorites) {
              items_menu.splice(
                item.type == "folder" ? 3 : 1,
                0,
                "multi_remove_from_favorites"
              );
            } else {
              items_menu.splice(
                item.type == "folder" ? 3 : 1,
                0,
                "multi_add_to_favorites"
              );
            }
          }
          const options = {
            showMenu: true,
            items: items_menu,
            favorites: item.favorites,
            positionX: event.clientX,
            positionY: event.clientY + 10,
          };
          this.$root.$emit("toggleContextMenu", options);
        }
      }
    },

    selecAll() {
      this.$emit("selectAll", this.selectAllModel);
    },

    onShareClick() {
      const selectedItems = this.selected.map((item) => item.id);
      this.$emit("onShare", selectedItems);
    },
  },

  watch: {
    isAllSelected(value) {
      this.selectAllModel = value;
    },
  },
};
</script>
<style>
.select-all__checkbox {
  position: absolute;
  left: 16px;
  top: 4px !important;
}
.v-application.v-application--is-rtl .select-all__checkbox {
  left: unset;
  right: 16px;
}
</style>
