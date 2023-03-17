<template>
  <div>
    <div
      v-if="loading"
      class="d-flex flex-grow-1 align-center justify-center"
      style="height: 400px"
    >
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <v-card
      v-else
      class="
        d-flex
        flex-grow-1
        white
        darken-4
        rounded
        overflow-hidden
        black--text
      "
      style="height: 380px"
      elevation="0"
      dark
    >
      <!-- information -->
      <div class="d-flex flex-column justify-space-between flex-grow-1">
        <v-card-title class="pa-2 pb-1 pe-0 text-h5 grey--text">
          <div>{{ $tr(column) }}</div>
        </v-card-title>

        <div class="d-flex flex-column flex-grow-1 pb-2">
          <div class="px-2 d-flex align-center justify-space-between">
            <div class="text-h5 grey--text">
              {{ getProfit }} {{ column == "prod_profit" ? "%" : "" }}
            </div>
            <div class="d-flex flex-column text-right">
              <div class="font-weight-bold primary--text">
                <trend-percent :value="percentage" />
              </div>
              <div class="caption grey--text">vs last weak</div>
            </div>
          </div>

          <div class="px-2 pb-2">
            <div class="d-flex align-center">
              <v-spacer></v-spacer>
            </div>
          </div>
        </div>
        <apexchart
          type="area"
          height="120"
          :options="chartOptions"
          :series="series"
        ></apexchart>
      </div>
    </v-card>
  </div>
</template>
   
   <script>
import TrendPercent from "./TrendPercent.vue";
import moment from "moment";
// import TrendPercent from '../common/TrendPercent'

function formatDate(date) {
  // return date;
  return date ? moment(date).format("D MMM") : "";
}

/*
   |---------------------------------------------------------------------
   | DEMO Dashboard Card Component
   |---------------------------------------------------------------------
   |
   | Demo card component to be used to gather some ideas on how to build
   | your own dashboard component
   |
   */
export default {
  components: {
    TrendPercent,
  },
  props: {
    column: String,
    loading: Boolean,
    value: {
      type: Number,
      default: 0,
    },
    percentage: {
      type: Number,
      default: 0,
    },
    percentageLabel: {
      type: String,
      default: "vs. last week",
    },
    series: {
      type: Array,
      default: () => [
        {
          name: "Sales",
          data: [],
        },
      ],
    },
    xaxis: {
      type: Object,
      default: () => ({
        categories: ["2018-09-19T00:00:00.000Z", "2018-09-20T00:00:00.000Z"],
      }),
    },
    label: {
      type: String,
      default: "dashboard.sales",
      format: "dd/MM",
    },
    actionLabel: {
      type: String,
      default: "View Report",
    },
    options: {
      type: Object,
      default: () => ({}),
    },
    loading: {
      type: Boolean,
      default: false,
    },
    profile_data: {
      type: Object,
      default: () => ({}),
    },
    users: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    getProfit() {
      if (this.series[0].data.length > 0 && this.column == "prod_profit") {
        let total = this.series[0].data.reduce((a, b) => a + b, 0);
        total = total / this.series[0].data.length;
        return total.toFixed(2);
      }
      if (this.series[0].data.length > 0) {
        return this.profile_data[this.column];
      }
    },
    chartOptions() {
      const primaryColor = this.$vuetify.theme.isDark
        ? this.$vuetify.theme.themes.dark.primary
        : this.$vuetify.theme.themes.light.primary;

      return {
        chart: {
          height: 140,
          type: "area",
          sparkline: {
            enabled: true,
          },
          animations: {
            speed: 400,
          },
        },
        colors: [primaryColor],
        fill: {
          type: "solid",
          colors: [primaryColor],
          opacity: 0.15,
        },
        stroke: {
          curve: "smooth",
          width: 2,
        },
        xaxis: this.xaxis,
        tooltip: {
          followCursor: true,
          theme: "dark",
          custom: ({ ctx, series, seriesIndex, dataPointIndex, w }) => {
            const seriesName = w.config.series[seriesIndex].name;
            // const user = w.config.series[seriesIndex].user;

            return `<div class="rounded-lg pa-1 caption">
                 <div class="font-weight-bold">
                  ${this.getLabels(dataPointIndex)}
            </div>
                 <div> ${this.getUsers(dataPointIndex)}</div>
               </div>`;
          },
        },
        ...this.options,
      };
    },
  },
  methods: {
    getLabels(index) {
      return formatDate(this.xaxis.categories[index]);
    },
    getUsers(key) {
      let data = "";
      try {
        console.log("users", this.users);
        let user = this.users[this.xaxis.categories[key]];
        for (let index = 0; index < user.length; index++) {
          data =
            `<div class="d-flex align-center justify-space-between" > <span style="min-width:60px"> ${user[index].value}</span> <span> ${user[index].firstname} </span> </div>` +
            " " +
            data;
        }
        return data;
      } catch (error) {
        console.log("error", error);
        return "user";
      }
    },
  },
};
</script>
   
   <style scoped>
.custom-card-title-1 {
  font-size: 17px;
  text-transform: uppercase;
  font-weight: 500;
  color: #777;
}
</style>