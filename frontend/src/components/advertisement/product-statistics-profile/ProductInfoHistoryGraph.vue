<template>
  <v-dialog v-model="showDialog" width="1100" persistent>
    <v-card class="white">
      <v-card-title class="pa-2 pb-3" style="color: #777">
        <span class="dialog-title d-inline-block ps-1 text-capitalize">
          {{ $tr("history") }}</span
        >
        <v-spacer />
        <div class="d-flex">
          <FilterDateRange
            ref="filterDateRange"
            :dateRangeProp="date_range"
            :hide-title="true"
            @dateChanged="FilterByDateRange($event)"
            :custom_design="true"
          />

          <svg
            @click="closeDialog()"
            class="ms-2"
            width="38px"
            height="38px" 
            viewBox="0 0 700 560"
            fill="currentColor"
            style="
              transform: scaleY(-1);
              cursor: pointer;
              align-self: flex-start;
            "
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
        </div>
      </v-card-title>

      <div class="d-flex pa-3">
        <div
          style="border: 2px solid #eeee; width: 60%; height: 400px"
          class="rounded-lg d-flex align-end pa-1"
        >
          <area-chart
            :key="key"
            class="w-full"
            :loading.sync="loading_chart"
            :xaxis.sync="xaxis"
            :series.sync="series"
            :column.sync="column"
            :profile_data="profile_data"
            :users="users"
          />
        </div>

        <div
          v-if="!loading"
          style="height: 400px; overflow-y: auto; width: 40%"
          class=""
        >
          <v-timeline align-top dense flat class="customTimeLine3 pt-0 pe-1">
            
            <NoRemark v-if="(histories.length == 0 || histories == null) && !loading" style="position:absolute; top: 75px;left:25%;" />
            <v-timeline-item
              class="pb-1 align-start"
              v-for="(history, index) in histories"
              :key="index"
              :color="icons[column]"
            >
              <template v-slot:icon>
                <svg-icon
                  class="white--text"
                  :icon="column"
                  :width="20"
                  :height="20"
                ></svg-icon>
              </template>
              <div>
                <div
                  class="
                    text-body-1
                    mb-1
                    font-weight-regular
                    d-flex
                    justify-space-between
                  "
                >
                  <span class="">
                    <span class="font-weight-bold" style="color: #777"
                      >{{ $tr(column) }}
                    </span>
                    <span style="color: #777">{{ $tr("set_to") }}</span>
                    <span class="font-weight-bold" style="color: #777"
                      >{{ history.changed_value }}
                    </span>
                  </span>
                  <span class="grey--text">04:04 PM</span>
                </div>
                <span class="d-flex align-end">
                  <v-avatar size="28">
                    <img :src="history.changed_by.image" />
                  </v-avatar>
                  <span class="ms-1" style="color: #777">{{
                    history.changed_by.firstname +
                    " " +
                    history.changed_by.lastname
                  }}</span>
                </span>
              </div>

              <v-divider class="my-1"></v-divider>
            </v-timeline-item>
          </v-timeline>  
        </div>
        <div v-else class="px-5" style="width: 40%">
          <v-skeleton-loader
            v-for="i in 3"
            :key="i"
            type=" list-item-avatar, list-item-two-line"
          ></v-skeleton-loader>
        </div>
      </div>

      <v-card-actions class="pb-3 px-4">
        <v-spacer></v-spacer>
        <v-btn class="custom-btn" color="primary" @click="closeDialog()">
          {{ $tr("close") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import SvgIcon from "../../design/components/SvgIcon.vue";
import FilterDateRange from "../FilterDateRange.vue";
import AreaChart from "./AreaChart.vue";
import moment from "moment";
import NoRemark from "../remarks/NoRemark.vue";

export default {
  components: { SvgIcon, FilterDateRange, AreaChart, NoRemark },

  data() {
    return {
      key: 0,
      column: "",
      profile_data: {},
      showDialog: false,
      histories: [],
      loading: false,
      loading_chart: false,
      xaxis: { categories: [] },
      series: [
        {
          name: "",
          user: [],
          data: [],
        },
      ],
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },

      icons: {
        prod_cost: "#e9f6fe",
        prod_available_quantity: "#fefaee",
        prod_max_adver_cost: "#feeff2",
        prod_profit: "#dbefef",
      },
      users: {},
    };
  },
  methods: {
    openDialog(data, column) {
      this.column = JSON.parse(JSON.stringify(column));
      this.profile_data = JSON.parse(JSON.stringify(data));
      this.showDialog = true;
      this.fetchHistory();
      this.fetchGraphInfo();
    },
    closeDialog() {
      this.showDialog = false;
      this.series = [
        {
          name: "",
          data: [],
        },
      ];
      this.xaxis.categories = [];
    },
    async fetchHistory() {
      try {
        this.loading = true;
        const response = await this.$axios.get(
          "advertisement/product-profile-history",
          {
            params: {
              id: this.profile_data.id,
              column: this.column,
              ...this.date_range,
            },
          }
        );
        this.histories = response.data;
      } catch (error) {
        console.error("error", error);
      }
      this.loading = false;
    },
    async fetchGraphInfo() {
      try {
        this.loading_chart = true;
        const response = await this.$axios.get(
          "advertisement/product-profile-history-chart",

          {
            params: {
              id: this.profile_data.id,
              column: this.column,
              ...this.date_range,
            },
          }
        );
        this.chart_data = response.data;

        this.sortData();
        this.key++;
      } catch (error) {
        console.error("error", error);
      }
      this.loading_chart = false;
    },
    sortData() {
      this.users = {};
      this.series[0].name = this.column;
      for (const key in this.chart_data) {
        this.xaxis.categories.push(key);

        this.chart_data[key].forEach((row) => {
          if (!this.users[key]) this.users[key] = [];

          this.users[key].push({
            ...row.changed_by,
            value: row.changed_value,
          });
        });
        this.series[0].data.push(
          parseInt(
            this.chart_data[key][this.chart_data[key].length - 1].changed_value
          )
        );
      }
    },
    FilterByDateRange(dates) {
      this.date_range = dates;
      this.xaxis.categories = [];
      this.series[0].data = [];
      this.fetchGraphInfo();
      this.fetchHistory();
    },
  },
};
</script>

<style>
</style>