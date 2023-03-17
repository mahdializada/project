<template>
  <div
    :key="drawerKey"
    class="profile-dialog"
    v-if="hasDrawer"
    @onbeforeunload="closeBrowser()"
  >
    <div :class="`overlay ${drawer ? '' : 'hidden'}`"></div>
    <v-navigation-drawer
      touchless
      v-model="drawer"
      fixed
      floating
      right
      width="1300"
      :style="colorGrey"
      style="background-color: var(--v-surface-base)"
    >
      <div>
        <div class="primary" style="min-height: 180px">
          <div class="d-flex align-center pa-2">
            <div class="title ms-3 text-h5 font-weight-bold white--text">
              {{ $tr($attrs.titleText) }}
            </div>
            <v-spacer></v-spacer>
            <NuxtLink :to="link" v-if="link">
              <v-btn icon text large class="me-1" color="white">
                <!-- <v-icon large>mdi-window-maximize</v-icon> -->
                <svg viewBox="0 0 560 420" height="20px" fill="currentColor">
                  <path
                    d="M547.75,420H14c-8.75,0-14-5.25-14-12.25V14C0,5.25,5.25,0,14,0H547.75c7,0,12.25,5.25,12.25,14V407.75A11.96189,11.96189,0,0,1,547.75,420ZM26.25,393.75h507.5V26.25H26.25Z"
                  />
                </svg>
              </v-btn>
            </NuxtLink>

            <CloseBtn class="white--text" @click="toggleDrawer" />
          </div>
        </div>
        <div
          class=""
          style="min-height: 100px; background-color: var(--v-surface-base)"
        >
          <v-card
            class="rounded mx-4 px-2"
            style="transform: translateY(-100px)"
          >
            <slot name="header" />
          </v-card>
        </div>
        <div class="" style="">
          <slot name="body" />
        </div>
      </div>
    </v-navigation-drawer>
  </div>
  <div v-else-if="!hasDrawer">
    <div class="primary" style="min-height: 180px">
      <div class="d-flex align-center pa-2">
        <div class="title ms-3 text-h5 font-weight-bold white--text">
          {{ $tr($attrs.titleText) }}
        </div>
        <v-spacer></v-spacer>
        <NuxtLink :to="backLink">
          <v-btn icon large class="me-1" color="white">
            <svg
              height="24px"
              fill="currentColor"
              viewBox="0 0 503.98801 465.91712"
            >
              <path
                d="M10.07809,465.91712h332.64a10.07582,10.07582,0,0,0,10.07809-10.0781V123.199a10.0825,10.0825,0,0,0-10.07809-10.0781H10.0781A10.08252,10.08252,0,0,0,0,123.199v332.64a10.07584,10.07584,0,0,0,10.0781,10.0781Zm10.078-332.64,312.48.00391v312.48h-312.48Z"
              />
              <path
                d="M493.91807.0001h-332.64A10.07584,10.07584,0,0,0,151.2,10.07819v82.879h20.156l.00391-72.801h312.48v312.48h-110.89v20.16h120.96a10.08255,10.08255,0,0,0,10.07812-10.0781V10.0781A10.07589,10.07589,0,0,0,493.90989,0Z"
              />
            </svg>
          </v-btn>
        </NuxtLink>
      </div>
    </div>
    <div
      class=""
      style="min-height: 100px; background-color: var(--v-surface-base)"
    >
      <v-card class="rounded mx-4 px-2" style="transform: translateY(-100px)">
        <slot name="header" />
      </v-card>
    </div>

    <slot name="body" />
  </div>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import UserChip from "~/components//design/UserChip.vue";
export default {
  components: { CloseBtn, UserChip },
  props: {
    isFetchingDetails: {
      type: Boolean,
      default: false,
    },
    hasDrawer: {
      type: Boolean,
      default: true,
    },
    link: {
      type: String,
      default: "",
    },
    backLink: {
      type: String,
      default: "",
    },
    colorGrey: {
      type: String,
      default: "background-color:var(--v-background-base)",
    },
  },
  created() {},
  data() {
    return {
      drawer: false,
      drawerKey: 0,
    };
  },

  methods: {
    toggleDrawer() {
      if (this.drawer) {
        this.drawerKey++;
        this.$emit("update:isFetchingDetails", false);
      }
      this.drawer = !this.drawer;
    },
    closeBrowser() {
      if (process.client) {
        window.onbeforeunload = function (event) {
          var message =
            "Important: Please click on 'Save' button to leave this page.";
          if (typeof event == "undefined") {
            event = window.event;
          }
          if (event) {
            event.returnValue = message;
          }
          alert(message);
        };
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.drawer-button {
  position: fixed;
  top: 440px;
  right: -20px;
  z-index: 6;

  .v-icon {
    margin-left: -18px;
    font-size: 1.3rem;
  }
}

// should remove
.header {
  background: var(--stepper-header-bg);
}
.job-dep,
.job-salary {
  font-size: 14px !important;
}
.stats .stat {
  font-size: 14px !important;
}
.stats .stat .stat-header {
  border-radius: 6px 6px 0 0;
  font-weight: 500;
  padding: 4px 10px;
}
.stats .stat .stat-body {
  padding: 2px 10px;
  border: 1px solid rgba(144, 144, 144, 0.5);
  border-top: 0 !important;
  border-radius: 0 0 6px 6px;
}
.profile-dialog {
  max-height: 100%;
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: auto;
  margin: 0;
}
.overlay.hidden {
  z-index: -1;
  opacity: 0;
  transition: all 0.2s;
}
.overlay {
  position: fixed;
  z-index: 6;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  opacity: 0.46;
  background-color: rgb(33, 33, 33);
  border-color: rgb(33, 33, 33);
}
</style>
