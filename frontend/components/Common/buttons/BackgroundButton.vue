<template>
  <div class="d-flex" style="position: relative" @click="$emit('click')">
    <v-btn
      class="background-btn"
      :color="color"
      :style="`width:${size}px;height:${size}px`"
      :loading="loading"
    ></v-btn>
    <span style="" class="btn-icon" v-if="!loading">
      <v-icon :color="color" :size="iconSize" v-if="iconType == 'icon'">{{
        icon
      }}</v-icon>
      <span
        v-if="iconType == 'svg'"
        v-html="icon"
        :class="`${color}--text`"
      ></span>
    </span>
  </div>
</template>

<script>
export default {
  props: {
    size: {
      type: Number,
      default: 32,
    },
    color: {
      type: String,
      default: "primary",
    },
    icon: {
      type: String,
      default: "mdi-pencil",
    },
    iconType: {
      type: String,
      default: "icon",
    },
    iconSize: {
      type: Number,
      default: 20,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped >
.background-btn {
  min-height: unset !important;
  max-height: unset !important;
  min-width: unset !important;
  max-width: unset !important;
  border-radius: 50%;
  opacity: 0.2;
}
.btn-icon {
  position: absolute;
  transform: translate(-50%, -50%);
  top: 50%;
  left: 50%;
  cursor: pointer;
}
</style>