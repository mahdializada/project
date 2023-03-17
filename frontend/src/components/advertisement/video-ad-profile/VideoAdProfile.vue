<template>
  <v-dialog v-model="showDialog" width="1200" persistent>
    <v-card style="background: #f7f8fc; overflow: hidden">
      <v-container fluid class="pa-0">
        <v-row class="mx-0">
          <v-col cols="9" class="pa-0 pe-2">
            <ad-profile-top :selected_ads="selected_ads">
              <template slot="date_slot">
                <filter-date-range
                  ref="filterDateRange"
                  :hide-title="true"
                  :custom_design="true"
                />
              </template>
            </ad-profile-top>

            <v-row class="ma-0">
              <v-col cols="12" md="12" class="pa-0 pt-2 mb-2">
                <AdProfileGraph
                  :selected_ads.sync="selected_ads"
                  :serries_setting.sync="serries_setting"
                  ref="adProfileGraphRefs"
                />
              </v-col>
              <v-col
                cols="12"
                md="12"
                sm="12"
                class="pa-0 mb-2"
                style="overflow: hidden"
              >
                <AdsTotalInfoCard />
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="3" class="white pa-0">
            <v-card-title class="pa-1" style="color: #777">
              <span class="dialog-title"> {{ $tr("Ad Preview") }}</span>
              <v-spacer />
              <svg
                @click="closeDialog()"
                width="48px"
                height="48px"
                viewBox="0 0 700 560"
                fill="currentColor"
                style="transform: scaleY(-1); cursor: pointer"
              >
                <g>
                  <path
                    d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
                  />
                  <path
                    d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
                  />
                </g>
              </svg>
            </v-card-title>

            <div
              style="height: 560px; position: relative"
              class="ma-2 d-flex justify-center"
            >
              <div class="mobile-preview-layout">
                <div
                  style="
                    width: 70px;
                    height: 20px;
                    background-color: black;
                    border-radius: 30px;
                    position: absolute;
                    top: 2%;
                    left: 50%;
                    opacity: 0.4;
                    transform: translate(-50%);
                  "
                  class="mt-1"
                ></div>
                <iframe
                  loading="lazy"
                  style="
                    width: 100%;
                    height: 100%;
                    top: 0;
                    left: 0;
                    border: none;
                    padding: 0;
                    margin: 0;
                    border-radius: 27px;
                  "
                  src="https://www.canva.com/design/DAFTT6uQ2Hg/watch?embed"
                  allowfullscreen="allowfullscreen"
                  allow="fullscreen"
                >
                </iframe>
              </div>
              <div class="mobile-preview-border"></div>
            </div>
            <div
              class="white px-1"
              style="
                position: absolute;
                bottom: 8.5%;
                right: 12%;
                z-index: 9999;
                transform: translate(50%);
              "
            >
              <v-btn class="me-1" fab dark x-small color="primary">
                <v-icon dark> mdi-play </v-icon>
              </v-btn>
              <v-btn class="me-1" fab dark x-small color="primary">
                <v-icon dark> mdi-share-variant-outline </v-icon>
              </v-btn>
              <v-btn class="" fab dark x-small color="primary ">
                <v-icon dark> mdi-tray-arrow-down </v-icon>
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
import FilterDateRange from "../FilterDateRange.vue";
import AdProfileGraph from "./AdProfileGraph.vue";
import AdProfileTop from "./AdProfileTop.vue";
import AdsSerrries from "./AdsSerrries.vue";
import AdsTotalInfoCard from "./AdsTotalInfoCard.vue";
export default {
  components: {
    AdProfileTop,
    FilterDateRange,
    AdsSerrries,
    AdProfileGraph,
    AdsTotalInfoCard,
  },
  data() {
    return {
      showDialog: false,
      serries_setting: [],
      custom_selected_serries: [],
      selected_ads: {},
      counter: 0,
    };
  },
  methods: {
    async openDialog(item) {
      this.selected_ads = item;

      this.showDialog = true;
      await this.fetchHeaders();
      // if (this.counter > 0) {
      //   this.$refs.adProfileGraphRefs.applySavedFilter(
      //     item.ad_id,
      //     this.custom_selected_serries
      //   );
      // }
      this.counter++;
      try {
        // r(
        //   item.ad_id,
        //   this.custom_selected_serries
        // );
      } catch (error) {
        console.log("error", error);
      }
    },
    closeDialog() {
      this.showDialog = false;
      this.serries_setting = [];
      this.custom_selected_serries = [];
      this.selected_ads = {};
    },
    async fetchHeaders() {
      try {
        const response = await this.$axios.get("sub-system-header", {
          params: {
            tab_name: "advertisement_ad",
            serries_name: "video_ad_profile",
          },
        });
        this.serries_setting = response.data.settings;
        this.custom_selected_serries =
          response.data.selected_serries == null
            ? ["spend"]
            : response.data.selected_serries;
        console.log("response serresss", this.custom_selected_serries);
      } catch (error) {}
    },
  },
};
</script>

<style scoped>
.dialog-title {
  font-size: 18px;
  font-weight: 600;
  color: #777;
}
.mobile-preview-layout {
  width: 85%;
  height: 450px;
  border-radius: 35px;
  padding: 4px;
  border: 4px solid #777;
  z-index: 45;
  background-color: white;
}
.mobile-preview-border {
  width: 100%;
  height: 420px;
  border: 2px solid rgb(221, 217, 217);
  border-radius: 13px;
  position: absolute;
  top: 15%;
  left: 50%;
  transform: translate(-50%);
  z-index: 4;
}
</style>
<style>
.lkfugA {
  display: none !important;
}
._1VtxqQ {
  display: none !important;
}
</style>