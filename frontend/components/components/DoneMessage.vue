<template>
  <div
    class="d-flex align-center justify-center"
    style="min-height: 510px"
  >
    <div class="text-center">
      <div class="svg-wrapper">
        <div class="svg">
          <lottie-player
            src="/assets/done_animation.json"
            background="transparent"
            speed="1"
            style="width: 380px; height: 380px"
            loop
            autoplay
          >
          </lottie-player>
        </div>
      </div>
      <h3 class="success-title mt-3">{{ $tr(title) }}</h3>
      <p class="success-sub">{{ $tr(subTitle) }}</p>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
    },
    subTitle: {
      type: String,
    },
  },
};
</script>
<style scoped>
.svg-wrapper {
  height: 320px;
  width: 400px;
  overflow: hidden;
  position: relative;
}
.svg-wrapper .svg {
  position: absolute;
  bottom: -40px;
  left: 50%;
  transform: translateX(-50%);
}

.success-title {
  font-size: 22px;
  color: var(--v-secondary-darken2);
}

.success-sub {
  font-size: 18px;
  color: var(--v-secondary-base);
}
</style>
