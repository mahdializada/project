<template>
  <v-row class="mx-0">
    <v-col cols="12" md="4" class="px-2 py-0">
      <ads-serrries />
    </v-col>
    <v-col cols="12" md="8" class="pa-0">
      <v-card elevation="0" min-height="475" class="white pt-1">
        <div class="d-flex align-start justify-space-between">
          <div
            id="graph-table-scroll"
            class="ma-1 pa-1"
            style="max-height: 80px; overflow-y: auto"
          >
            <v-chip
              v-for="(item, index) in chartData.datasets"
              :key="index"
              class="me-1 mb-1"
              :style="` height: 20px ;background-color: ${item.backgroundColor.replace(
                '1)',
                '0.08)'
              )}; color: ${item.borderColor}`"
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
              <span style="font-size: 10px">
                {{ item.label }}
              </span>
              <v-icon
                :color="item.borderColor"
                @click="
                  toggleSerries({ label: item.label, action: 'manual-pop' })
                "
                small
                class="ms-1 cursor-pointer"
                style="line-height: 10px; padding-top: 2px"
                >mdi-close
              </v-icon>
            </v-chip>
          </div>
          <div class="ps-1 pe-2">
            <v-select
              on
              v-model="selected_setting"
              style="width: 165px"
              @change="applySavedFilter(request_id, $event)"
              rounded
              dense
              placeholder="saved views"
              :items="serries_setting2"
              hide-details
              item-text="name"
              background-color=""
              class="px-0"
              item-value="columns"
              outlined
            >
              <template v-slot:prepend-item>
                <p
                  v-if="3 > 1"
                  class="mb-1 py-1 ps-3"
                  style="font-size: 16px; font-weight: 500"
                >
                  {{ $tr("shared_views") }}
                </p>
              </template>

              <template v-slot:[`item`]="{ item, on, attrs }" class="mt-1">
                <v-list-item
                  v-on="on"
                  v-bind="attrs"
                  class="pe-1"
                  style="min-width: 250px"
                >
                  <span class="d-flex align-center">
                    <v-icon size="20" class="me-1" v-if="item.scope_type"
                      >mdi-earth</v-icon
                    >
                    <v-icon size="20" class="me-1" v-else>mdi-account</v-icon>
                    <span>
                      {{ item.name }}
                      <p class="mb-0 text-caption">
                        {{
                          $auth.user.id == item.user_id
                            ? $tr("you")
                            : item.user.firstname + " " + item.user.lastname
                        }}
                      </p>
                    </span>
                  </span>
                  <v-spacer></v-spacer>
                  <span class="d-flex align-center">
                    <v-icon
                      size="20"
                      :disabled="$auth.user.id != item.user_id"
                      class="mx-1"
                      style="margin-bottom: 3px"
                      @click.stop="deleteView(item.id)"
                    >
                      mdi-delete</v-icon
                    >
                    <!-- v-model="item.default" -->
                    <v-switch
                      dense
                      :input-value="item.default"
                      @click.stop="() => {}"
                      :disabled="$auth.user.id != item.user_id"
                      @change="defaultView(item, $event)"
                      hide-details
                      color="primary"
                      class="ms-auto mt-0 pt-0"
                      :false-value="0"
                      :true-value="1"
                    >
                    </v-switch>
                  </span>
                </v-list-item>
              </template>
            </v-select>
          </div>
        </div>

        <div id="legend-container" class="position-relative pa-2 pt-1">
          <div v-show="loading_graph_api" style="height: 350px">
            <div v-for="i in 5" :key="i" class="d-flex w-full">
              <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
              <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
              <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
              <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
              <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
            </div>
          </div>
          <div v-show="!loading_graph_api">
            <line-chart
              :height="350"
              :chart-options="chartOptions"
              :chart-data="chartData"
              :plugins="plugins"
            />
          </div>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import htmlLegendPlugin from "../../../plugins/chartjs/htmlLegend";
import AdsSerrries from "./AdsSerrries.vue";
export default {
  props: {
    date_range: {
      type: Object,
      require: true,
    },
    selected_ads: {
      type: Object,
      require: true,
    },
    serries_setting: {
      type: Array,
      require: true,
    },
  },

  data() {
    return {
      request_id: "",
      serries_setting2: [],
      filter_type: "today",
      loading_graph_api: false,
      axiosSource: null,
      defaultDataSet: {
        label: null,
        data: [],
        backgroundColor: "rgb(127, 17, 224)",
        borderColor: "rgb(127, 17, 224)",
        pointStyle: "circle",
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 2,
        pointBorderWidth: 1,
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
      temp_serries: [],
    };
  },
  watch: {
    serries_setting(value) {
      this.serries_setting2 = value;
    },
  },
  methods: {
    toggleSerries(params) {
      const label = params.label;
      const action = params.action;
      if (action == "push") {
        this.fetchGraphData(label);
      } else {
        if (action == "manual-pop") {
          this.chartData.datasets = [
            ...this.chartData.datasets.filter((row) => row.label != label),
          ];
          this.$root.$emit("unselectSerries", label);
        } else {
          this.chartData.datasets = [
            ...this.chartData.datasets.filter(
              (row) => row.label != this.$tr(label.label)
            ),
          ];
        }
      }
    },

    async fetchGraphData(label) {
      try {
        this.loading_graph_api = true;

        let url = "advertisement/graph-profile-statistics";
        const body = {
          ...this.date_range,
          column_name: label.label,
          request_id: this.request_id,
          type: this.filter_type,
        };
        const { data } = await this.$axios.post(url, body);
        let dataset = JSON.parse(JSON.stringify(this.defaultDataSet));
        dataset.label = this.$tr(label.label);
        dataset.data = data.data;
        dataset.backgroundColor = label.color;
        dataset.borderColor = label.color;
        this.chartData.labels = data.label;
        this.chartData.datasets.push(dataset);
      } catch (error) {
        console.log("error", error);
      }
      this.loading_graph_api = false;
    },

    async Filter(date, ad_ids = []) {
      let labels = this.$refs.serriesRef.getSelectedLabels();
      let url = "advertisement/graph-profile-statistics";
      const body = {
        ...this.date_range,
        ad_ids: ad_ids,
        column_name: labels,
        request_id: this.request_id,
        type: this.filter_type,
      };
      this.loading_graph_api = true;
      const { data } = await this.$axios.post(url, body);
      for (let index = 0; index < data.length; index++) {
        const temp_data = data[index];
        console.log(" tempdata", this.chartData.datasets[index].data);
        this.chartData.datasets[index].data = temp_data.data;
        this.chartData.labels = temp_data.label;
      }
      this.loading_graph_api = false;
    },
    addType(type) {
      this.filter_type = type;
    },
    async applySavedFilter(id, labels) {
      console.log("seeeeeeeee", labels);
      // let serries = this.$refs.serriesRef.assignSerries(labels);
      this.$root.$emit("assignSerries", labels);

      const body = {
        ...this.date_range,
        column_name: labels,
        request_id: id,
        type: this.filter_type,
      };
      let url = "advertisement/graph-profile-statistics";
      this.loading_graph_api = true;
      const { data } = await this.$axios.post(url, body);
      this.chartData.datasets = [];
      for (let index = 0; index < data.length; index++) {
        const temp_data = data[index];
        let dataset = JSON.parse(JSON.stringify(this.defaultDataSet));
        dataset.label = this.$tr(this.temp_serries[index].label);
        dataset.data = temp_data.data;
        dataset.backgroundColor = this.temp_serries[index].color;
        dataset.borderColor = this.temp_serries[index].color;
        this.chartData.datasets.push(dataset);
        this.chartData.labels = temp_data.label;
      }
      this.loading_graph_api = false;
    },
    async deleteView(id) {
      try {
        this.$TalkhAlertNA(
          "Are you sure?",
          "delete",
          async () => {
            this.confirmDelete(id);
          },
          "delete"
        );
      } catch (error) {
        console.log("error", error);
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    async confirmDelete(id) {
      const response = await this.$axios.delete("view_name/" + id);
      if (response.status == 200) {
        this.serries_setting2 = this.serries_setting2.filter(
          (row) => row.id != id
        );
        this.serries_setting2 = [...this.serries_setting2];
        this.$toasterNA("green", this.$tr("delete_successfully"));
      } else {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

    onSubmit(data) {
      this.serries_setting2.push(data);

      this.serries_setting2 = [...this.serries_setting2];
    },
    async defaultView(item, event) {
      // let temp_view = JSON.parse(JSON.stringify(this.all_views));
      item.default = event;
      try {
        const response = await this.$axios.post("view-name-edit", item);
        if (response.status == 200) {
          this.$toasterNA("green", this.$tr("changed to default!"));
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    assignedResult(data) {
      console.log("data result from serries", data);
      this.temp_serries = data;
    },
  },
  mounted() {
    console.log("mounted called");
    this.applySavedFilter(this.selected_ads.ad_id, this.serries_setting);
    //   this.generateId,
    this.$root.$on("toggleSerries", this.toggleSerries);
    this.$root.$on("assignedResult", this.assignedResult);
    // this.$root.$on("applySavedFilter", this.emitApplySavedFilter);
  },
  created() {
    console.log("created called");
  },
  components: { AdsSerrries },
};
</script>
<style>
.graph__loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
}

.theme--dark .adv-tabs {
  background: #242424;
}

.adv-tabs .v-tabs-bar {
  background: transparent !important;
}

.adv-tabs .v-tab {
  background: var(--v-surface-base);
  border-radius: 6px;
  margin: 0 4px;
  width: 300px;
  overflow: hidden;
}

.adv-tabs .v-tab.v-tab--active {
  background: var(--v-background-base);
}

.adv-tabs .v-tabs-slider-wrapper {
  display: none;
}

.text-start {
  text-align: left;
}

.v-application--is-rtl .text-start {
  text-align: right;
}

.adv-tabs .tab-content {
  text-transform: none !important;
  cursor: unset;
}

.adv-tabs .tab-adv-sub-title {
  font-size: 16px;
  font-weight: 100;
  color: #777;
}

.theme--dark .adv-tabs .tab-adv-sub-title {
  color: #ccc;
}

.adv-tabs .tab-adv-title {
  font-weight: bold;
  color: var(--v-friqiBase-base);
}

.adv-tabs .v-slide-group__prev,
.adv-tabs .v-slide-group__next {
  background: var(--v-background-base);
  min-width: 26px;
  max-width: 26px;
}

.adv-tabs .v-slide-group__prev {
  margin-right: 4px;
}

.adv-tabs .v-slide-group__next {
  margin-left: 4px;
}

.adv-tabs .chart-btn {
  min-width: 52px;
  background: var(--v-background-base);
  border-radius: 6px;
  transform: translateX(-100%);
  cursor: pointer;
  transition: all 0.3s;
}

.adv-tabs .chart-btn.rotate .v-icon {
  transform: rotate(180deg);
}

.adv-graph-legend-icon {
  position: relative;
}

.adv-graph-legend-icon::after {
  height: 1px;
  width: 40px;
  background: currentColor;
  display: block;
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.slide-horizontal-enter-active,
.slide-horizontal-leave-active {
  transition: all 0.6s;
}

.slide-horizontal-leave,
.slide-horizontal-enter-to {
  max-height: 600px;
  overflow: hidden;
}

.slide-horizontal-leave {
  opacity: 0;
}

.slide-horizontal-enter,
.slide-horizontal-leave-to {
  overflow: hidden;
  max-height: 0;
}

.slide-horizontal-leave-to {
  opacity: 0;
}

.adv-graph-switch {
  position: absolute;
  top: -24px;
  right: 0px;
}
</style>
<style>
#graph-table-scroll::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

#graph-table-scroll::-webkit-scrollbar {
  width: 8px !important;
  background-color: var(--v-surface-base) !important;
}

#graph-table-scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}

#graph-table-scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

#graph-table-scroll::-webkit-scrollbar-thumb {
  border-radius: 8px !important;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2) !important;
}
</style>
