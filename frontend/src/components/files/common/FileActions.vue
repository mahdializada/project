<template>
  <div class="d-flex">
    <v-btn
      v-if="!trash && !isShared"
      @mouseover="favoriteIconMouseHover"
      @mouseleave="favoriteIconMouseLeave"
      @click.stop="toggleFavorite"
      :loading="favoriteLoading"
      :disabled="favoriteLoading"
      class="me-1"
      fab
      x-small
      color="accent lighten-4"
    >
      <v-icon size="22" color="accent">
        {{
          noneFavoriteIcon
            ? noneFavoriteIcon
            : item.favorites
            ? "mdi-star"
            : "mdi-star-outline"
        }}
      </v-icon>
    </v-btn>
    <v-btn
      @click.stop="downloadFile"
      v-if="!trash"
      class="me-1"
      fab
      dark
      x-small
      color="primary  lighten-4"
    >
      <template v-slot:loader>
        <v-progress-circular
          :rotate="-96"
          :size="40"
          :width="2"
          color="primary"
        >
          %
        </v-progress-circular>
      </template>

      <v-icon size="22" color="primary"> mdi-download </v-icon>
    </v-btn>

    <v-btn
      v-if="trash"
      @click.stop="restoreFile(item)"
      class="me-1"
      fab
      x-small
      color="primary  lighten-4"
    >
      <v-icon size="22" color="primary"> mdi-restore </v-icon>
    </v-btn>

    <v-btn
      v-if="isShared ? item.user_id == $auth.user.id : true"
      @click.stop="deleteFilesOrfolder"
      :loading="deleting"
      :disabled="deleting"
      class="me-1"
      fab
      x-small
      color="error  lighten-4"
    >
      <v-icon size="22" color="error"> mdi-delete </v-icon>
    </v-btn>
  </div>
</template>

<script>
import { mapMutations, mapGetters } from "vuex";

export default {
  props: {
    isShared: false,
    item: null,
    trash: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      noneFavoriteIcon: null,
      deleting: false,
      favoriteLoading: false,
    };
  },

  computed: {
    ...mapGetters("files", ["getZippingTotal"]),
  },

  methods: {
    ...mapMutations("files", ["pushZippingFile", "removeZippingFile"]),

    favoriteIconMouseHover() {
      this.noneFavoriteIcon = this.item.favorites
        ? "mdi-star-outline"
        : "mdi-star";
    },
    favoriteIconMouseLeave() {
      this.noneFavoriteIcon = this.item.favorites
        ? "mdi-star"
        : "mdi-star-outline";
    },

    deleteFilesOrfolder() {
      if (this.trash) this.deleteTrash();
      else this.deleteFile();
    },

    async deleteFile() {
      try {
        if (this.item) {
          this.deleting = true;
          const { id, type } = this.item;
          const items = [{ id, type }];
          this.$emit("deleteFile", items);
          this.deleting = false;
        }
      } catch (error) {}
    },

    async toggleFavorite() {
      const { id, name, type, favorites } = this.item;
      this.$emit("toggleFavorite", [{ id, name, type, favorites }]);
    },

    downloadFile() {
      if (this.item) {
        const url = "files/personal/files/download";
        this.pushZippingFile({ url, item: this.item, context: this });
      }
    },
    restoreFile() {
      this.$emit("restoreFile", [this.item]);
    },
    deleteTrash() {
      this.deleting = true;
      this.$emit("deleteTrash", [this.item]);
      this.deleting = false;
    },
  },
};
</script>

<style></style>
