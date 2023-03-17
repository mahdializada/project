<template>
  <div class="h-100 d-flex align-center justify-center" style="min-height: 250px">
    <div class="text-center">
      <div class="svg-wrapper">
        <div class="svg">
          <lottie-player
            src="/assets/loader.json"
            background="transparent"
            speed="1"
            style="width: 200px; height: 200px"
            loop
            autoplay
          ></lottie-player>
        </div>
      </div>
      <div class="mt-4">
        <v-progress-linear v-model="value" color="amber" height="25">
          <template v-slot:default="{ value }">
            <strong>{{ Math.ceil(value) }}%</strong>
          </template>
        </v-progress-linear>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "file-upload-loader",
  props: {
    value: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      // value: 78,
    };
  },
};
</script>
