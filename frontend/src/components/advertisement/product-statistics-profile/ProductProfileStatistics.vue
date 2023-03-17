  <template>
  <!-- <v-overlay class="custom h-100" dark :value="showDialog" z-index="1000"> -->
  <div
    id="my-overlay"
    style=""
    :class="` ${!showDialog ? 'hide' : 'show'} ${overlay ? 'overlay' : ''}`"
  >
    <div
      style="z-index: 1000"
      :class="`graph-container  ${!showDialog ? 'hide' : 'show'} ${
        full_screen ? 'full-screen' : ''
      }`"
    >
      <v-scroll-x-transition>
        <div class="h-full" v-show="selected_profile == 1">
          <div class="d-flex h-full">
            <div :class="`product-info  ${media_expand ? 'expand' : ''}`">
              <div
                style="border-radius: 30px 0 0 0; width: 100%"
                class="white ps-4 d-flex align-center justify-space-between"
              >
                <div class="d-flex align-center">
                  <v-btn
                    fab
                    x-small
                    class="me-1 primary"
                    @click="closeDialog()"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-btn fab x-small class="primary" @click="fullScreen()">
                    <v-icon>mdi-fullscreen</v-icon>
                  </v-btn>
                  <div
                    class="grey rounded mx-3"
                    style="height: 40px; width: 1px"
                  ></div>
                  <span>
                    <v-avatar size="40" class="grey lighten-3 me-1">
                      <v-avatar size="35" color="white">
                        <v-avatar size="30" color="white">
                          <v-avatar
                            size="30"
                            class="text-h6 primary white--text"
                          >
                            {{ selected_item.pcode[0] }}
                          </v-avatar>
                        </v-avatar>
                      </v-avatar>
                    </v-avatar>
                    <span class="dialog-title"> {{ selected_item.pcode }}</span>
                  </span>
                </div>
                <div class="d-flex align-center">
                  <slot name="date_slot">
                    <FilterDateRange
                      ref="filterDateRange"
                      :dateRangeProp="date_range"
                      :hide-title="true"
                      @dateChanged="FilterByDateRange($event)"
                      :custom_design="true"
                      :nudge_left="100"
                    />
                  </slot>
                  <v-img
                    src="/new_landing/product_profile_header.svg"
                    height="60px"
                  ></v-img>
                  <!-- <ad-profile-svg class="ms-2" style="z-index: 90000" /> -->
                </div>
              </div>
              <div style="height: 90%; overflow-y: auto" class="pe-1">
                <div style="width: 100%">
                  <div class="py-3 d-flex align-center">
                    <ProductTotalInfoCard
                      :profile_data="profile_data"
                      :loading.sync="loading_profile"
                    />
                  </div>
                  <ProductStatisticsGeneralInfo
                    :profile_data="profile_data"
                    :loading.sync="loading_profile"
                  />
                </div>

                <div
                  style="width: 100%"
                  class="px-2 py-2 pe-0 d-flex justify-space-between"
                >
                  <SalesCard
                    :key="chart_key"
                    style="min-height: 200; width: 49%"
                    :value="1837.32"
                    :percentage="3.2"
                    :loading="loading_chart"
                    :percentage-label="$tr('dashboard.lastweek')"
                    :action-label="$tr('dashboard.viewReport')"
                    :xaxis.sync="xaxis"
                    :series.sync="series"
                  ></SalesCard>
                  <ColumnChart
                    :key="chart_key + 100"
                    style="min-height: 200; width: 49%"
                    :profitability.sync="profitability"
                    :xaxis="xaxis"
                    :series="series"
                    :value="1837.32"
                    :percentage="3.2"
                    :loading="loading_chart"
                    :percentage-label="$tr('dashboard.lastweek')"
                    :action-label="$tr('dashboard.viewReport')"
                    :colors="colors"
                  ></ColumnChart>
                </div>
              </div>
            </div>
            <div :class="`product-media white  ${media_expand ? 'hide' : ''}`">
              <LandingPagePreview
                :profile_data="profile_data"
                :loading.sync="loading_profile"
                v-show="media_index == 0"
                class="w-full"
                @close="closeDialog()"
                @updateFile="updateFile"
              />
              <LandingPageMedia
                v-show="media_index == 1"
                ref="mediaRefs"
                class="w-full"
              />

              <div class="horizental-tab">
                <div
                  :class="`d-flex justify-center ${
                    media_index != index ? 'primary' : 'white'
                  } mb-1 horizental-tab-item2`"
                  v-for="(item, index) in 2"
                  :key="index"
                  :style="media_index == index ? ' z-index: 1' : 'z-index:0;'"
                  style="padding-top: 10px"
                  @click="toggleMedia(index)"
                >
                  <span
                    :class="
                      media_index != index ? 'white--text' : 'primary--text'
                    "
                  >
                    <svg
                      v-if="index == 0"
                      xmlns="http://www.w3.org/2000/svg"
                      width="23"
                      height="31"
                      viewBox="0 0 23 38.999"
                    >
                      <path
                        id="preview"
                        d="M-2569-9329a4,4,0,0,1-4-4v-31a4,4,0,0,1,4-4h15a4,4,0,0,1,4,4v31a4,4,0,0,1-4,4Zm-2-35v31a2,2,0,0,0,2,2h15a2,2,0,0,0,2-2v-31a2,2,0,0,0-2-2h-15A2,2,0,0,0-2571-9364Zm4,31a2,2,0,0,1-2-2v-3a2,2,0,0,1,2-2h11a2,2,0,0,1,2,2v3a2,2,0,0,1-2,2Zm-.5-5v3a.5.5,0,0,0,.5.5h11a.5.5,0,0,0,.5-.5v-3a.5.5,0,0,0-.5-.5h-11A.5.5,0,0,0-2567.5-9338Zm.5-4a2,2,0,0,1-2-2v-18a2,2,0,0,1,2-2h11a2,2,0,0,1,2,2v18a2,2,0,0,1-2,2Zm0-2h11v-18h-11Z"
                        transform="translate(2573 9368)"
                        fill="currentColor"
                      />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      width="23"
                      height="27.5"
                      viewBox="0 0 23 27.5"
                    >
                      <path
                        id="media"
                        d="M-2567.5-9340.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h12a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm-1.5-4.5a4,4,0,0,1-4-4v-15a4,4,0,0,1,4-4h15a4,4,0,0,1,4,4v15a4,4,0,0,1-4,4Zm-2-19v15a2,2,0,0,0,2,2h15a2,2,0,0,0,2-2v-15a2,2,0,0,0-2-2h-15A2,2,0,0,0-2571-9364Zm6.437,11.212v-7.429a.852.852,0,0,1,1.293-.738l6.287,3.717a.856.856,0,0,1,0,1.474l-6.287,3.715a.847.847,0,0,1-.435.121A.857.857,0,0,1-2564.562-9352.787Z"
                        transform="translate(2573 9368)"
                        fill="currentColor"
                      />
                    </svg>
                  </span>
                </div>
                <v-btn
                  @click="toggleMediaExpand()"
                  x-small
                  fab
                  icon
                  color="primary"
                  class="primary mt-1 pa-0"
                  ><v-icon color="white">{{
                    media_expand ? "mdi-chevron-left" : "mdi-chevron-right"
                  }}</v-icon></v-btn
                >
              </div>
            </div>
          </div>
        </div>
      </v-scroll-x-transition>
      <v-scroll-x-transition>
        <AdvertisementGraphProfileProduct
          ref="advertisementRefs"
          @close="closeDialog()"
          @fullScreen="fullScreen()"
          :current_tab="'item_code'"
          v-show="selected_profile == 0"
        />
      </v-scroll-x-transition>
      <v-scroll-x-transition>
        <div v-show="selected_profile == 2">
          <AdvertisementProfilePercentage
            @closeProfile="closeDialog()"
            :current_tab="'item_code'"
            :selected_item.sync="getSelectedItem"
            ref="profilePercentageRefs"
            @fullScreen="fullScreen != fullScreen"
          />
        </div>
      </v-scroll-x-transition>
      <v-scroll-x-transition>
        <div class="h-full" v-show="selected_profile == 3">
          <OnlineSalesNote
            :selected_item="selected_item"
            ref="OnlineSalesNoteRefs"
            @fullScreen="fullScreen"
            @closeDialog="closeDialog"
          />
        </div>
      </v-scroll-x-transition>

      <!-- design request -->
      <v-scroll-x-transition>
        <OnlineSalesDesignRequest
          ref="designRequestRefs"
          :selected_product="selected_item"
          v-show="selected_profile == 4"
          @closeDialog="closeDialog()"
          @fullScreen="fullScreen()"
        />
        <!-- <div class="h-full" v-show="selected_profile == 3">design request</div> -->
      </v-scroll-x-transition>

      <!--total info card-->

      <profile-horizental-tab
        :selected_profile.sync="selected_profile"
        @onTabChange="onTabChange"
        :tabs="['graph', 'note', 'profile', 'details', 'design_request']"
      />
    </div>
  </div>
  <!-- </v-overlay> -->
</template>

  <script>
import FilterDateRange from "../FilterDateRange.vue";
import AdvertisementGraphProfileProduct from "../graph/AdvertisementGraphProfileProduct.vue";
import ColumnChart from "./columnChart.vue";
import LandingPageMedia from "./LandingPageMedia.vue";
import LandingPagePreview from "./LandingPagePreview.vue";
import ProductStatisticsGeneralInfo from "./ProductStatisticsGeneralInfo.vue";
import ProductTotalInfoCard from "./ProductTotalInfoCard.vue";
import SalesCard from "./SalesCard.vue";
import moment from "moment";
import ProfileHorizentalTab from "../graph/ProfileHorizentalTab.vue";
import AdvertisementProfilePercentage from "../graph/AdvertisementProfilePercentage.vue";
import OnlineSalesNote from "../../online_sales_management_system/online-sales-profile/OnlineSalesNote.vue";
import OnlineSalesDesignRequest from "../../online_sales_management_system/online-sales-profile/OnlineSalesDesignRequest.vue";

export default {
  components: {
    ProductTotalInfoCard,
    ProductStatisticsGeneralInfo,
    SalesCard,
    ColumnChart,
    LandingPagePreview,
    LandingPageMedia,
    AdvertisementGraphProfileProduct,
    FilterDateRange,
    ProfileHorizentalTab,
    AdvertisementProfilePercentage,
    OnlineSalesNote,
    OnlineSalesDesignRequest,
  },
  data() {
    return {
      full_screen: false,
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      selected_profile: 0,
      tabs: ["mdi-chart-bar"],
      colors: {
        profit: "#42a2e2",
        medium_profit: "#42a2e2",
        less_profit: "#fbcc5f",
        loss: "#39a0e6",
        medium_loss: "#39a0e6",
        more_loss: "#cacbcf",
      },
      chart_key: 0,
      loading_chart: false,
      selectedItems: [],
      profitability: [],

      series: [{ name: "", data: [] }],
      xaxis: { categories: [] },
      showDialog: false,
      profile_data: {},
      selected_item: { pcode: "R", pname: "pr30" },
      init_product_chart: false,
      init_product_graph: false,
      loading_profile: false,
      overlay: false,
      no_data: false,
      media_index: 0,
      media_expand: false,
    };
  },
  computed: {
    getSelectedItem() {
      return [this.selected_item];
    },
  },
  mounted() {},
  methods: {
    updateFile(data) {
      this.profile_data.attachments = data.attachments;
      this.$emit("updateAttachment", data);
    },
    FilterByDateRange(dates) {
      console.log("from  range", dates);
      this.date_range = dates;
      this.xaxis.categories = [];
      this.series[0].data = [];
      this.fetchGraphInfo();
    },

    onTabChange(index) {
      if (index == this.selected_profile) return;
      if (index == 1) {
        if (!this.init_product_chart) {
          if (this.loading_profile)
            return this.$toasterNA(
              "orange",
              this.$tr("Loading Profile data please wait...")
            );
          if (this.no_data) {
            this.$toasterNA("orange", this.$tr("Product info not added"));
            return;
          }
        }
        this.init_product_chart = true;
      }
      if (index == 2) {
        this.$refs.profilePercentageRefs.showProfile();
      }
      if (index == 3) {
        this.$refs.OnlineSalesNoteRefs.fetchNotes();
      }
      if (index == 4) {
        this.$refs.designRequestRefs.fetchDesignRequests();
      }
      this.selected_profile = index;
    },
    async openDialog(item) {
      try {
        this.init_product_graph = true;
        this.showDialog = true;
        this.selected_item = JSON.parse(JSON.stringify(item));
        this.$refs.advertisementRefs.showProfile([item]);
        setTimeout(() => {
          this.overlay = true;
        }, 500);

        this.fetchProfileData();
      } catch (error) {
        console.log("error", error);
      }
    },

    closeDialog() {
      this.overlay = false;
      this.profile_data = {};
      this.xaxis.categories = [];
      this.series[0].data = [];
      this.showDialog = false;
      this.init_product_graph = false;
      this.init_product_chart = false;
      this.selected_profile = 0;
      this.$refs.advertisementRefs.closeProfile();
      this.chart_key++;
      this.no_data = false;
      this.$refs.mediaRefs.resetMedia();
      this.media_index = 0;
      this.$refs.profilePercentageRefs.resetProfile();
    },

    async fetchProfileData() {
      try {
        this.loading_profile = true;
        const response = await this.$axios.get(
          "advertisement/product-profile-info",
          { params: { item_code: this.selected_item.pcode } }
        );
        if (Object.keys(response.data).length == 0) {
          this.loading_profile = false;
          this.no_data = true;
          return;
        }
        this.profile_data = response.data;
        this.fetchGraphInfo();
        this.chart_key++;
      } catch (error) {
        console.error("error", error);
      }
      this.loading_profile = false;
    },
    async fetchGraphInfo() {
      try {
        this.loading_chart = true;
        const response = await this.$axios.get(
          "advertisement/product-profile-history-chart",
          {
            params: {
              id: this.profile_data.id,
              column: "prod_profit",
              ...this.date_range,
            },
          }
        );

        this.sortData(response.data);
        this.chart_key++;
      } catch (error) {
        console.error("error", error);
      }
      this.loading_chart = false;
    },

    sortData(data) {
      this.profitability = [];

      this.series[0].name = this.$tr("prod_profit");
      for (const key in data) {
        this.xaxis.categories.push(key);

        this.series[0].data.push(
          parseInt(data[key][data[key].length - 1].changed_value)
        );
        this.profitability.push(
          this.getColor(data[key][data[key].length - 1].profit_type)
        );
        this.series = [...this.series];
        this.xaxis = { ...this.xaxis };
      }
    },
    getColor(type) {
      return this.colors[type];
    },
    toggleMedia(index) {
      this.$refs.mediaRefs.fetchMedia(this.selected_item);
      this.media_index = index;
    },
    toggleMediaExpand() {
      this.media_expand = !this.media_expand;
      console.log("expand", this.media_expand);
    },
    fullScreen() {
      this.full_screen = !this.full_screen;
    },
  },
};
</script>
  <style scoped>
.product-media {
  width: 26%;
  transition: all 0.5s;
  position: relative;
}
.product-media.hide {
  transform: translateX(100%);
  width: 5%;
  transition: all 0.5s;
}
.product-info {
  width: 74%;
  padding-right: 54px;
  transition: all 0.5s;
  height: 100%;
}
.product-info.expand {
  width: 95%;
  padding-right: unset !important;
  transition: all 0.5s;
  height: 100%;
}
/* style="width: 95%; height: 100%" :class="`product-info  */
.card-background {
  background: #f7f8fc;
}
.dialog-title {
  font-size: 20px !important;
  font-weight: 500 !important;
  color: #777 !important;
}
.graph-container {
  position: relative;
  background: #f7f8fc;
  position: fixed;
  right: 0;
  bottom: 0;
  height: 100% !important;
  width: 1480px;
  border-radius: 30px 0 0 30px;
  transition: all 0.5s;
}
.graph-container.full-screen {
  width: 97.5% !important;
}

