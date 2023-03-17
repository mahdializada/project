<template>
  <div
    :class="`w-full input-card background ${px} ${py} position-relative ${
      pbNone ? 'pb-0' : ''
    }`"
    :style="`border:${borderStyle}; box-shadow:${boxShadow};`"
  >
    <div>
      <div
        class="
          gender-selection-wrapper
          d-flex
          align-center
          justify-space-between
        "
      >
        <div class="input-title">
          {{ text }}
        </div>

        <v-checkbox
          v-bind="$attrs"
          v-on="$listeners"
          :value="value"
          @change="valueChange"
        >
        </v-checkbox>
      </div>

      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    items: Array,
    value: null,
    text: String,
    pbNone: Boolean,
    borderStyle: String,
    boxShadow: String,
    px: {
      type: String,
      default: "px-4",
    },
    py: {
      type: String,
      default: "py-2",
    },
    small: Boolean,
  },
  data() {
    return {};
  },
  methods: {
    valueChange(value) {
      this.$emit("onChange", value);
    },
  },
  components: {},
};
</script>
<style scoped>
.input-card {
  border-radius: 16px;
}
.input-title {
  font-size: 18px;
  font-weight: 500;
  color: #777;
}
.input-title.small {
  font-size: 16px;
}
</style>
