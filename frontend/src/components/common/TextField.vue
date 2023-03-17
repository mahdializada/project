<template>
  <v-text-field
    :label="$tr(label)"
    outlined
    dense
    clear-icon="mdi-close"
    v-bind="$attrs"
    v-on="$listeners"
    clearable
    counter
  />
</template>

<script>
export default {
  props: {
    label: {
      required: true,
      type: String,
    },
  },
};
</script>
