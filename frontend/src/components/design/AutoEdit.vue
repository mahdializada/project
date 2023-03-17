<template>
  <v-card class="main-Card px-3">
    <v-card-title primary-title class="pb-1 my-0 title">
      <h2 class="text-h4 font-weight-bold">
        {{ $tr("item_auto_edit", tableName) }}
      </h2>
      <CloseBtn @click="closeDialog" class="ms-auto" />
    </v-card-title>
    <v-card-text class="position-relative px-2">
      <v-card class="headerCard pt-3 px-3" elevation="0">
        <v-row>
          <v-col
            cols="12 pa-0 ma-0"
            md="3"
            lg="2"
            class="text-center"
            v-if="showTitle"
          >
            <v-avatar class="rounded d-inline-block ms-3" size="130">
              <v-icon v-if="isIcon" color="white" class="ma-0 pa-0" size="135">
                {{ iconOrLogo }}
              </v-icon>
              <v-img
                v-else
                lazy-src="https://picsum.photos/id/11/10/6"
                :src="iconOrLogo"
              ></v-img>
            </v-avatar>
          </v-col>
          <v-col
            class="pa-0 ma-0 pt-2"
            cols="12"
            md="12"
            lg="10"
            v-if="showTitle"
          >
            <div class="d-flex justify-space-between">
              <div>
                <h2
                  class="name text-h4 ms-1 font-weight-bold"
                  style="height: 0px"
                  v-if="checkFirstname()"
                >
                  {{ autoEditData[topCurrent].firstname }}
                </h2>
                <h2
                  class="name text-h4 font-weight-bold"
                  v-if="checkName()"
                  style="height: 0px"
                >
                  {{ autoEditData[topCurrent].name }}
                </h2>

                <div v-if="checkUserRulesRelation()">
                  <p
                    class="role text-h5 font-weight-regular"
                    v-for="role in autoEditData[topCurrent].roles"
                    :key="role.id"
                  >
                    {{ role.name }}
                  </p>
                </div>
              </div>
              <div class="me-3">
                <div class="d-flex">
                  <h2
                    class="text-h5 mx-2 font-weight-bold"
                    style="color: #58595b"
                  >
                    {{ tableName }} :
                  </h2>
                  <span class="text-h5 font-weight-bold" style="color: #58595b">
                    {{ topCurrent + 1 }}
                  </span>
                  <span class="text-h5 font-weight-bold" style="color: #58595b"
                    >/</span
                  >
                  <span class="text-h5 font-weight-bold" style="color: #58595b">
                    {{ autoEditData.length }}
                  </span>
                </div>
                <div class="d-flex justify-">
                  <v-btn-toggle
                    rounded
                    class="ms-auto float-right pt-1"
                    mandatory
                  >
                    <v-btn
                      active-class="primary white--text"
                      x-small
                      @click="prev"
                    >
                      <v-icon x-small>fa-angle-double-left</v-icon>
                    </v-btn>
                    <v-btn
                      active-class="primary white--text"
                      x-small
                      @click="next"
                    >
                      <v-icon x-small>fa-angle-double-right</v-icon>
                    </v-btn>
                  </v-btn-toggle>
                </div>
              </div>
            </div>
          </v-col>
          <v-col
            cols="12 pa-0 ma-0 pt-2 ps-2"
            md="12"
            lg="12"
            v-if="!showTitle"
          >
            <div class="d-flex justify-space-between">
              <div>
                <h2
                  class="name text-h4 font-weight-bold"
                  v-if="checkFirstname()"
                >
                  {{ autoEditData[topCurrent].firstname }}
                </h2>
                <h2 class="name text-h4 font-weight-bold" v-if="checkName()">
                  {{ autoEditData[topCurrent].name }}
                </h2>
                <div v-if="checkUserRulesRelation()">
                  <p
                    class="role text-h5 font-weight-rgular"
                    v-for="role in autoEditData[topCurrent].roles"
                    :key="role.id"
                  >
                    {{ role.name }}
                  </p>
                </div>
              </div>
              <div class="me-3">
                <div class="d-flex">
                  <h2
                    class="text-h5 mx-2 font-weight-bold header-counter"
                    style="color: #58595b"
                  >
                    {{ tableName }} :
                  </h2>
                  <span class="text-h5 font-weight-bold" style="color: #58595b">
                    {{ topCurrent + 1 }}
                  </span>
                  <span class="text-h5 font-weight-bold" style="color: #58595b"
                    >/</span
                  >
                  <span class="text-h5 font-weight-bold" style="color: #58595b">
                    {{ autoEditData.length }}
                  </span>
                </div>
                <div class="d-flex justify-">
                  <v-btn-toggle
                    rounded
                    class="ms-auto float-right pt-1"
                    mandatory
                  >
                    <v-btn
                      active-class="primary white--text"
                      x-small
                      @click="prev"
                    >
                      <v-icon x-small>fa-angle-double-left</v-icon>
                    </v-btn>
                    <v-btn
                      active-class="primary white--text"
                      x-small
                      @click="next"
                    >
                      <v-icon x-small>fa-angle-double-right</v-icon>
                    </v-btn>
                  </v-btn-toggle>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
        <div class="d-flex justify-center margin pb-1">
          <div class="stepper-header d-flex justify-space-around align-center">
            <template v-for="(header, i) in headers">
              <div
                :class="` ${i == 0 ? 'spacer-none' : ''} ${
                  current >= i + 1 ? 'current' : ''
                }`"
                :key="i"
              ></div>
              <div
                class="mr-md-5"
                :class="`stepper-header-item-outer text-center ${
                  current == i + 1 ? 'current' : ''
                }`"
                :key="i + 5 * headers.length"
                @click="changeTo(i + 1)"
              >
                <div
                  class="
                    stepper-header-item-inner
                    rounded-circle
                    d-flex
                    justify-center
                    align-center
                    mx-auto
                  "
                >
                  <v-icon style="font-size: 18px">{{ header.icon }}</v-icon>
                </div>
                <div class="stepper-header-title">{{ $tr(header.title) }}</div>
              </div>
              <div
                :class="` ${i == headers.length - 1 ? 'spacer-none' : ''} ${
                  current > i + 1 ? 'current' : ''
                }`"
                :key="i + 3 * headers.length"
              ></div>
            </template>
          </div>
        </div>
      </v-card>
      <v-card
        elevation="0"
        :class="`mt-4 dataCard ${hasLogo ? 'has-logo' : ''}`"
      >
        <div
          :class="`content`"
          v-for="(header, i) in headers"
          :key="i * 1000"
          class=""
        >
          <transition :name="back ? 'slideback' : 'slide'">
            <div v-show="current == i + 1" class="stepper-content-item">
              <slot :name="header.slotName"></slot>
            </div>
          </transition>
        </div>
      </v-card>
      <v-card class="mt-5 footerCart" elevation="0">
        <v-row
          class="
            d-flex
            flex-column
            justify-center
            flex-sm-row
            justify-sm-space-between
            py-2
            px-3
          "
        >
          <v-btn
            text
            color="primary"
            class="stepper-btn"
            @click="prevPage"
            v-if="current != 1"
          >
            <v-icon left x-small>fa-chevron-left</v-icon>
            {{ $tr("prev") }}
          </v-btn>
          <div class="d-flex flex-row-reverse justify-center ms-auto me-3">
            <v-btn
              @click="nextPage"
              v-if="!(isLoading || isSubmitting) && current != headers.length"
              color="primary ml-2"
              class="stepper-btn"
              :type="'button'"
            >
              {{ $tr(current === headers.length ? "Update" : "next") }}
              <v-icon right small>
                {{
                  current === headers.length ? "fa-check" : "fa-chevron-right"
                }}
              </v-icon>
            </v-btn>
            <v-btn
              color="primary"
              class="stepper-btn px-4 ml-2 mr-1"
              v-else-if="isLoading || isSubmitting"
              style="min-width: 150px"
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
            <v-btn
              v-if="!(isLoading || isSubmitting)"
              color="primary"
              class="border px-2 ms-0 ms-md-2 stepper-btn"
              @click="save"
            >
              {{ $tr("save") }}
            </v-btn>
            <v-btn
              v-if="!(isLoading || isSubmitting)"
              color="primary"
              class="border px-2 ms-0 ms-md-2 stepper-btn"
              @click="saveAndNext"
              >{{ $tr("save_next") }}
            </v-btn>
          </div>
        </v-row>
      </v-card>
    </v-card-text>
  </v-card>
</template>
<script>
import CloseBtn from "./Dialog/CloseBtn.vue";
export default {
  components: {
    CloseBtn,
  },
  props: {
    isLoading: {
      require: true,
      default: false,
    },
    isSubmitting: {
      require: true,
      default: false,
    },
    showTitle: {
      type: Boolean,
      default: true,
      require: false,
    },
    autoEditData: {
      require: true,
      type: Array,
    },
    headers: {
      require: true,
      type: Array,
    },
    topIcon: {
      require: true,
    },
    tableName: {
      require: true,
    },
    iconOrLogo: {
      require: true,
    },
    isIcon: {
      type: Boolean,
      default: true,
    },
    loading: {
      type: Boolean,
      default: false,
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
      topCurrent: 0,
      page: 1,
    };
  },
  methods: {
    closeDialog() {
      this.$root.$emit("closeAutoEdit");
    },
    setCurrent(current) {
      this.current = current;
    },
    nextPage() {
      this.$emit("next", this.current);
    },
    nextPageResult() {
      if (this.current == this.headers.length) {
        this.current = 1;
      } else {
        this.current += 1;
      }
    },
    prevPage() {
      this.$emit("prev");
    },
    prevPageResult() {
      if (this.current == 1) {
        this.current = this.headers.length;
      } else {
        this.current -= 1;
      }
    },
    saveAndNext() {
      this.$emit("saveAandNext", this.topCurrent);
    },
    /*
    some changes are made because of validation
    1: if validation got error on save and next button the data will nut update but here in saveAndNext function will add +1
    to topCurrent so I made a seperate function by name of saveAndNextResult to increase topCurrent variable after data updated

    2: you can check in CompanyAutoEdit component that how it implenented !
    */
    saveAndNextResult() {
      if (this.topCurrent + 1 == this.autoEditData.length) {
        this.topCurrent = 0;
      } else {
        this.topCurrent += 1;
      }
    },
    save() {
      this.$emit("save");
    },
    next() {
      if (this.topCurrent + 1 == this.autoEditData.length) {
        this.topCurrent = 0;
      } else {
        this.topCurrent += 1;
      }
      this.$emit("getCurrent", this.topCurrent);
    },
    prev() {
      if (this.topCurrent + 1 == 1) {
        this.topCurrent = this.autoEditData.length - 1;
      } else {
        this.topCurrent -= 1;
      }
      this.$emit("getCurrent", this.topCurrent);
    },
    changeTo(index) {
      this.$emit("changeTo", index);
    },
    changeToResult(index) {
      if (this.current > index) {
        this.back = true;
      } else {
        this.back = false;
      }
      this.current = index;
    },
    checkFirstname() {
      return !!this.autoEditData[this.topCurrent]?.firstname;
    },
    checkName() {
      return !!this.autoEditData[this.topCurrent]?.name;
    },
    checkUserRulesRelation() {
      return !!this.autoEditData[this.topCurrent]?.roles;
    },
  },
};
</script>
<style scoped>
.header-counter {
  min-width: 154px;
}
.stepper-btn {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
.footerCart {
  overflow: hidden;
  position: absolute;
  bottom: 20px;
  right: 16px;
  left: 16px;
}
.dataCard {
  overflow: hidden !important;
  position: absolute;
  top: 116px;
  bottom: 110px;
  right: 16px;
  left: 16px;
}
.has-logo.dataCard {
  top: 172px !important;
}
.main-Card {
  overflow-x: hidden !important;
  height: 80vh;
  background-color: var(--stepper-header-bg) !important;
}
.stepper-content-item {
  overflow-x: hidden !important;
}
.main-Card .margin {
  margin-top: -45px;
}
@media only screen and (max-width: 1200px) {
  .margin {
    margin-top: 0px !important;
  }
  .dataCard {
    top: 172px !important;
  }
}
.main-Card .title {
  color: #b6c1d2 !important;
}
.stepper-header {
  position: sticky;
  top: 0;
  z-index: 1000000000;
}
.stepper-header-item-outer {
  position: relative;
  cursor: pointer;
  min-width: 64px;
}
.spacer.current {
  background: var(--spacer-active-current);
}
.spacer-none {
  background: transparent !important;
}
@media only screen and (max-width: 800px) {
  .spacer {
    display: none;
  }
}
.stepper-header-item-inner {
  height: 46px;
  width: 46px;
  border: 2px solid var(--stepper-header-item-border);
  background: var(--color-white);
  transition: all 0.3s;
  z-index: 3;
  position: relative;
}
.current.stepper-header-item-outer .stepper-header-item-inner {
  background: var(--v-primary-base) !important;
  border-color: var(--v-primary-base) !important;
  color: white;
}
.stepper-header-item-inner .v-icon {
  color: var(--stepper-header-item-border);
  transition: all 0.3s;
}
.current .stepper-header-item-inner .v-icon {
  color: white !important;
}
.stepper-header-title {
  color: var(--input-placeholder-color);
}
.current .stepper-header-title {
  color: var(--v-primary-base);
}
.slide-leave-active {
  transition: 0.3s;
  position: absolute;
  display: none;
}
.slide-enter-active {
  transition: 0.3s;
}
.slide-enter {
  transform: translate(100%, 0);
}
.slide-leave-to {
  transform: translate(-100%, 0);
  position: absolute;
}
.slideback-leave-active {
  transition: 0.3s;
  display: none;
}
.slideback-enter {
  transform: translate(-100%, 0%);
  /* position: absolute; */
}
.slideback-enter-active {
  transition: 0.3s;
  /* position: absolute; */
}
</style>
