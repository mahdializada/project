<template>
  <div>
    <p class="custom-card-title-1 mb-1">Profitablity</p>
    <v-card class="d-flex flex-grow-1 white darken-4 pa-0" elevation="0" dark>
      <!-- loading spinner -->
      <div
        v-if="loading"
        class="d-flex flex-grow-1 align-center justify-center"
        style="height: 300px"
      >
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>

      <!-- information -->
      <div class="d-flex flex-column flex-grow-1" v-else>
        <div class="d-flex align-center ps-2 pt-2">
          <span
            v-for="(item, index) in colors"
            :key="index"
            class="grey--text d-flex align-center pe-2"
          >
            <span
              class="d-inline-block me-1"
              style="height: 13px; width: 13px"
              :style="'background:' + item"
            >
            </span>
            <span style="font-size: 9px; line-height: 4px">{{
              $tr(index)
            }}</span>
          </span>
        </div>
        <apexchart
          type="bar"
          height="290"
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
    series: {
      type: Array,
      require: true,
    },
    xaxis: {
      type: Object,
      require: true,
    },
    profitability: Array,
    colors: Object,
    loading: Boolean,
  },

  data() {
    return {
      chartOptions: {
        bar: {
          distributed: true,
        },
        chart: {
          height: 300,
          type: "bar",
          toolbar: {
            show: false,
          },
        },
        fill: {
          type: "solid",
          colors: this.profitability,
          // colors: [
          //   function ({ value, seriesIndex, w }) {
          //     if (value < 55) {
          //       return "#7E36AF";
          //     } else {
          //       return "#D9534F";
          //     }
          //   },
          //   function ({ value, seriesIndex, w }) {
          //     if (value < 111) {
          //       return "#7E36AF";
          //     } else {
          //       return "#D9534F";
          //     }
          //   },
          // ],
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "smooth",
          opacity: 0.15,
        },
        xaxis: this.xaxis,
        // tooltip: {
        //   x: {
        //     format: "dd/MM/yy HH:mm",
        //   },
        // },
        tooltip: {
          followCursor: true,
          theme: "dark",
          custom: function ({ ctx, series, seriesIndex, dataPointIndex, w }) {
            const seriesName = w.config.series[seriesIndex].name;

            return `<div class="rounded-lg pa-1 caption">
              <div class="font-weight-bold">${formatDate(
                w.globals.labels[dataPointIndex]
              )}</div>
              <div>${series[seriesIndex][dataPointIndex]} ${seriesName}</div>
            </div>`;
          },
        },
      },
    };
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