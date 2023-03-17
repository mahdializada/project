<template>
  <div
    class="personal-top-bar d-flex justify-space-between align-center px-2 file-border-bottom"
  >
    <div class="d-flex align-center">
      <v-btn
        v-if="lastItem.text != homeName && prevItem"
        fab
        x-small
        text
        class="ma-0 me-1"
        @click="$router.push(prevItem.to)"
      >
        <v-icon :class="`${$vuetify.rtl ? 'rotate' : ''}`" dark size="22">
          mdi-keyboard-backspace
        </v-icon>
      </v-btn>
      <h2 v-if="lastItem.text == homeName" class="file-top-bar-title ma-0">
        {{ $tr(lastItem.text) }}
      </h2>
      <h2 v-else class="file-top-bar-title ma-0">
        {{ lastItem.text }}
      </h2>
    </div>
    <div class="d-flex align-center">
      <SearchFiles
        v-if="showSearch"
        :loading="searchLoading"
        @search="(value) => $emit('onSearch', value)"
      />
      <FileTopbarItem
        :hideUploadBtns="hideUploadBtns"
        @submitFilter="submitFilter"
        @clearFilter="$emit('clearFilter')"
        :showNewButton="homeName == 'drive' || showNewButton"
        :currentSortItem="currentSortItem"
        :showFilterBtn="showFilterBtn"
        :showSortBtn="showSortBtn"
        :showMore ='showMore'
      />
    </div>
  </div>
</template>
<script>
import SearchFiles from "../common/SearchFiles.vue";
import FileTopbarItem from "../common/FileTopbarItem.vue";
export default {
  props: {
    showSortBtn: {
      type: Boolean,
      default: true,
    },
    showMore: {
      type: Boolean,
      default: true,
    },
    showSearch: {
      type: Boolean,
      default: true,
    },
    showFilterBtn: {
      type: Boolean,
      default: true,
    },
    hideUploadBtns: {
      type: Boolean,
      default: false,
    },
    searchLoading: {
      type: Boolean,
      default: false,
    },
    homeName: {
      type: String,
      default: "drive",
    },
    currentSortItem: {
      type: String,
    },
    lastItem: {
      type: Object,
    },
    prevItem: {},
    hideCompanies: {
      type: Boolean,
    },
    showNewButton: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    SearchFiles,
    FileTopbarItem,
  },
  methods: {
    submitFilter(item) {
      this.$emit("submitFilter", item);
    },
  },
};
</script>
<style>
.file-top-bar-title {
  text-transform: capitalize;
}
.rotate {
  transform: rotate(180deg);
}
.personal-top-bar {
  height: 54px;
}
</style>
