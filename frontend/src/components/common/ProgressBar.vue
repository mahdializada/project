<template>
  <v-dialog v-model="loading" persistent width="300">
    <v-card color="primary" dark>
      <v-card-text class="pt-1">
        {{ $tr('waiting_text') }}
        <v-progress-linear
          indeterminate
          color="white"
          class="mb-0"
        ></v-progress-linear>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "ProgressBar",
  data() {
    return {
      loading: true,
    }
  }
}
</script>

<style scoped>

</style>
