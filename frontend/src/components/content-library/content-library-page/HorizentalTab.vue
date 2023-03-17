<template>
  <div class="horizental-tab">
    <div
      :class="`d-flex justify-center ${
        selected_profile != index ? 'primary' : 'white'
      } mb-1 horizental-tab-item`"
      v-for="(tab, index) in tabs"
      :key="index"
      :style="selected_profile == index ? ' z-index: 1' : 'z-index:0'"
      style="padding-top: 10px"
      @click="onTabChange(index)"
    >
      <div
        :class="selected_profile != index ? 'white--text' : 'primary--text'"
        style="
          transform: rotateZ(-90deg);
          position: absolute;
          min-width: 60px;
          bottom: 50%;
        "
      >
        {{ $tr(tab.title) }}
      </div>
      <span
        :class="selected_profile != index ? 'white--text' : 'primary--text'"
        style="position: absolute; bottom: 0; z-index: 10"
      >
        <svg
          style="transform: rotate(-90deg)"
          v-if="tab.icon == 'video'"
          xmlns="http://www.w3.org/2000/svg"
          width="19.274"
          height="16.358"
          viewBox="0 0 19.274 16.358"
        >
          <path
            id="media-tab"
            d="M-14853.832-12157.1c.045-2.49.041-4.98,0-7.471a1.768,1.768,0,0,1,1.894-1.872q7.235.029,14.472,0a1.776,1.776,0,0,1,1.9,1.9c-.041,2.49-.011,4.98-.015,7.467a1.646,1.646,0,0,1-1.842,1.85q-3.64.006-7.272,0c-2.412,0-4.828-.034-7.236.015h-.047A1.769,1.769,0,0,1-14853.832-12157.1Zm6.412-5.883c-.008,1.409,0,2.822,0,4.234a1.233,1.233,0,0,0,1.912,1.13q1.822-1.028,3.627-2.091a1.235,1.235,0,0,0-.019-2.226q-1.784-1.058-3.594-2.077a4.181,4.181,0,0,0-.679-.246A1.28,1.28,0,0,0-14847.42-12162.987Zm-5.384-4.228a1.979,1.979,0,0,1,1.63-1.271,3.867,3.867,0,0,1,.466-.021q2.588-.006,5.178,0,3.456.006,6.911,0a2.035,2.035,0,0,1,2.043,1.192.441.441,0,0,1,.03.1Zm1.2-2.091a1.745,1.745,0,0,1,1.654-1.268q5.251-.018,10.494,0a1.745,1.745,0,0,1,1.666,1.268Z"
            transform="translate(14854.333 12171.073)"
            fill="currentColor"
            stroke="rgba(0,0,0,0)"
            stroke-width="1"
          />
        </svg>

        <svg
          style="transform: rotate(-90deg)"
          v-if="tab.icon == 'graph'"
          xmlns="http://www.w3.org/2000/svg"
          width="18.068"
          height="13.551"
          viewBox="0 0 18.068 13.551"
        >
          <path
            id="comparing-tab"
            d="M17.5,15.792H2.258V5.065A.564.564,0,0,0,1.694,4.5H.565A.564.564,0,0,0,0,5.065V16.922a1.129,1.129,0,0,0,1.129,1.129H17.5a.564.564,0,0,0,.565-.565V16.357A.564.564,0,0,0,17.5,15.792ZM16.374,5.629H12.208a.847.847,0,0,0-.6,1.446l1.143,1.143-2.589,2.589L7.574,8.219a1.129,1.129,0,0,0-1.6,0L3.553,10.643a.565.565,0,0,0,0,.8l.8.8a.565.565,0,0,0,.8,0l1.625-1.626L9.365,13.2a1.129,1.129,0,0,0,1.6,0l3.388-3.388,1.143,1.143a.847.847,0,0,0,1.446-.6V6.194A.564.564,0,0,0,16.374,5.629Z"
            transform="translate(0 -4.5)"
            fill="currentColor"
          />
        </svg>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: { selected_profile: Number, tabs: Array },
  methods: {
    onTabChange(index) {
      console.log("iundesx", index);
      this.$emit("onTabChange", index);
    },
  },
};
</script>

<style scoped>
.horizental-tab {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translate(-100%, -50%);
}
.horizental-tab-item {
  width: 40px;
  min-height: 130px;
  border-top-left-radius: 10px;
  border-bottom-left-radius: 40px;
  position: relative;
}
.horizental-tab-item::after {
  content: "";
  width: 40px;
  height: 33px;
  position: absolute;
  border-top-right-radius: 5px;
  border-bottom-left-radius: 10px;
  left: 0px;
  bottom: -15px;
  margin-bottom: 7px;
  background-color: inherit;

  transform: skewY(35deg);
}
.card-background {
  background: #f7f8fc;
}
</style>