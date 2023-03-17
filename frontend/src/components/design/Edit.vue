<template>
  <v-card class="main-Card px-3">
    <v-card-title
      primary-title
      class="pb-1 my-0 title d-flex justify-space-between"
    >
      <h2 class="text-h4 font-weight-bold">
        {{ $tr(`${isAutoEdit ? "item_auto_edit" : "item_edit"}`, tableName) }}
      </h2>
      <CloseBtn @click="close" type="button" />
    </v-card-title>
    <v-card-text class="position-relative card-pos">
      <v-card
        :class="`headerCard position-relative py-3 px-3 ${
          hasLogo ? 'has-logo' : ''
        } ${isAutoEdit ? 'is-auto-edit' : ''}`"
        elevation="0"
      >
        <div class="d-flex">
          <div class="d-flex flex-responsive">
            <v-avatar class="rounded d-inline-block" size="120" v-if="hasLogo">
              <v-icon v-if="isIcon" color="white" class="ma-0 pa-0" size="135">
                {{ iconOrLogo }}
              </v-icon>
              <v-img v-else :src="iconOrLogo"></v-img>
            </v-avatar>
            <h2
              class="edit-title mb-1 ms-2 mt-1 text-h4 font-weight-bold"
              v-if="!showTitle"
            >
              {{ editData ? editData.name : "" }}
            </h2>
            <h2
              class="edit-title mb-1 ms-2 mt-1 text-h4 font-weight-bold"
              v-if="showTitle"
            >
              {{ title }}
            </h2>
          </div>
        </div>
        <div class="auto-edit-control-btns" v-if="isAutoEdit">
          <div class="d-flex">
            <h2 class="text-h5 mx-2 font-weight-bold" style="color: #58595b">
              {{ tableName }} :
            </h2>
            <span class="text-h5 font-weight-bold" style="color: #58595b">
              {{ currentIndex + 1 }}
            </span>
            <span class="text-h5 font-weight-bold" style="color: #58595b">
              /
            </span>
            <span class="text-h5 font-weight-bold" style="color: #58595b">
              {{ totals }}
            </span>
          </div>
          <v-btn-toggle rounded class="ms-auto float-right pt-1" mandatory>
            <v-btn
              active-class="primary white--text"
              x-small
              @click="onPrevItem"
            >
              <v-icon x-small>
                {{
                  $vuetify.rtl
                    ? "fa-angle-double-right"
                    : "fa-angle-double-left"
                }}
              </v-icon>
            </v-btn>
            <v-btn
              active-class="primary white--text"
              x-small
              @click="onNextItem"
            >
              <v-icon x-small>
                {{
                  $vuetify.rtl
                    ? "fa-angle-double-left"
                    : "fa-angle-double-right"
                }}
              </v-icon>
            </v-btn>
          </v-btn-toggle>
        </div>
        <div class="stepper-tabs">
          <div class="px-5 px-md-0">
            <StepperHeader
              ref="CarouselHeader"
              :headers="headers"
              :current="current"
              @changeToIndexValidate="changeToIndexValidate"
            />
          </div>
        </div>
      </v-card>
      <v-card
        elevation="0"
        :class="`mt-4 dataCard ${hasLogo ? 'has-logo' : ''} ${
          isAutoEdit ? 'is-auto-edit' : ''
        }`"
      >
        <div
          :class="`content px-md-4 pb-0`"
          v-for="(header, i) in headers"
          :key="i * 1000"
          class=""
        >
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
            <div v-show="current === i + 1" class="stepper-content-item">
              <slot :name="header.slotName"></slot>
            </div>
          </transition>
        </div>
      </v-card>
      <v-card class="mt-5 footerCart" elevation="0">
        <div class="stepper-footer-outer">
          <div
            :class="`stepper-footer py-2 px-1 px-md-8 d-flex ${
              current !== headers.length
                ? 'justify-space-between'
                : 'justify-end'
            }`"
          >
            <v-btn
              v-if="current !== 1 && current !== headers.length"
              text
              color="primary"
              class="stepper-btn"
              @click="prev"
            >
              <v-icon left x-small>{{
                $vuetify.rtl ? "fa-chevron-right" : "fa-chevron-left"
              }}</v-icon>
              {{ $tr("prev") }}
            </v-btn>
            <v-spacer></v-spacer>

            <v-btn
              color="primary"
              class="stepper-btn px-4"
              style="min-width: 150px"
              v-if="isLoading || isSubmitting"
              :loading="isLoading || isSubmitting"
            >
              <template v-slot:loader>
                <span>
                  <span>
                    {{
                      isSubmitting
                        ? $tr("submitting") + "..."
                        : $tr("validating") + "..."
                    }}</span
                  >
                  <v-progress-circular
                    class="ma-0"
                    indeterminate
                    color="white"
                    size="25"
                    :width="2"
                  />
                </span>
              </template>
            </v-btn>

            <!--     Auto Edit Section       -->
            <div v-else-if="isAutoEdit">
              <v-btn
                color="primary"
                class="border px-3 ms-2 stepper-btn"
                @click="saveAndNext"
                v-if="
                  currentIndex + 1 < totals && current !== this.headers.length
                "
              >
                {{ $tr("save_next") }}
              </v-btn>
              <v-btn
                class="ms-2 px-3 border stepper-btn"
                @click="onSave"
                v-if="current !== this.headers.length"
              >
                {{ $tr("save") }}
              </v-btn>
              <v-btn
                v-if="current !== headers.length - 1"
                class="ms-2 me-3 px-3 border stepper-btn"
                @click="current === headers.length ? close() : onValidate()"
                >{{
                  current === headers.length - 1
                    ? $tr("update")
                    : current === headers.length
                    ? $tr("close")
                    : $tr("next")
                }}
              </v-btn>
            </div>
            <v-btn
              v-else
              color="primary"
              class="stepper-btn"
              @click="() => onValidate()"
              type="button"
              >{{
                current === headers.length - 1
                  ? $tr("update")
                  : current === headers.length
                  ? $tr("close")
                  : $tr("next")
              }}
              <v-icon right small
                >{{
                  current === headers.length - 1
                    ? "fa-check"
                    : current === headers.length
                    ? "fa-times"
                    : $vuetify.rtl
                    ? "fa-chevron-left"
                    : "fa-chevron-right"
                }}
              </v-icon>
            </v-btn>
          </div>
        </div>
      </v-card>
    </v-card-text>
  </v-card>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import StepperHeader from "./StepperHeader.vue";

export default {
  components: {
    CloseBtn,
    StepperHeader,
  },
  props: {
    showTitle: {
      type: Boolean,
      default: false,
      require: false,
    },
    title: {
      require: true,
      type: String,
    },
    headers: {
      require: true,
      type: Array,
    },
    iconOrLogo: {
      require: true,
    },
    isIcon: {
      type: Boolean,
      default: true,
    },
    tableName: {
      require: true,
    },
    isLoading: {
      require: true,
      default: false,
    },
    isSubmitting: {
      require: true,
      default: false,
    },
    isAutoEdit: {
      type: Boolean,
      default: false,
    },
    currentIndex: {
      type: Number,
      default: 0,
    },
    totals: {
      type: Number,
      default: 5,
    },
    editData: {
      type: Object,
    },
    hasLogo: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      back: false,
      current: 1,
    };
  },
  methods: {
    closeDialog() {
      this.$root.$emit("closeEdit");
    },
    onSave() {
      this.$emit("onSave");
    },
    saveAndNext() {
      this.$emit("OnSaveAndNext");
    },
    // goes to next page
    onNextItem() {
      this.current = 1;
      this.$emit("onItemChange", "next");
    },
    // goes prev item
    onPrevItem() {
      this.current = 1;
      this.$emit("onItemChange", "back");
    },

    // call validation event  or submit event
    close() {
      this.$emit("close");
    },

    onValidate() {
      if (this.current === this.headers.length - 1) {
        this.$emit("onSubmit");
      } else if (this.current === this.headers.length) {
        this.close();
      } else {
        this.$emit("onValidate", this.current);
      }
    },

    // goes to next step
    nextForce() {
      if (this.current !== this.headers.length) {
        this.back = false;
        this.current = this.current + 1;
      } else {
        this.current = 1;
      }
      if (this.current != 2) {
        this.$refs.CarouselHeader.carouselNext();
      }
    },

    // goes one step back
    prev() {
      if (this.current !== 1) {
        this.back = true;
        this.current = this.current - 1;
      }
      if (this.current !== this.headers.length - 2) {
        this.$refs.CarouselHeader.carouselPrev();
      }
    },

    // set current step to a specific step
    setCurrent(current) {
      this.current = current;
    },

    async changeToIndexValidate(index) {
      if (
        index !== this.headers.length &&
        this.current !== this.headers.length
      ) {
        if (index > this.current) {
          await this.$emit("onChangeTo", index);
        } else {
          this.changeTo(index);
        }
      }
    },

    // change to specific step or fire on icon clicked
    changeTo(index) {
      if (index < this.current) {
        this.back = true;
      } else {
        this.back = false;
      }
      this.current = index;
    },

    success() {
      this.current = this.headers.length;
    },
  },
};
</script>
<style scoped>
.edit-title {
  font-size: 26px !important;
  color: var(--input-title-color);
}
.headerCard {
  height: 170px;
}
.auto-edit-control-btns {
  position: absolute;
  top: 24px;
  right: 24px;
}
.v-application--is-rtl .auto-edit-control-btns {
  right: unset;
  left: 24px;
}
.has-logo .stepper-tabs {
  max-width: 500px;
  margin: 0 auto;
  transform: translateY(-70%);
}
.stepper-tabs {
  max-width: 500px;
  margin: 0 auto;
  transform: translateY(30%);
}
.stepper-btn {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
.card-pos {
  height: 73vh;
}
.footerCart {
  position: absolute;
  bottom: 20px;
  right: 16px;
  left: 16px;
}
.dataCard {
  overflow-y: scroll !important;
  overflow-x: hidden;
  position: absolute;
  top: 160px;
  bottom: 110px;
  right: 16px;
  left: 16px;
}
.main-Card {
  background-color: var(--stepper-header-bg) !important;
  height: 80vh;
  overflow: hidden;
}
.main-Card .margin {
  margin-top: -45px;
}
@media only screen and (max-width: 960px) {
  .margin {
    margin-top: 0 !important;
  }
}
.main-Card .title {
  color: #b6c1d2 !important;
}
@media only screen and (max-width: 1050px) {
  .headerCard.has-logo.is-auto-edit {
    height: 270px !important;
  }
  .headerCard.has-logo {
    height: 240px !important;
  }
  .has-logo.is-auto-edit .stepper-tabs {
    transform: translateY(0%);
  }
  .has-logo .stepper-tabs {
    transform: translateY(30%);
  }
  .dataCard.has-logo.is-auto-edit {
    top: 260px !important;
  }
  .dataCard.has-logo {
    top: 230px !important;
  }
  .is-auto-edit .flex-responsive {
    flex-direction: column;
  }
}
</style>
