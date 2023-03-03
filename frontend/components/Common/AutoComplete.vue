<template>
  <v-autocomplete
    :label="$tr(label)"
    outlined
    :no-data-text="$tr('no_data_available')"
    clearable
    dense
    v-bind="$attrs"
    v-on="$listeners"
  >
  </v-autocomplete>
</template>

<script>
export default {
  name: "AutoComplete",
  props: {
    label: {
      required: true,
      type: String,
    },
  },
};
</script>

<style scoped></style>
