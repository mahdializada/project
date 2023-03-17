<template>
  <div class="d-flex align-center">
    <button
      class="align-center action__button d-flex justify-space-between px-2 py-1"
    >
      <v-icon>mdi-eye</v-icon>
      <div class="ms-1">View Selected</div>
    </button>
    <button
      class="align-center action__button d-flex justify-space-between px-2 py-1"
    >
      <v-icon>mdi-content-copy</v-icon>
      <div class="ms-1">Clone</div>
    </button>
    <button
      class="align-center action__button d-flex justify-space-between px-2 py-1"
    >
      <v-icon>mdi-pencil</v-icon>
      <div class="ms-1">Edit</div>
    </button>
    <button
      class="align-center action__button d-flex justify-space-between px-2 py-1"
    >
      <v-icon>mdi-delete</v-icon>
      <div class="ms-1">Delete</div>
    </button>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
.theme--light {
  .action__button:hover {
    background: #f5efef;
  }
}
.theme--dark {
  .action__button:hover {
    background: #1f2020;
  }
}
</style>