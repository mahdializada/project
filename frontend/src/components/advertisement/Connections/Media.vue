<template>
    <div class="d-flex align-center">
      <div
        class="ms-1 rounded d-flex align-center justify-center"
        style="height: 80px; min-width: 30px; max-width: 30px"
      >
        <v-icon @click="scrollTo('left')" class="primary white--text rounded-circle"
          >mdi-chevron-left</v-icon
        >
      </div>
      <div
        id="scroll-box"
        class="d-flex align-center px-1"
        style="overflow-x: hidden; scroll-behavior: smooth"
      >
      <div
      class="custom-card mx-1 d-flex"
      style="min-width: 200px;z-index: 100 !important;"
      height="80"
      v-for="item in items"
      :key="item"
    >
    <slot name="body" :item="item"></slot>
    </div>
      </div>
      <div
        class="ms-1 rounded d-flex align-center justify-center"
        style="height: 80px; min-width: 30px; max-width: 30px"
      >
        <v-icon @click="scrollTo('right')" class="primary white--text rounded-circle"
          >mdi-chevron-right</v-icon
        >
      </div>
    </div>
  </template>
  
  <script>
  
  export default {
    props: {
        items:{
            require:true,
        },
      api_loading: {
        type: Boolean,
      },
    },
    data() {
      return {
        scroll_left: 0,
      };
    },
    methods: {
      scrollTo(direction) {
        console.log(direction);
        const scrollBox = document.getElementById("scroll-box");
        if (direction == "left") {
          scrollBox.scrollLeft -= 500;
        } else {
          scrollBox.scrollLeft += 500;
        }
        this.scroll_left = scrollBox.scrollLeft;
      },
    },
  };
  </script>
  
  <style scoped>
  .btn-background {
    background: #2e7be622 !important;
    color: var(--v-primary-base) !important;
  }
  </style>
  