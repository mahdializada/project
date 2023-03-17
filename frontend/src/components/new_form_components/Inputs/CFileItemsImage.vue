<template>
  <div
    :class="`product-image-file ma-1 ${file.retry ? 'retry error--text' : ''}`"
  >
    <div
      class="product-image-file-image position-relative"
      :style="`background-image:url('${file.preview}')`"
    >
      <v-btn
        v-if="
          (file.progress == 100 || file.progress == 0) && file.retry == false
        "
        class="pa-1 close-btn"
        fab
        dark
        x-small
        color="rgba(255,255,255,0.5)"
        @click="$emit('removeFile', file)"
      >
        <v-icon dark color="primary" size="20"> mdi-close </v-icon>
      </v-btn>
      <div
        v-if="file.progress !== 100 || file.retry"
        class="
          rounded-circle
          background
          d-flex
          align-center
          justify-center
          center-circle
        "
        style=""
      >
        <v-btn
          v-if="file.retry"
          class="pa-1"
          style="height: 34px; width: 34px"
          fab
          x-small
          @click="$emit('retry', file)"
        >
          <v-icon dark color="error" size="18"> mdi-reload </v-icon>
        </v-btn>
        <v-progress-circular
          v-else-if="file.progress !== 100"
          :size="36"
          :value="file.progress"
          color="primary"
          :rotate="-90"
        >
          <v-btn
            class="pa-1"
            style="height: 28px; width: 28px"
            fab
            x-small
            @click="$emit('cancelUpload', file)"
          >
            <v-icon dark color="primary" size="20"> mdi-close </v-icon>
          </v-btn>
        </v-progress-circular>
      </div>
    </div>
    <div
      class="text-center mt-1"
      style="max-width: 150px; word-break: break-all"
    >
      {{ file.file.name }}
    </div>
  </div>
</template>
<script>
export default {
  props: {
    file: Object,
  },
};
</script>
<style scoped>
.product-image-file {
  font-size: 13px;
}
.product-image-file-image {
  height: 150px;
  width: 150px;
  border-radius: 20px;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  border: 2px solid #d9e0f4;
}

.product-image-file.retry .product-image-file-image {
  border: 2px solid var(--v-error-base);
}
.close-btn {
  height: 26px;
  width: 26px;
  position: absolute;
  top: 8px;
  right: 8px;
}
.center-circle {
  height: 44px;
  width: 44px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
