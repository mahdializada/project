<template>
  <div :class="`d-flex mt-1 ${classes}`">
    <div :class="`d-flex ${iconalign}`">
      <v-avatar color="blue lighten-4" size="30">
        <v-icon color="primary" :size="size">{{ icon }}</v-icon>
      </v-avatar>
    </div>
    <div class="margin-top w-full d-flex justify-center flex-column ms-2">
      <p class="header margin-bottom font-weight-bold">{{ $tr(header) }}</p>
      <slot name="body"></slot>
    </div>
    <slot name="rear-icon"></slot>
  </div>
</template>
<script>
export default {
  name: "properties-list-item",
  props: {
    classes: String,
    header: String,
    icon: String,
    iconalign: {
      type: String,
      default: "align-center",
    },
    size: {
      type: String,
      default: "20",
    },
  },
  data() {
    return {};
  },
};
</script>
<style scoped>
.margin-bottom {
  margin-bottom: 0px;
}
.header {
  font-size: 13px;
}
.comments {
  font-size: 12px;
  margin-bottom: 0px;
}
</style>
