<template>
  <div
    class="lib-top-bar d-flex justify-space-between align-center px-2 file-border-bottom"
  >
    <div class="d-flex align-center">
      <v-btn
        v-if="hideCompanies"
        fab
        x-small
        text
        class="ma-0 me-1"
        @click="$emit('toggleCompanies')"
      >
        <v-icon :class="`${$vuetify.rtl ? 'rotate' : ''}`" dark size="22">
          mdi-keyboard-backspace
        </v-icon>
      </v-btn>

      <h2 v-if="$attrs.loading" class="file-top-bar-title ma-0">
        <v-skeleton-loader
          type="text"
          width="200px"
          style="margin-top: 12px"
        ></v-skeleton-loader>
      </h2>

      <h2 v-else class="file-top-bar-title ma-0">
        {{ company && company.name }}
      </h2>
    </div>
    <div class="d-flex align-center">
      <SearchFiles @search="() => {}" />
      <v-btn
        color="primary"
        style="height: 30px"
        class="ms-2 px-2 font-weight-medium text-uppercase"
      >
        <v-icon class="me-1"> mdi-plus </v-icon>
        {{ $tr("new") }}
      </v-btn>
    </div>
  </div>
</template>
<script>
import SearchFiles from "../common/SearchFiles.vue";
import { mapGetters } from "vuex";

export default {
  props: {
    hideCompanies: {
      type: Boolean,
    },
  },
  components: {
    SearchFiles,
  },

  computed: {
    ...mapGetters("library", ["company", "loading"]),
  },
};
</script>
<style>
.rotate {
  transform: rotate(180deg);
}
</style>