.graph-container.hide {
  transform: translateX(110%);
}
.v-application--is-rtl .graph-container.hide {
  transform: translateX(-110%);
}
</style>


  <style>
.horizental-tab-item2 {
  width: 40px;
  height: 70px;
  /* border: 1px solid red !important; */
  border-top-left-radius: 10px;
  border-bottom-left-radius: 40px;
  position: relative;
}
.horizental-tab-item2::after {
  border-color: #777 !important;
  /* border-width: 1px !important; */
  /* border-bottom-width: 2px !important;
  border-left-width: 2px !important; */

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

.v-expansion-panel-content__wrap {
  padding: 10px !important;
}
#my-overlay.hide {
  transform: translateX(110%);
}
.v-application--is-rtl #my-overlay.hide {
  transform: translateX(-110%);
}
#my-overlay {
  position: fixed; /*
   Sit on top of the page content */
  width: 100%; /* Full width (cover the whole page) */
  height: 100%; /* Full height (cover the whole page) */
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000; /* Specify a stack order in case you're using a different order for other elements */
  cursor: pointer; /* Add a pointer on hover */
  transition: all 0.5s;
}
#my-overlay.overlay {
  background-color: rgba(
    0,
    0,
    0,
    0.5
  ) !important; /* Black background with opacity */
}
.horizental-tab {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translate(-100%, -50%);
}
</style>
