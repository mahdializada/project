<template>
  <div>
    <AdvertisementProfileTop
      @close="closeProfile()"
      @fullScreen="fullScreen()"
      :selected_item.sync="selected_item"
      :current_tab="'ad_set'"
    >
      <template slot="date_slot">
        <div class="d-flex align-center">
          <FilterDateRange
            ref="filterDateRange"
            :dateRangeProp="date_range"
            :hide-title="true"
            @dateChanged="FilterByDate($event)"
            :custom_design="true"
            :nudge_left="145"
          />
          <v-img
            src="/new_landing/product_profile_header.svg"
            height="80"
          ></v-img>
        </div>
      </template>
    </AdvertisementProfileTop>

    <div>
      <v-row class="mx-0 mt-0">
        <v-col cols="7" class="pe-0 mb-0 pb-1">
          <p
            style="color: #777; font-size: 18px; font-weight: 600"
            class="text-uppercase mb-1"
          >
            {{ $tr("target") }}
          </p>
          <v-card
            flat
            class="white"
            min-height="440"
            style="max-height: 440px; overflow-y: auto"
            v-show="!api_loading"
          >
            <v-row class="mx-0">
              <v-col
                cols="6"
                class="ma-0 my-2 px-2 pb-1 pa-0"
                style="border-right: 0.5px solid #e0e0e0"
              >
                <p class="dialog-title-1 mb-1 pt-1">Demographic Infromation</p>
                <div style="border-bottom: 0.5px solid #e0e0e0" class="">
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
              <v-col cols="6" class="pa-0 px-2 my-2">
                <p class="dialog-title-1 mb-1 pt-1">Devices</p>
                <div style="border-bottom: 0.5px solid #e0e0e0" class="">
                  <p class="dialog-title-2 mb-1">Device</p>
                  <div class="pb-2">
                    <v-chip
                      color="grey lighten-2"
                      v-for="(item, index) in target_data.device_models"
                      :key="index"
                      outlined
                    >
                      <v-icon left color="blue"> mdi-apple</v-icon>
                      <span class="black--text d-inline-block">{{ item }}</span>
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
          </v-card>
          <div v-show="api_loading">
            <v-skeleton-loader
              v-for="item in 6"
              :key="item"
              v-bind="$attrs"
              type="list-item-avatar-two-line"
            ></v-skeleton-loader>
          </div>
        </v-col>
        <v-col cols="5" class="mb-0 pb-1">
          <p
            style="color: #777; font-size: 18px; font-weight: 600"
            class="text-uppercase mb-1"
          >
            {{ $tr("placement_and_goals") }}
          </p>
          <v-card
            flat
            class="white px-2 py-1"
            min-height="440"
            style="max-height: 440px; overflow-y: auto"
            v-show="!api_loading"
          >
            <p class="dialog-title-1 mb-1 pt-1 ps-1">Placements</p>

            <div class="pa-1" style="border-bottom: 0.5px solid #e0e0e0">
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

            <div class="pa-1">
              <p class="dialog-title-2 mb-1">placement Type</p>
              <div class="pb-2" style="border-bottom: 0.5px solid #e0e0e0">
                <v-chip color="grey lighten-2" outlined>
                  <v-icon left color="blue">mdi-cellphone-screenshot</v-icon>
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
                  <span class="black--text d-inline-block">{{ item }}</span>
                </v-chip>
              </div>
            </div>
          </v-card>
          <div v-show="api_loading" style="min-height: 400px">
            <v-skeleton-loader
              v-for="item in 6"
              :key="item"
              v-bind="$attrs"
              type="list-item-avatar-two-line"
            ></v-skeleton-loader>
          </div>
        </v-col>
      </v-row>

      <div class="d-flex align-center">
        <div style="width: 400px" class="ms-3">
          <AdvertisementProfileDataTable
            ref="budgetRefs"
            @onPaginate="onPaginate"
            :last_page.sync="budget_last_page"
            :show_pagination="true"
            card_title="budget_history"
            :items.sync="budget_data"
            height="300"
            :headers="budget_headers"
            :loading_api.sync="budget_loading"
          />
        </div>
        <div style="width: 400px" class="ms-3">
          <AdvertisementProfileDataTable
            ref="bidRefs"
            @onPaginate="onPaginate"
            :last_page.sync="bid_last_page"
            :show_pagination="true"
            card_title="bid_history"
            :items.sync="bid_data"
            height="300"
            :headers="bid_headers"
            :loading_api.sync="bid_loading"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AdvertisementProfileTop from "./AdvertisementProfileTop.vue";
