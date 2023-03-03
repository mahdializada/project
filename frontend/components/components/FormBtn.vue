<template>
  <v-btn
    :class="`add-btn arial px-4 ${notBold ? '' : 'bold'} ${
      active ? 'active' : ''
    }`"
    v-bind="$attrs"
    v-on="$listeners"
    :color="`${notPrimary ? '' : 'primary'}`"
    outlined
  >
    {{ $tr(title) }}
  </v-btn>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
    },
    notBold: {
      type: Boolean,
    },
    notPrimary: {
      type: Boolean,
    },
    active: {
      type: Boolean,
    },
  },
};
</script>
<style>
.add-btn {
  height: 40px !important;
  border: 2px solid var(--input-border-color) !important;
  color: var(--input-title-color) !important;
  font-size: 16px !important;
  border-radius: 4px !important;
  font-weight: 400 !important;
  transition: all 0.3s;
}
.add-btn.bold {
  font-weight: 600 !important;
  font-size: 18px !important;
}
.add-btn.active {
  background: var(--v-primary-base) !important;
  border-color: var(--v-primary-base) !important;
  color: var(--color-white) !important;
}
</style>
