<template lang="">
  <v-card
    class="add-new-card rounded-lg overflow-y-auto d-flex flex-column justify-center align-center mx-1"
    @click="addNewPlatform"
    outlined
    width="250"
    height="350"
  >
    <div class="borderGrey d-flex justify-center align-center">
      <v-icon class="borderGrayIcon">mdi-plus</v-icon>
    </div>
    <p class="mb-0 mt-1 add-new-card-title grey--text">{{ $tr(label) }}</p>
  </v-card>
</template>
<script>
export default {
  props: {
    label: {
      type: String,
      default: "add_another_platform",
    },
  },
  methods: {
    addNewPlatform() {
      this.$emit("addNewPlatform");
    },
  },
};
</script>
<style scoped>
.borderGrey {
  height: 40px;
  width: 40px;
  border: 1px solid var(--v-surface-darken2);
  border-radius: 5px !important;
}
.add-new-card:hover .borderGrey {
  border: 1px solid var(--v-primary-base);
  background: var(--v-primary-base);
}
.add-new-card .borderGrey .borderGrayIcon {
  color: var(--v-surface-darken2);
}
.add-new-card:hover .borderGrey .borderGrayIcon {
  color: white;
}
.add-new-card:hover .add-new-card-title {
  color: var(--v-primary-base) !important;
}
</style>