import countries_data from "../../../configs/menus/countries_data";
import FlagIcon from "../../common/FlagIcon.vue";
import AdvertisementProfileDataTable from "./AdvertisementProfileDataTable.vue";
import moment from "moment";
import FilterDateRange from "../FilterDateRange.vue";
export default {
  components: {
    AdvertisementProfileTop,
    FlagIcon,
    AdvertisementProfileDataTable,
    FilterDateRange,
  },
  props: {
    current_tab: String,
    selected_item: Array,
    request_id: String,
  },
  computed: {},
  data() {
    return {
      init_data: false,
      api_loading: false,
      dialog: false,
      min: 10,
      max: 70,
      range: [10, 70],
      selected_tab: 0,

      target_data: { locations: [] },

      bid_headers: [
        // { text: "budget", value: "daily_budget", align: "center" },
        {
          text: "bid",
          value: "bid",
          align: "center",
        },
        {
          text: "date ",
          value: "bid_data_date",
          align: "center",
        },

        {
          text: "user",
          value: "changed_by",
          align: "center",
        },
      ],
      budget_headers: [
        // { text: "budget", value: "daily_budget", align: "center" },
        {
          text: "budget",
          value: "daily_budget",
          align: "center",
        },
        {
          text: "date ",
          value: "bid_data_date",
          align: "center",
        },

        {
          text: "user",
          value: "changed_by",
          align: "center",
        },
      ],

      date_range: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },

      bid_loading: false,
      bid_last_page: 0,
      bid_data: [],

      budget_loading: false,
      budget_data: [],
      budget_last_page: 1,
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
    closeProfile() {
      this.$emit("closeProfile");
    },
    fullScreen() {
      this.$emit("fullScreen");
    },
    showProfile() {
      if (!this.init_data) {
        this.fetchAdsetTargeting();
        this.fetchBidHistory();
        this.fetchBudgetHisotry();
      }
    },
    resetProfile() {
      this.init_data = false;
      this.$refs.budgetRefs.resetDatatable();
      this.$refs.bidRefs.resetDatatable();
      this.$refs.filterDateRange.onDateSelected(5);
    },

    getAgeGroup(index) {
      let data = this.target_data.age_groups[index];
      if (typeof data == "object") {
        console.log("type object");
        data = (data.min_age || "*") + "-" + (data.max_age || "*");
      }
      return data;
    },

    getCountryData(item) {
      try {
        if (this.selected_item[0].platform.platform_name == "tiktok") {
          return { code: item.region_code?.toLowerCase(), name: item.name };
        }
        let country = countries_data.find(
          (row) => row.code == item?.toUpperCase()
        );
        let name = country.name;
        let code = item;
        return { code, name };
      } catch (error) {
        return { code: "", name: "" };
      }
    },

    async fetchAdsetTargeting() {
      try {
        this.api_loading = true;
        const result = await this.$axios.get(
          "advertisement/adset-targeting/" + this.selected_item[0].adset_id,
          { params: { platform: this.selected_item[0].platform.platform_name } }
        );
        this.target_data = result.data;
        this.init_data = true;
      } catch (error) {
        console.log("error", error);
      }
      this.api_loading = false;
    },

    async fetchBidHistory(page = 1) {
      try {
        this.bid_loading = true;
        const result = await this.$axios.get(
          "advertisement/adset-bid-budget-history",
          {
            params: {
              id: this.selected_item[0].adset_id,
              type: "bid",
              page: page,
              ...this.date_range,
            },
          }
        );
        this.bid_data = result.data?.data;
        this.bid_last_page = result.data?.last_page;
        this.$refs.bidRefs.paginate_loading = false;
      } catch (error) {
        console.log("error", error);
      }
      this.bid_loading = false;
    },
    async fetchBudgetHisotry(page = 1) {
      try {
        this.budget_loading = true;
        const result = await this.$axios.get(
          "advertisement/adset-bid-budget-history",
          {
            params: {
              id: this.selected_item[0].adset_id,
              type: "budget",
              page: page,
              ...this.date_range,
            },
          }
        );
        this.budget_data = result.data?.data;
        this.budget_last_page = result.data?.last_page;
        this.$refs.budgetRefs.paginate_loading = false;
      } catch (error) {
        console.log("error", error);
      }
      this.budget_loading = false;
    },

    async onPaginate(params) {
      try {
        if (params.card_title == "bid_history") {
          this.fetchBidHistory(params.page);
        } else {
          this.fetchBudgetHisotry(params.page);
        }
      } catch (error) {
        console.error(error);
      }
    },

    FilterByDate(date) {
      if (this.init_data) {
        this.$refs.budgetRefs.resetDatatable();
        this.$refs.bidRefs.resetDatatable();
        this.date_range = date;
        this.fetchBidHistory();
        this.fetchBudgetHisotry();
      }
    },
  },
};
</script>

<style>
</style>