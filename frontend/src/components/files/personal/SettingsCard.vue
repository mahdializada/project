<template>
  <v-card class="pa-1 my-1 d-flex justify-space-between">
    <span class="d-flex align-center">
      <v-avatar color="surface darken-1" rounded size="56">
        <v-icon :color="colorIcon">{{ icon }}</v-icon>
      </v-avatar>
      <span class="ms-1">
        <p class="font-weight-bold" style="font-size: 15px">
          {{ ($tr(name)) | capitalize }}
        </p>
        <p>{{ FileAmount }} {{ " " + $tr("files") }}</p>
      </span>
    </span>
    <p class="primary--text float-right font-weight-bold text-h6">
      {{ FileSize | getFileSize(1) }}
    </p>
  </v-card>
</template>
<script>
export default {
  props: {
    icon: {
      type: String,
      default: "mdi-file",
    },
    colorIcon: {
      type: String,
      default: "primary",
    },
    name: {
      type: String,
      require: true,
    },
    FileSize: {
      type: String,
      require: true,
    },
    FileAmount: {
      type: Number,
      require: true,
    },
  },
};
</script>
<style lang=""></style>
