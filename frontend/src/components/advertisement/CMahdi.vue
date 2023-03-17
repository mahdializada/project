<template>
  <div>
    <v-dialog v-model="dialog" width="900" persistent>
      <v-card
        elevation="2"
        v-bind="$attrs"
        v-on="$listeners"
        class="position-relative"
        min-height="500"
      >
        <v-card-title class="pa-2 pb-3" style="color: #777">
          <span class="dialog-title">
            {{
              $tr("target_audience") + " (" + selected_item?.name + ")"
            }}</span
          >
          <v-spacer />
          <svg
            @click="closeDialog()"
            width="44px"
            height="44px"
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

        <v-card-text class="px-0">
          <div class="w-full" style="border-bottom: 2px solid #e0e0e0">
            <div class="d-flex align-center ps-2">
              <div
                :class="`targeting-tab pa-1  text-uppercase ${
                  index == selected_tab ? 'active' : ''
                }`"
                v-for="(item, index) in ['targeting', 'placement & goals']"
                :key="index"
                @click="selected_tab = index"
              >
                {{ $tr(item) }}
              </div>
            </div>
          </div>

          <v-tabs-items
            v-model="selected_tab"
            class="px-2"
            style="height: 500px; overflow-y: auto"
          >
            <v-tab-item>
              <v-row class="mx-0" v-show="!api_loading">
                <v-col
                  cols="6 ma-0 px-0 pe-2 pb-1 pt-2"
                  style="border-right: 0.5px solid #e0e0e0; min-height: 500px"
                >
                  <p class="dialog-title-1 mb-1 pt-1 ps-1">
                    Demographic Infromation
                  </p>
                  <div style="border-bottom: 0.5px solid #e0e0e0" class="pa-1">
                    <p class="dialog-title-2 mb-1">Countries</p>
                    <div class="pb-2">
                      <v-chip
                        v-for="(item, key) in target_data.locations"
                        :key="key"
                        color="grey lighten-2 ps-1"
                        style="height: 34px"
                        outlined
                      >
                        <FlagIcon round :flag="getCountryData(item).code" />
                        <span class="black--text d-inline-block ps-1">{{
                          getCountryData(item).name
                        }}</span>
                      </v-chip>
                    </div>
                  </div>

                  <div style="border-bottom: 0.5px solid #e0e0e0" class="pa-1">
                    <p class="dialog-title-2 mb-1">Language</p>
                    <div class="pb-2">
                      <v-chip
                        color="grey lighten-2"
                        outlined
                        v-for="(item, index) in target_data.languages"
                        :key="index"
                      >
                        <v-icon left color="primary"> mdi-web </v-icon>
                        <span class="black--text d-inline-block"
                          >Afghanistan</span
                        >
                      </v-chip>
                    </div>
                  </div>

                  <div style="border-bottom: 0.5px solid #e0e0e0" class="pa-1">
                    <p class="dialog-title-2 mb-1">Gender</p>
                    <div class="pb-2">
                      <v-chip color="grey lighten-2" outlined>
                        <v-icon left color="blue"> {{ gender.icon }} </v-icon>
                        <span class="black--text d-inline-block">{{
                          gender.gender
                        }}</span>
                      </v-chip>
                    </div>
                  </div>
                  <div class="pa-1">
                    <p class="dialog-title-2 mb-1">Age Groups</p>
                    <div class="pb-2">
                      <v-chip
                        color="grey lighten-2"
                        class="me-1 mb-1"
                        outlined
                        v-for="(item, index) in target_data.age_groups"
                        :key="index"
                      >
                        <v-icon left color="blue">
                          {{ gender.icon }}
                        </v-icon>
                        <span class="black--text d-inline-block">{{
                          getAgeGroup(index)
                        }}</span>
                      </v-chip>
                    </div>
                  </div>
                </v-col>
                <v-col cols="6" class="pa-0 ps-2 pt-2">
                  <p class="dialog-title-1 mb-1 pt-1 ps-1">Devices</p>
                  <div style="border-bottom: 0.5px solid #e0e0e0" class="pa-1">
                    <p class="dialog-title-2 mb-1">Device</p>
                    <div class="pb-2">
                      <v-chip
                        color="grey lighten-2"
                        v-for="(item, index) in target_data.device_models"
                        :key="index"
                        outlined
                      >
                        <v-icon left color="blue"> mdi-apple</v-icon>
                        <span class="black--text d-inline-block">{{
                          item
                        }}</span>
                      </v-chip>
                    </div>
                  </div>

                  <div style="border-bottom: 0.5px solid #e0e0e0" class="pa-1">
                    <p class="dialog-title-2 mb-1">Connection Type</p>
                    <div class="pb-2">
                      <v-chip
                        color="grey lighten-2"
                        outlined
                        v-for="(item, index) in target_data.network_types"
                        :key="index"
                      >
                        <v-icon left color="blue">mdi-wifi</v-icon>
                        <span class="black--text d-inline-block">
                          {{ item }}</span
                        >
                      </v-chip>
                      <!-- <v-chip color="grey lighten-2" outlined>
                        <v-icon left color="blue">mdi-signal</v-icon>
                        <span class="black--text d-inline-block">
                          {{ item }}</span
                        >
                      </v-chip> -->
                    </div>
                  </div>
                </v-col>
              </v-row>
              <div v-show="api_loading">
                <v-skeleton-loader
                  v-for="item in 6"
                  :key="item"
                  v-bind="$attrs"
                  type="list-item-avatar-two-line"
                ></v-skeleton-loader>
              </div>
            </v-tab-item>
            <!-- placements and goals -->
            <v-tab-item>
              <v-row class="mx-0" v-show="!api_loading">
                <v-col
                  cols="6"
                  class="ma-0 px-0 pe-2 pb-1 pt-2 h-full"
                  style="border-right: 0.5px solid #e0e0e0; min-height: 500px"
                >
                  <p class="dialog-title-1 mb-1 pt-1 ps-1">Placements</p>
                  <div class="pa-1">
                    <p class="dialog-title-2 mb-1">placement Type</p>
                    <div
                      class="pb-2"
                      style="border-bottom: 0.5px solid #e0e0e0"
                    >
                      <v-chip color="grey lighten-2" outlined>
                        <v-icon left color="blue"
                          >mdi-cellphone-screenshot</v-icon
                        >
                        <span class="black--text d-inline-block">{{
                          target_data.placement_type
                        }}</span>
                      </v-chip>
                    </div>
                  </div>
                  <div class="pa-1">
                    <p class="dialog-title-2 mb-1">Placements</p>
                    <div class="pb-2">
                      <v-chip
                        color="grey lighten-2"
                        outlined
                        v-for="(item, index) in target_data.placements"
                        :key="index"
                      >
                        <v-icon left color="blue">mdi-cellphone-cog</v-icon>
                        <span class="black--text d-inline-block">{{
                          item
                        }}</span>
                      </v-chip>
                    </div>
                  </div>
                </v-col>
                <v-col cols="6 ma-0 px-0 ps-2 pb-1 pt-2">
                  <div class="pa-1">
                    <p class="dialog-title-2 mb-1">Goals</p>
                    <div class="pb-2">
                      <v-chip color="grey lighten-2" outlined>
                        <v-icon left color="blue">mdi-tag-heart</v-icon>
                        <span class="black--text d-inline-block">
                          {{ target_data.optimization_goal }}</span
                        >
                      </v-chip>
                    </div>
                  </div>
                </v-col>
              </v-row>
              <div v-show="api_loading">
                <v-skeleton-loader
                  v-for="item in 6"
                  :key="item"
                  v-bind="$attrs"
                  type="list-item-avatar-two-line"
                ></v-skeleton-loader>
              </div>
            </v-tab-item>
          </v-tabs-items>
        </v-card-text>
        <v-card-actions class="pa-2">
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="closeDialog"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import FlagIcon from "../common/FlagIcon.vue";
import countries_data from "../../configs/menus/countries_data";
export default {
  components: { FlagIcon },
  data() {
    return {
      api_loading: false,
      dialog: false,
      min: 10,
      max: 70,
      range: [10, 70],
      selected_tab: 0,
      selected_item: {},
      target_data: { locations: [] },
    };
  },
  computed: {
    gender() {
      let icon = "";
      let gender = "";
      if (
        this.target_data?.gender == "MALE" ||
        this.target_data?.gender == "GENDER_MALE"
      ) {
        gender = "male";
        icon = "mdi-human-male";
      } else if (
        this.target_data?.gender == "FEMALE" ||
        this.target_data?.gender == "GENDER_FEMALE"
      ) {
        gender = "female";
        icon = "mdi-human-female";
      } else {
        gender = "both";
        icon = "mdi-human-male-female";
      }

      return { gender, icon };
    },
  },
  methods: {
    getAgeGroup(index) {
      let data = this.target_data.age_groups[index];
      if (typeof data == "object") {
        console.log("type object");
        data = (data.min_age || "*") + "-" + (data.max_age || "*");
      }
      return data;

      // <v-chip color="grey lighten-2" outlined>
      //                   <v-icon left color="blue"> mdi-human-female </v-icon>
      //                   <span class="black--text d-inline-block">Female</span>
      //                 </v-chip>
      //                 <v-chip color="grey lighten-2" outlined>
      //                   <v-icon left color="blue">
      //                     mdi-human-male-female
      //                   </v-icon>
      //                   <span class="black--text d-inline-block">Both</span>
      //                 </v-chip>
    },

    getCountryData(item) {
      try {
        if (this.selected_item.platform.platform_name == "tiktok") {
          return { code: item.region_code?.toLowerCase(), name: item.name };
        }
        let country = countries_data.find(
          (row) => row.code == item?.toUpperCase()
        );
        let name = country.name;
        let code = item;
        return { code, name };
      } catch (error) {
        console.log("log error", error);
        return { code: "", name: "" };
      }
    },
    toggleDialog(data = {}) {
      if (!this.dialog) {
        this.dialog = true;
        this.selected_item = data;
        this.fetchAdsetTargeting();
      }
    },
    closeDialog() {
      this.dialog = false;
    },
    async fetchAdsetTargeting() {
      try {
        this.api_loading = true;
        const result = await this.$axios.get(
          "advertisement/adset-targeting/" + this.selected_item.adset_id,
          { params: { platform: this.selected_item.platform.platform_name } }
        );
        this.target_data = result.data;
        console.log("log", result.data);
      } catch (error) {
        console.log("error", error);
      }
      this.api_loading = false;
    },
  },
};
</script>
<style>
.custom-chip.v-chip {
  border-radius: 20px !important;
  font-size: 16px !important;
  height: 40px !important;
}
</style>
<style scoped>
.close-btn {
  height: 46px;
  width: 46px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50% !important;
  transition: all 0.3s;
  color: var(--close-btn-icon-color);
}

.close-btn:hover {
  background: rgba(0, 0, 0, 0.02);
}

.theme--dark .close-btn:hover {
  background: rgba(255, 255, 255, 0.05);
}

.select-close {
  height: 40px;
  width: 40px;
  color: var(--input-border-color) !important;
}

.custome .v-tab--active {
  border: 1px solid var(--v-surface-darken1);
  border-bottom: 1px solid transparent !important;
  border-radius: 3px 3px 0 0;
}
.custome .v-tab {
  border-bottom: 1px solid var(--v-surface-darken1);
}

/* map */
#container {
  height: 500px;
  min-width: 310px;
  max-width: 800px;
  margin: 0 auto;
}

.loading {
  margin-top: 10em;
  text-align: center;
  color: gray;
}
.targeting-tab {
  cursor: pointer;
  border-bottom: 3px solid transparent;
  font-size: 18px;
  font-weight: 500;
  padding-bottom: 7px;
}
.targeting-tab.active {
  cursor: pointer;
  border-bottom: 3px solid var(--v-primary-base);
  font-size: 18px;
  font-weight: 500;
  padding-bottom: 7px;
}
</style>