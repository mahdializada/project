<template>
  <div :class="`${position ? '' :'position-relative'} ${right ? 'right' : ''}`" v-click-outside="
    () => {
      clickOutside ? (show = false) : '';
    }
  " :style="`--indicator: ${indicator}px`">
    <slot name="activator" :listeners="{ click: toggleShow }"></slot>
    <transition :name="'fade'">
      <div :class="`pop-over pa-2 rounded white--text font-weight-medium d-flex align-center pe-5 ${color}`" v-if="show"
        :style="`background: ${color}`">
        <div class="icon d-flex align-center justify-center me-1">
          <v-icon dark>{{ icon }}</v-icon>
        </div>
        <slot name="body"></slot>
        <div :class="`pop-over-after ${color}--text`" :style="`color: ${color}`"></div>
        <v-btn fab dark x-small text elevation="0" class="pop-over-close" @click="show = false">
          <v-icon dark> mdi-close </v-icon>
        </v-btn>
      </div>
    </transition>
  </div>
</template>
<script>
export default {
  props: {
    color: {
      type: String,
      default: "primary",
    },

    clickOutside: {
      type: Boolean,
      default: true,
    },
    icon: {
      type: String,
      default: "mdi-alert-circle-outline",
    },
    value: Boolean,
    right: Boolean,
    indicator: {
      type: Number || String,
      default: 38,
    },
    position:Boolean,
  },
  data() {
    return {
      show: false,
    };
  },
  methods: {
    toggleShow() {
      this.show = !this.show;
    },
  },
  watch: {
    value(value) {
      this.show = value;
    },
    show(value) {
      this.$emit("input", value);
    },
  },
};
</script>
<style scoped>
.pop-over {
  position: absolute;
  bottom: 70px;
  min-width: 300px;
  max-width: 400px;
}

.right .pop-over {
  right: 0;
}

.pop-over-after {
  height: 18px;
  width: 20px;
  position: absolute;
  left: var(--indicator);
  bottom: 0;
  transform: translateY(66%);
  border-top: 18px solid currentColor;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
}

.v-application--is-rtl .pop-over-after,
.right .pop-over-after {
  left: unset;
  right: var(--indicator);
}

.v-application--is-rtl .right .pop-over-after {
  right: unset;
  left: var(--indicator);
}

.icon {
  height: 36px;
  width: 36px;
  min-width: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
}

.pop-over-close {
  position: absolute;
  top: 2px;
  right: 2px;
}

.v-application--is-rtl .pop-over-close {
  right: unset;
  left: 2px;
}
</style>
