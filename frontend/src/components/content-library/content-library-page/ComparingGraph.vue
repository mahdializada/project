<template>
  <v-card
    style="width: 1250px; height: 750px; overflow: auto"
    class="d-flex card-background overflow-hidden"
  >
    <div style="width: 80px; height: 100%" class="white text-center pt-3">
      <v-btn icon plain @click="closeDialog()">
        <v-icon size="32">mdi-close-circle-outline</v-icon>
      </v-btn>
    </div>
    <div class="w-full card-background ps-1">
      <div class="white mb-2 header">
        <div class="d-flex justify-space-between">
          <div class="d-flex align-center pa-2">
            <v-avatar size="40" color="primary">
              <v-icon color="white">mdi-video</v-icon>
            </v-avatar>
            <div class="ps-2">
              <p class="mb-0 font-weight-medium">Media Name here</p>
              <p class="mb-0 grey--text">description</p>
            </div>
          </div>
          <div class="d-flex align-center">
            <FilterDateRange
              class="me-1"
              ref="filterDateRange"
              :dateRangeProp="date_range"
              :hide-title="true"
              @dateChanged="Filter($event)"
              @filterType="typeChange($event)"
              :custom_design="true"
              :nudge_left="200"
            />
            <img src="/content-library/icons/arab_img.svg" alt="not found" />
          </div>
        </div>
      </div>
      <!-- content -->
      <div class="mx-3 mt-7">
        <div class="d-flex align-center ps-0">
          <div
            :class="`d-flex align-center pa-1 ps-2 me-1 rounded tab-item cursor-pointer ${
              index == active_tab ? 'active ' : ''
            }`"
            v-for="(tab, index) in tabs"
            :key="index"
            @click="tabChange(index)"
          >
            <v-avatar
              size="30"
              :color="index == active_tab ? 'primary ' : 'grey lighten-3'"
            >
              <span
                v-html="tab.icon"
                class="d-flex align-center"
                :style="`color: ${
                  index != active_tab
                    ? 'grey !important'
                    : 'var(--v-surface-base) !important'
                }`"
              ></span>
            </v-avatar>
            <v-badge
              :value="getSelectedSeries(index).length"
              :color="index == active_tab ? 'primary' : 'grey'"
              dot
            >
              <p
                :class="`mb-0 ps-1 font-weight-medium  ${
                  index == active_tab ? 'primary--text ' : 'grey--text'
                }`"
              >
                {{ $tr(tab.name) }}
              </p>
            </v-badge>
          </div>
        </div>
        <div class="w-full white" style="height: 500px">
          <div class="d-flex">
            <SerriesCard2
              :selected_tab.sync="tabs[active_tab]"
              :serries.sync="tab_series"
              ref="serriesRef"
              :style="` ${'min-width: 22%'}`"
              @toggleChartLine="toggleChartLine"
              :loading.sync="loading_series"
            />
            <div class="pa-2 pb-1 px-1" :style="` ${'min-width: 78%'}`">
              <v-card min-height="400" elevation="0" class="white pt-1">
                <div class="d-flex align-start justify-space-between">
                  <div class="ps-2 pa-1">
                    <v-chip
                      v-for="(item, index) in getSelectedSeries()"
                      :key="index"
                      class="me-1"
                      :style="` height: 25px ;background-color: ${
                        getColor(index).background
                      }; color: ${getColor(index).color}`"
                    >
                      <svg
                        class="me-1"
                        xmlns="http://www.w3.org/2000/svg"
                        width="22.326"
                        height="10.157"
                        viewBox="0 0 22.326 10.157"
                      >
                        <path
                          id="graph-label-icon"
                          d="M-4481.357-146.035h-4.93a1.25,1.25,0,0,1-1.25-1.25,1.25,1.25,0,0,1,1.25-1.25h4.883a5.08,5.08,0,0,1,4.946-3.922,5.083,5.083,0,0,1,4.949,3.922h5.048a1.251,1.251,0,0,1,1.25,1.25,1.251,1.251,0,0,1-1.25,1.25h-5.1a5.084,5.084,0,0,1-4.9,3.736A5.082,5.082,0,0,1-4481.357-146.035Z"
                          transform="translate(4487.538 152.457)"
                          fill="currentColor"
                        />
                      </svg>
                      {{ item.name }}
                      <v-icon
                        :color="getColor(index).color"
                        @click="unselectSeries(item)"
                        small
                        class="ms-1 cursor-pointer"
                        style="line-height: 10px; padding-top: 2px"
                        >mdi-close
                      </v-icon>
                    </v-chip>
                  </div>
                  <div>
                    <span class="d-flex align-center grey--text">
                      <svg
                        class="me-1"
                        xmlns="http://www.w3.org/2000/svg"
                        width="38.999"
                        height="12.002"
                        viewBox="0 0 38.999 12.002"
                      >
                        <path
                          id="line"
                          d="M-14645.5-12093.82h-14.5v-1.5h14.646a6.007,6.007,0,0,1,5.854-4.681,6,6,0,0,1,5.854,4.681H-14621v1.5h-12.5a6,6,0,0,1-6,5.82A6,6,0,0,1-14645.5-12093.82Zm6,4.819a5.006,5.006,0,0,0,5-4.819h-9.993A5,5,0,0,0-14639.5-12089Zm4.822-6.32a5.01,5.01,0,0,0-4.822-3.68,5.01,5.01,0,0,0-4.822,3.68Z"
                          transform="translate(14660.001 12100.002)"
                          fill="#707070"
                          opacity="0.8"
                        />
                      </svg>
                      {{ $tr("view_quality") }}
                    </span>
                    <span class="d-flex align-center grey--text">
                      <svg
                        class="me-1"
                        xmlns="http://www.w3.org/2000/svg"
                        width="42.001"
                        height="12.002"
                        viewBox="0 0 42.001 12.002"
                        style="margin-top: 1px"
                      >
                        <path
                          id="dash"
                          d="M-14645.493-12093.681H-14648v-1.5h2.614a6.007,6.007,0,0,1,5.885-4.824,6.005,6.005,0,0,1,5.886,4.824h3.614v1.5h-3.508a6,6,0,0,1-5.992,5.681A6,6,0,0,1-14645.493-12093.681Zm5.992,4.68a5.006,5.006,0,0,0,4.99-4.68H-14636v-1.5h1.36a5.01,5.01,0,0,0-4.859-3.823,5.011,5.011,0,0,0-4.859,3.823h2.361v1.5h-2.491A5.006,5.006,0,0,0-14639.5-12089Zm15.5-4.68v-1.5h6v1.5Zm-36,0v-1.5h6v1.5Z"
                          transform="translate(14660.001 12100.002)"
                          fill="#707070"
                        />
                      </svg>
                      {{ $tr("ad_attraction") }}
                    </span>
                    <span class="d-flex align-center grey--text">
                      <svg
                        class="me-1"
                        xmlns="http://www.w3.org/2000/svg"
                        width="38.999"
                        height="12.002"
                        viewBox="0 0 38.999 12.002"
                      >
                        <path
                          id="dotted"
                          d="M-14645.493-12093.681H-14648v-1.5h2.614a6.007,6.007,0,0,1,5.885-4.824,6.005,6.005,0,0,1,5.886,4.824h.615v1.5h-.509a6,6,0,0,1-5.992,5.681A6,6,0,0,1-14645.493-12093.681Zm.992-.32a5,5,0,0,0,5,5,5.006,5.006,0,0,0,4.99-4.68H-14636v-1.5h1.36a5.01,5.01,0,0,0-4.859-3.823A5.007,5.007,0,0,0-14644.5-12094Zm20.5.32v-1.5h3v1.5Zm-6,0v-1.5h3v1.5Zm-12,0v-1.5h3v1.5Zm-12,0v-1.5h3v1.5Zm-6,0v-1.5h3v1.5Z"
                          transform="translate(14660.001 12100.002)"
                          fill="#707070"
                        />
                      </svg>
                      {{ $tr("buyer") }}
                    </span>
                  </div>
                </div>

                <div id="legend-container" class="position-relative pa-2 pt-0">
                  <div v-show="isFetchingGraphData" style="height: 360px">
                    <div v-for="i in 6" :key="i" class="d-flex w-full">
                      <v-skeleton-loader
                        type="list-item"
                        class="rounded-0 w-full"
                      />
                      <v-skeleton-loader
                        type="list-item"
                        class="rounded-0 w-full"
                      />
                      <v-skeleton-loader
                        type="list-item"
                        class="rounded-0 w-full"
                      />
                      <v-skeleton-loader
                        type="list-item"
                        class="rounded-0 w-full"
                      />
                      <v-skeleton-loader
                        type="list-item"
                        class="rounded-0 w-full"
                      />
                    </div>
                  </div>
                  <div
                    style="width: 800px"
                    v-if="!isFetchingGraphData"
                    class="
                      text-center
                      d-flex
                      flex-column
                      justify-center
                      align-center
                    "
                  >
                    <line-chart
                      v-if="getSelectedSeries().length > 0"
                      :height="360"
                      class="w-full"
                      :chart-options="chartOptions"
                      :chart-data.sync="chartData"
                      :plugins="plugins"
                    />
                    <div class="d-flex flex-column justify-center align-center">
                      <select-series-svg-icon
                        v-if="getSelectedSeries().length == 0"
                      />
                      <p
                        v-if="getSelectedSeries().length == 0"
                        class="pt-2 text-h6 grey--text"
                        style="max-width: 300px"
                      >
                        Select Series from left panel to compare data!
                      </p>
                    </div>
                  </div>
                </div>
              </v-card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-card>
</template>


<!-- {
  labels: [1, 2, 3, 2],
  datasets: [
    {
      data: [11, 21, 31, 24],
      label: 'data',
      elements: { line: { borderDash: [12, 9] } },
    },
    {
      data: [1, 2, 3, 54],
      label: 'data',
      elements: { line: { borderDash: [] } },
    },
    defaultDataSet,
  ],
} -->
<script>
import moment from "moment";
import SerriesCard2 from "../../advertisement/graph/SerriesCard2.vue";
import htmlLegendPlugin from "../../../plugins/chartjs/htmlLegend";
import SelectSeriesSvgIcon from "./SelectSeriesSvgIcon.vue";
import tableIcons from "~/configs/menus/advertisementsTableIcons";
import FilterDateRange from "../../advertisement/FilterDateRange.vue";

export default {
  props: {
    contentData: { type: Object, require: true },
  },
  components: { SerriesCard2, SelectSeriesSvgIcon, FilterDateRange },
  data() {
    return {
      init_chart: false,
      line_types: {
        ad_attraction_percentage: [12, 9],
        buyer_percentage: [4, 2],
        view_quality_percantage: [],
      },

      colors: [
        {
          color: "rgba(3 ,155, 229,1)",
          background: "rgba(3,155,229,0.1)",
          index: 0,
        },
        {
          color: "rgba(255 ,111, 0,1)",
          background: "rgba(255,111,0,0.1)",
          index: 1,
        },
      ],
      active_tab: 0,
      tabs: [
        { name: "platform", id: "id", icon: tableIcons.platform },
        { name: "campaign", id: "campaign_id", icon: tableIcons.compaign },
        { name: "ad_set", id: "adset_id", icon: tableIcons.ad_set },
        { name: "ad", id: "ad_id", icon: tableIcons.ad },
      ],
      date_range: {
        start_date: "2021-11-11",
        end_date: moment().format("YYYY-MM-DD"),
      },
      tab_series: [],
      serries_setting2: [],
      filter_type: "today",
      loading_graph_api: false,
      axiosSource: null,
      defaultDataSet: {
        label: null,
        data: [],
        backgroundColor: "rgba(3,155,229,0.1)",
        borderColor: "rgba(3 ,155, 229,1)",
        pointStyle: "circle",
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 2,
        pointBorderWidth: 1,
        elements: { line: { borderDash: [1, 7] } },
        color: 0,
        index: 0,
      },
      chartData: {
        labels: [],
        datasets: [],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: "index",
        },
        plugins: {
          htmlLegend: {
            // ID of the container to put the legend in
            containerID: "legend-container",
          },
          legend: {
            display: false,
          },
        },
      },
      selected_setting: [],
      plugins: [htmlLegendPlugin],
      isFetchingGraphData: false,
      loading_series: false,
    };
  },
  computed: {},
  created() {
    this.fetchCanvaRelations(true);
  },
  methods: {
    reset() {
      try {
        console.log("reset called");
        this.$refs.serriesRef.selected_series = [];
        this.chartData.datasets = [];
        this.chartData.labels = [];
        this.$refs.filterDateRange.onDateSelected(5);
      } catch (error) {}
    },
    closeDialog() {
      this.$emit("close");
    },
    getSelectedSeries(index = this.active_tab) {
      try {
        const series = this.$refs.serriesRef.selected_series;
        return series.filter((row) => this.tabs[index].id in row);
      } catch (error) {
        return [];
      }
    },
    tabChange(index = 0) {
      console.log("tabl changes");
      this.chartData.datasets = [];
      this.chartData.labels = [];
      this.active_tab = index;
      this.fetchCanvaRelations();
    },
    unselectSeries(item) {
      this.$refs.serriesRef.toggleColumn(item);
    },

    async fetchGraphData(id) {
      this.isFetchingGraphData = true;
      try {
        let url = "advertisement/canva-statistics";
        const body = {
          ...this.date_range,
          model_name: this.tabs[this.active_tab].name,
          media_link: this.contentData.project_url,
          request_id: id,
          type: this.filter_type,
          graph_base: "monthly",
        };
        const { data } = await this.$axios.post(url, body);
        for (let index = 0; index < data.length; index++) {
          const temp_data = data[index];
          let dataset = JSON.parse(JSON.stringify(this.defaultDataSet));
          dataset.label = this.$tr(temp_data.column);
          dataset.data = temp_data.data;
          dataset.backgroundColor = this.getColor().background;
          dataset.borderColor = this.getColor().color;
          dataset.index = this.getColor().index;
          dataset.id = id;
          dataset.elements.line.borderDash = this.line_types[temp_data.column];
          this.chartData.labels = temp_data.label;
          this.chartData.datasets.push(dataset);
        }
        // this.loading_graph_api = false;
      } catch (error) {
        console.error("error", error);
      }
      this.isFetchingGraphData = false;
    },

    async Filter(date) {
      this.date_range = date;
      const series = this.getSelectedSeries();
      this.chartData.datasets = [];
      this.chartData.labels = [];
      series.forEach(async (row) => {
        await this.fetchGraphData(row[this.tabs[this.active_tab].id]);
      });
      // let labels = this.$refs.serriesRef.getSelectedLabels();
      // let url = "advertisement/graph-profile-statistics";
      // const body = {
      //   ...this.date_range,
      //   ad_ids: ad_ids,
      //   column_name: labels,
      //   request_id: this.request_id,
      //   model_name: this.model_name,
      //   type: this.filter_type,
      // };
      // this.loading_graph_api = true;
      // const { data } = await this.$axios.post(url, body);
      // console.log("data", data);
      // for (let index = 0; index < data.length; index++) {
      //   const temp_data = data[index];
      //   console.log(" tempdata", this.chartData.datasets[index].data);
      //   this.chartData.datasets[index].data = temp_data.data;
      //   this.chartData.labels = temp_data.label;
      // }
      // this.loading_graph_api = false;
    },
    typeChange(type) {
      console.log("type changes", type);
      this.filter_type = type;
    },

    async fetchCanvaRelations(first = false) {
      console.log("contentData", this.contentData);
      this.loading_series = true;
      const series = this.getSelectedSeries();
      series.forEach(async (row) => {
        await this.fetchGraphData(row[this.tabs[this.active_tab].id]);
      });
      try {
        let url = "advertisement/canva-statistics";
        const body = {
          ...this.date_range,
          media_link: this.contentData.project_url,
          model_name: this.tabs[this.active_tab].name,
          filters: first ? [] : this.$refs.serriesRef.selected_series,
        };
        const { data } = await this.$axios.post(url, body);
        this.tab_series = data;
      } catch (error) {
        console.error(error);
      }
      this.loading_series = false;
    },
    async toggleChartLine(data) {
      if (data.type == "pop") {
        this.chartData.datasets = this.chartData.datasets.filter(
          (row) => row.id != data.item[this.tabs[this.active_tab].id]
        );
        this.chartData.datasets = [...this.chartData.datasets];
        console.log("pop", this.chartData.datasets);
      } else {
        await this.fetchGraphData(data.item[this.tabs[this.active_tab].id]);
      }
    },
    getColor(index = null) {
      try {
        console.log(this.chartData.datasets[0].index);
        if (index == 0) {
          return this.colors[this.chartData.datasets[0].index];
        }
        if (index == 1) {
          if (this.chartData.datasets[0].index == 0) {
            return this.colors[1];
          }
          return this.colors[0];
        }
      } catch (error) {
        return this.colors[0];
      }
      if (this.chartData.datasets.length == 0) {
        return this.colors[0];
      }
      if (
        this.chartData.datasets.length >= 0 &&
        this.chartData.datasets.length < 3
      ) {
        return this.colors[0];
      } else {
        if (this.chartData.datasets[0].index == 0) {
          return this.colors[1];
        } else {
          return this.colors[0];
        }
      }
    },
  },
};
</script>

<style scoped>
.card-background {
  background: #f7f8fc;
}
.tab-item {
  border: 1px solid #e2e3e5;
  min-width: 180px;
  background: #fafbfd;
  margin-bottom: 8px;
}
.tab-item.active {
  background: white;
  border-bottom-left-radius: unset !important;
  border-bottom: unset;
  border-bottom-right-radius: unset !important;
  margin-bottom: unset !important;
  padding-bottom: 18px !important;
}
</style>