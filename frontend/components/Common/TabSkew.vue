<template>
  <div style="background: inherit">
    <div class="d-flex mt-2" style="background: inherit">
      <div
        :class="`d-flex align-center rohullah-tab-item ps-2 ${
          active_tab == index ? 'white primary--text ' : 'primary white--text'
        }`"
        v-for="(item, index) in tabs"
        :key="index"
        @click="tabChange(index)"
      >
        {{ $tr(item) }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    tabs: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      active_tab: 0,
    };
  },
  methods: {
    tabChange(index) {
      this.active_tab = index;
      this.$emit("tabChange", index);
    },
  },
};
</script>

<style scoped >
.rohullah-tab-item {
  border: 1px solid var(--v-surface-darken1) !important;
  width: 200px;
  height: 38px;
  position: relative;
  border-top-right-radius: 35px;
  border-top-left-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  text-transform: uppercase;
}
.rohullah-tab-item::after {
  border-top: 1px solid var(--v-surface-darken1);
  border-right: 1px solid var(--v-surface-darken1);
  border-bottom: 1px solid var(--v-surface-darken1);
  content: "";
  box-sizing: border-box;
  width: 25px;
  height: 39px;

  position: absolute;
  right: 0;
  border-top-right-radius: 6px;
  background-color: inherit;
  z-index: 10;
  transform: skewX(25deg);
}
.primary.rohullah-tab-item::after {
  border-top: 1px solid transparent !important;
  border-right: 1px solid transparent !important;
  border-bottom: 1px solid transparent !important;
}
.white.rohullah-tab-item {
  z-index: 11 !important;
}
</style>