<template>
  <div
    class="surface lighten-1 elevation-2 list-grid"
    style="
      padding: 4px;
      border-radius: 20px;
      position: absolute;
      bottom: 0;
      z-index: 2;
    "
  >
    <v-btn
      fab
      x-small
      color="primary darken-1"
      :text="layout == 'list'"
      @click="changeLayout('grid')"
      class="list-grid-btn"
    >
      <v-icon dark> mdi-view-grid </v-icon>
    </v-btn>
    <v-btn
      fab
      x-small
      color="primary darken-1"
      :text="layout == 'grid'"
      @click="changeLayout('list')"
      class="list-grid-btn"
    >
      <v-icon dark> mdi-format-list-bulleted </v-icon>
    </v-btn>
  </div>
</template>
<script>
import { mapMutations, mapState } from "vuex";

export default {
  computed: {
    ...mapState("files", ["layout"]),
  },
  methods: {
    ...mapMutations("files", ["changeLayout"]),
  },
};
</script>
<style scoped>
.list-grid {
  width: 76px;
}
.list-grid-btn {
  transition: all 0.4s !important;
}
</style>
