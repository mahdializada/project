<template>
  <v-text-field
    rounded
    dense
    outlined
    hide-details="auto"
    class="custom-textfield"
    v-bind="$attrs"
    v-on="$listeners"
  >
  </v-text-field>
</template>
<style>
.custom-textfield .v-input__slot {
  min-height: 36px !important;
  padding: 0 16px !important;
}
.custom-textfield .v-label {
  top: 7px !important;
}
.custom-textfield .v-label--active {
  transform: translateY(-14px) scale(0.75) !important;
}
.custom-textfield .v-input__append-inner,
.custom-textfield .v-input__prepend-inner {
  margin-top: 6px !important;
}
.custom-textfield .v-input__append-inner .v-icon.v-icon,
.custom-textfield .v-input__prepend-inner .v-icon.v-icon {
  font-size: 20px;
}
.v-application--is-ltr
  .custom-textfield.v-text-field--outlined.v-text-field--rounded
  legend {
  margin-left: 1px !important;
}
.v-application--is-rtl
  .custom-textfield.v-text-field--outlined.v-text-field--rounded
  legend {
  margin-right: 1px !important;
}
</style>
