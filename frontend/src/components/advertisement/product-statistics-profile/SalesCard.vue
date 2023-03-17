<template>
  <div>
    <p class="custom-card-title-1 mb-1">Profit %</p>
    <v-card
      class="d-flex flex-grow-1 primary darken-4"
      style="min-height: 300px"
      elevation="0"
      dark
    >
      <!-- loading spinner -->
      <div
        v-if="loading"
        class="d-flex flex-grow-1 align-center justify-center"
      >
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>

      <!-- information -->
      <div v-if="!loading" class="d-flex flex-column flex-grow-1">
        <v-card-title class="pa-2 pb-1 pe-0 text-h5">
          <div>Profit</div>
        </v-card-title>

        <div class="d-flex flex-column flex-grow-1 pb-2">
          <div class="px-2 d-flex align-center justify-space-between">
            <div class="text-h5">{{ getProfit }} %</div>
            <div class="d-flex flex-column text-right">
              <div class="font-weight-bold">
                <trend-percent :value="percentage" />
              </div>
              <div class="caption">vs last weak</div>
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
          height="160"
          :options.sync="chartOptions"
          :series.sync="series"
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
    // TrendPercent
  },
  props: {
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
          data: [11],
        },
      ],
    },
    xaxis: {
      type: Object,
      default: () => ({
        type: "datetime",
        categories: [],
      }),
    },
    label: {
      type: String,
      default: "dashboard.sales",
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
  },
  computed: {
    getProfit() {
      if (this.series[0].data.length > 0) {
        let total = this.series[0].data.reduce((a, b) => a + b, 0);
        total = total / this.series[0].data.length;
        return total.toFixed(2);
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

            return `<div class="rounded-lg pa-1 caption">
              <div class="font-weight-bold">${this.getLabels(
                dataPointIndex
              )}</div>
              <div>${series[seriesIndex][dataPointIndex]} ${this.$tr(
              seriesName
            )}</div>
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