<template>
  <div>
    <AdvertisementProfileTop
      @close="closeProfile()"
      @fullScreen="fullScreen()"
      :selected_item.sync="selected_item"
      :current_tab.sync="current_tab"
    >
      <template slot="date_slot">
        <div class="d-flex align-center">
          <FilterDateRange
            ref="filterDateRange"
            :dateRangeProp="date_range"
            :hide-title="true"
            @dateChanged="Filter($event)"
            @filterType="typeChange($event)"
            :custom_design="true"
            :nudge_left="130"
          />
          <v-img
            src="/new_landing/product_profile_header.svg"
            height="80"
          ></v-img>
        </div>
      </template>
    </AdvertisementProfileTop>

    <AdvertisementGraph2
      :series_type="'percentage'"
      :request_id.sync="generateId"
      :model_name.sync="current_tab"
      ref="graphRefs"
      :date_range.sync="date_range"
      :isLarg="true"
      :serries_setting.sync="serries_setting"
    />
    <div>
      <div class="px-2 py-3 d-flex">
        <radial-bar-chart
          v-for="(item, index) in percentage_items"
          :key="index"
          :item="item"
          :loading="api_loading"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FilterDateRange from "../FilterDateRange.vue";
import AdvertisementProfileTop from "./AdvertisementProfileTop.vue";
import moment from "moment";
import AdvertisementGraph2 from "./AdvertisementGraph2.vue";
import RadialBarChart from "./RadialBarChart.vue";

export default {
  components: {
    AdvertisementProfileTop,
    FilterDateRange,
    AdvertisementGraph2,
    RadialBarChart,
  },
  props: {
    current_tab: String,
    selected_item: Array,
    request_id: String,
  },
  computed: {
    generateId() {
      if (this.init_data) {
        switch (this.current_tab) {
          case "campaign":
            return this.selected_item[0].campaign_id;
          case "item_code":
            return this.selected_item[0].pcode;
          case "sales_type":
            return this.selected_item[0].sales_type;
          case "ad_set":
            return this.selected_item[0].adset_id;
          case "ad":
            return this.selected_item[0].ad_id;
          default:
            return this.selected_item[0].id;
        }
      }
      return "";
    },
  },
  data() {
    return {
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      percentage_items: [
        { title: "audience_quality", value: 0, color: "primary" },
        { title: "view_quality_percantage", value: 0, color: "#facbd5" },
        { title: "ad_attraction_percentage", value: 0, color: "#f8ebc6" },
        { title: "buyer_percentage", value: 0, color: "#badef7" },
      ],
      serries_setting: [],
      init_data: false,
      api_loading: false,
      custom_selected_serries: [
        "view_quality_percantage",
        "ad_attraction_percentage",
        "buyer_percentage",
      ],
    };
  },

  methods: {
    closeProfile() {
      this.$emit("closeProfile");
    },
    fullScreen() {
      this.$emit("fullScreen");
    },
    showProfile() {
      try {
        this.$refs.filterDateRange.onDateSelected(5);
        if (!this.init_data) {
          this.init_data = true;
          this.$refs.graphRefs.applySavedFilter(
            this.generateId,
            this.custom_selected_serries
          );
          this.fetchPercentageCardData();
        }
      } catch (error) {
        console.error("error", error);
      }
    },
    resetProfile() {
      this.init_data = false;
      this.$refs.filterDateRange.clear();
    },

    Filter(dates) {
      if (!this.init_data) return;
      this.date_range = dates;
      this.$refs.graphRefs.Filter(dates);
      this.fetchPercentageCardData();
    },

    typeChange(type) {
      this.$refs.graphRefs.addType(type);
    },

    async fetchPercentageCardData() {
      try {
        this.api_loading = true;
        let url = "advertisement/ad-insides";
        const body = {
          ...this.date_range,
          column_name: [],
          request_id: this.generateId,
          model_name: this.current_tab,
        };
        const { data } = await this.$axios.post(url, body);
        this.percentage_items = this.percentage_items.map((row) => {
          if (row.title != "audience_quality") row.value = data[row.title];
          return row;
        });
        this.percentage_items = [...this.percentage_items];
      } catch (error) {
        console.error("error", error);
      }
      this.api_loading = false;
    },
  },
};
</script>

<style>
</style>