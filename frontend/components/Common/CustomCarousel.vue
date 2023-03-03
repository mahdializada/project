<template>
  <div>
    <v-carousel
      v-model="current_index"
      :show-arrows-on-hover="show_arrows_on_hover"
      :show-arrows="show_arrows"
      hide-delimiters
      hide-delimiter-background
      :cycle="cycle"
      :height="height"
      :class="`my-carousel ${rounded ? 'rounded' : ''}`"
    >
      <v-carousel-item
        v-for="(item, i) in items"
        :key="i"
        :src="item.path || item"
      >
        <slot name="content"></slot>
      </v-carousel-item>

      <template v-slot:prev="{ on, attrs }">
        <v-btn class="" fab x-small color="white" v-bind="attrs" v-on="on">
          <v-icon color="primary">
            {{ $vuetify.rtl ? "mdi-chevron-right" : "mdi-chevron-left" }}
          </v-icon>
        </v-btn>
      </template>
      <template v-slot:next="{ on, attrs }">
        <v-btn class="" fab x-small color="white" v-bind="attrs" v-on="on">
          <v-icon color="primary">
            {{ $vuetify.rtl ? "mdi-chevron-left" : "mdi-chevron-right" }}
          </v-icon>
        </v-btn>
      </template>
    </v-carousel>
    <div class="d-flex align-center justify-center pt-1" v-if="showDelimiters">
      <span
        v-for="(item, index) in items"
        :key="index"
        @click="changeSlide(index)"
        :class="`rounded-sm  cursor-pointer ${
          index == current_index ? 'primary' : 'grey lighten-1'
        }`"
        style="width: 20px; height: 4.5px; margin: auto 2px"
      ></span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    rounded: {
      type: Boolean,
      default: true,
    },
    items: Array,
    height: String,
    show_arrows: Boolean,
    cycle: Boolean,
    show_arrows_on_hover: Boolean,
    showDelimiters: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      current_index: 0,
    };
  },
  methods: {
    changeSlide(index) {
      this.current_index = index;
    },
  },
};
</script>
<style>
.my-carousel .v-window__prev {
  margin: 0 8px !important;
}
.my-carousel .v-window__next {
  margin: 0 8px !important;
}
.v-carousel__controls {
  position: absolute;
  bottom: -30px;
  z-index: 1000 !important;
}
</style>