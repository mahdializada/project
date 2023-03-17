<template>
  <v-autocomplete
    class="form-new-item form-custom-auto-complete"
    background-color="var(--new-input-bg)"
    outlined
    append-icon="mdi-chevron-down"
    dense
    clearable
    placeholder="filter by company"
    hide-details
    :menu-props="{ bottom: true, offsetY: true }"
    v-bind="$attrs"
    v-on="$listeners"
    :item-text="itemText"
  ></v-autocomplete>
</template>

<script>
export default {
  props: {
    itemText: String,
  },
};
</script>

<style>
.form-custom-auto-complete.v-input__control {
  height: 30px !important;
  padding: 0px !important;
}
</style>
