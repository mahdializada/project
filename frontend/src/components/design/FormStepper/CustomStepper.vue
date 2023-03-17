<template>
  <div :style="`height:${CustomHeight}vh`">
    <div
      class="stepper-header d-flex justify-space-around align-center px-16 px-sm-16"
    >
      <StepperHeader
        ref="CarouselHeader"
        :headers="headers"
        :current="current"
        @changeToIndexValidate="changeToIndexValidate"
      />
    </div>
    <div class="stepper-content overflow-hidden">
      <div class="content-outer px-md-4 pt-md-4 pb-0">
        <div :class="`content`" v-for="(header, i) in headers" :key="i * 1000">
          <transition
            :name="
              $vuetify.rtl
                ? back
                  ? 'slide'
                  : 'slideback'
                : back
                ? 'slideback'
                : 'slide'
            "
          >
            <div v-show="current === i + 1" class="stepper-content-item pa-3">
              <slot :name="header.slotName"></slot>
            </div>
          </transition>
        </div>
      </div>
    </div>
    <div class="stepper-footer-outer pa-md-3 pa-2">
      <div
        :class="`stepper-footer py-2 px-1 px-md-8 d-flex ${
          current === 1
            ? 'justify-end'
            : current != headers.length
            ? 'justify-space-between'
            : 'justify-end'
        }`"
      >
        <v-btn
          text
          color="primary"
          class="stepper-btn"
          @click="prev"
          v-if="current !== headers.length && current !== 1"
        >
          <v-icon left x-small>
            {{ $vuetify.rtl ? "fa-chevron-right" : "fa-chevron-left" }}
          </v-icon>
          {{ $tr("prev") }}
        </v-btn>
        <div>
          <v-btn
            text
            color="primary"
            class="stepper-btn mr-2"
            @click="nextSkip"
            v-if="isInSkip(current)"
            :type="current === headers.length ? 'submit' : 'button'"
          >
            {{ $tr("skip") }}
            <v-icon right small
              >{{
                current == headers.length - 1
                  ? "fa-check"
                  : current == headers.length
                  ? "fa-times"
                  : $vuetify.rtl
                  ? "fa-chevron-left"
                  : "fa-chevron-right"
              }}
            </v-icon>
          </v-btn>
          <v-btn
            color="primary"
            class="stepper-btn px-3"
            style="min-width: 160px"
            v-if="loading || isSubmitting"
            :loading="loading || isSubmitting"
          >
            <template v-slot:loader>
              <span>
                <span>
                  {{ isSubmitting ? $tr("submitting") : $tr("validating") }}
                </span>
                <span
                  v-if="show_percentage && isSubmitting && percentage != 100"
                >
                  {{ percentage }} %
                </span>
                <span v-else>
                  ...
                  <v-progress-circular
                    class="ma-0"
                    indeterminate
                    color="white"
                    size="25"
                    :width="2"
                  />
                </span>
              </span>
            </template>
          </v-btn>
          <v-btn
            v-else-if="current === headers.length"
            v-show="showStartOver"
            color="primary"
            class="stepper-btn"
            @click="fillMore"
            :type="'button'"
          >
            {{ $tr("start_over") }}
            <v-icon right small>fa-redo</v-icon>
          </v-btn>
          <v-btn
            v-else
            color="primary"
            class="stepper-btn"
            @click="current === headers.length - 1 ? submit() : next()"
            :type="'button'"
          >
            {{ $tr(current === headers.length - 1 ? "submit" : "next") }}
            <v-icon right small>
              {{
                current === headers.length - 1
                  ? "fa-check"
                  : $vuetify.rtl
                  ? "fa-chevron-left"
                  : "fa-chevron-right"
              }}
            </v-icon>
          </v-btn>
          <v-btn
            v-if="current === headers.length && showClose"
            color="primary"
            class="stepper-btn"
            @click="
              () => {
                $emit('close');
              }
            "
            :type="'button'"
          >
            {{ $tr("close") }}
            <v-icon right small>fa-times</v-icon>
          </v-btn>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import StepperHeader from "../StepperHeader.vue";

export default {
  components: {
    StepperHeader,
  },
  props: {
    headers: {
      type: Array,
    },
    isSubmitting: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    show_percentage: {
      type: Boolean,
      default: false,
    },
    canNext: {
      type: Boolean,
    },
    skip: {
      type: Array,
      default() {
        return [];
      },
    },
    reloadReason: {
      type: Boolean,
      default: false,
    },
    showStartOver: {
      type: Boolean,
      default: true,
    },
    showClose: {
      type: Boolean,
      default: false,
    },
    CustomHeight: {
      type: String,
      default: '80',
    },
    percentage: {
      type: Number,
      default: 0,
      require: false,
    },
  },
  data() {
    return {
      current: 1,
      checkbox1: true,
      back: false,
      filePath: "",
      loadingInside: false,
      owl: $(".stepper-header-owl"),
    };
  },
  created() {},
  methods: {
    async next() {
      await this.$emit("validate", this.current);
      if (this.canNext) {
        this.$emit("update:canNext", false);
        if (this.current !== this.headers.length) {
          this.back = false;
          this.current = this.current + 1;
          this.$emit("onNext", this.current);
        } else {
          this.$emit("close");
          this.current = 1;
        }
      }
    },
    // goes to next step
    forceNext() {
      if (this.current !== this.headers.length) {
        this.back = false;
        this.current = this.current + 1;
        this.$emit("onNext", this.current);
      } else {
        this.$emit("close");
        this.current = 1;
      }
      if (this.current != 2) {
        this.$refs.CarouselHeader.carouselNext();
      }
    },

    nextSkip() {
      this.$emit("onSkip");
      if (this.current !== this.headers.length) {
        this.back = false;
        this.current = this.current + 1;
        this.$emit("onNext", this.current);
      } else {
        this.$emit("close");
        this.current = 1;
      }
      if (this.current != 2) {
        this.$refs.CarouselHeader.carouselNext();
      }
    },

    prev() {
      if (this.current !== 1) {
        this.back = true;
        this.current = this.current - 1;
      }
      if (this.current !== this.headers.length - 2) {
        this.$refs.CarouselHeader.carouselPrev();
      }
    },

    fillMore() {
      this.current = 1;
      if (this.reloadReason) {
        this.$emit("reloadReason");
      }
      this.$emit("onStartOver");
      this.$refs.CarouselHeader.carouselTo(0, 100);
    },

    async changeToIndexValidate(index) {
      if (
        index !== this.headers.length &&
        this.current !== this.headers.length
      ) {
        if (index > this.current) {
          await this.$emit("changeToValidate", index);
        } else {
          this.changeTo(index);
        }
      }
    },

    changeTo(index) {
      if (index < this.current) {
        this.back = true;
      } else {
        this.back = false;
      }
      this.current = index;
    },

    setCurrent(current) {
      this.current = current;
    },

    isInSkip(current) {
      return this.skip.includes(current);
    },

    submit() {
      this.$emit("submit");
    },
  },
};
</script>
<style scoped>

.stepper-header {
  padding: 20px 50px;
  background: var(--stepper-header-bg);
  position: sticky;
  top: 0;
  z-index: 1000000000;
  overflow: hidden;
}
.stepper-footer {
  background: var(--stepper-header-bg);
}
.stepper-btn {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
.stepper-footer-outer {
  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
}
.content-outer {
  position: absolute;
  top: 90px;
  left: 0;
  right: 0;
  bottom: 100px;
  overflow-y: scroll;
  overflow-x: hidden;
}
</style>
