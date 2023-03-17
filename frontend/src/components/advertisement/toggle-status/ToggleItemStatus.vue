<template>
  <div class="align-center d-flex justify-space-between borderDe rounded px-1 pa-2 cursor-pointer">
    <div class="align-center d-flex">
      <div class="me-1">
        <v-icon small color="primary">fa fa-arrow-right</v-icon>
      </div>
      <div class="text">Text</div>
    </div>
    <div class="align-center d-flex">
      <div class="switch__icon">
        <v-switch inset readonly hide-details />
      </div>
      <div class="right__icon">
        <v-icon small>fa fa-arrow-right</v-icon>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style>
.switch__icon .v-input--selection-controls {
  margin-top: 0;
  padding-top: 0;
}
</style>