<template>
  <div
    :class="`w-full input-card background ${px} ${py} position-relative ${
      pbNone ? 'pb-0' : ''
    }`"
    :style="`border:${borderStyle}; box-shadow:${boxShadow};`"
  >
    <div>
      <div class="gender-selection-wrapper">
        <v-radio-group
          :value="value"
          v-bind="$attrs"
          v-on="$listeners"
          @change="valueChange"
          row
          class="d-flex justify-space-between custom__radio__group"
        >
          <div class="input-title">
            {{ text }}
          </div>
          <v-spacer></v-spacer>
          <div class="d-flex">
            <v-radio
              v-for="(item, i) in items"
              :key="i"
              class="me-4"
              :label="$tr(item.label)"
              :value="item.value"
            ></v-radio>
          </div>
        </v-radio-group>
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
