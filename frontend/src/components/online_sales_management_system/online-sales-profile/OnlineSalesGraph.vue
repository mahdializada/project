<template>
  <div>
    <div class="d-flex align-start">
      <div class="me-2" style="width: 74%">
        <div
          class="white ps-4 d-flex py-2 align-center justify-space-between"
          style="border-radius: 30px 0 0 0; width: 100%"
        >
          <div class="d-flex align-center">
            <v-btn
              fab
              x-small
              class="me-1 primary"
              @click="$emit('closeDialog')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-btn fab x-small class="primary" @click="$emit('fullScreen')">
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
                    <v-avatar size="30" class="text-h6 primary white--text">
                      {{
                        current_tab != "item_code"
                          ? getHeaderTitle[0]
                          : selected_product?.product_code[0]
                      }}
                    </v-avatar>
                  </v-avatar>
                </v-avatar>
              </v-avatar>
              <span class="dialog-title d-inline-block me-2">
                {{ getHeaderTitle }}</span
              >
              <span v-if="current_tab == 'item_code'"
                >({{ selected_product.product_name }})</span
              >
            </span>
          </div>
          <div class="d-flex align-center pe-3">
            <FilterDateRange
              ref="filterDateRange"
              :customSelectDate="{ type: 'lifetime', index: 5 }"
              :dateRangeProp="date_range"
              :hide-title="true"
              @dateChanged="FilterByDateRange($event)"
              :custom_design="true"
              :nudge_left="300"
            />
            <!-- <AdProfileSvg class="ms-2" /> -->
          </div>
        </div>
        <div class="ps-2 pt-2">
          <v-card
            elevation="0"
            class="rounded-sm"
            v-if="current_tab != 'item_code'"
          >
            <p class="pa-1 ps-3 text-h5 mb-0">
              {{ $tr("registered_products") }}
            </p>
            <div id="legend-container" class="pa-2 pt-0">
              <div v-show="extra_data_loading" style="height: 350px">
                <div v-for="i in 5" :key="i" class="d-flex w-full">
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
              <div class="" v-show="!extra_data_loading">
                <line-chart
                  style="max-height: 350px"
                  :height="350"
                  :chart-options="chartOptions"
                  :chart-data="chartData"
                  :plugins="plugins"
                />
              </div>
            </div>
          </v-card>
        </div>
      </div>
      <!-- start left side  -->
      <div
        id="graph-table-scroll"
        style="width: 28%; height: 100vh"
        class="white pt-3 px-2 overflow-y-auto"
      >
        <AdvertisementProfileDataTable
          card_title="status"
          class="mb-2"
          :headers="status_header"
          :items.sync="status_data"
          :loading_api="extra_data_loading"
        />
        <AdvertisementProfileDataTable
          card_title="label"
          class="mb-2"
          :items.sync="label_data"
          :headers="label_header"
          :loading_api="extra_data_loading"
          :model_name.sync="current_tab"
          :selected_item="[selected_product]"
        />
        <!-- remark start -->
        <div class="mb-5">
          <div
            class="custom-title mb-1 ps-2 py-auto d-flex align-center"
            style="height: 40px; background-color: #f7f8fc"
          >
            {{ $tr("remarks") }}
          </div>
          <div
            id="graph-table-scroll"
            ref="remarksContainer"
            style="
              position: relative;
              max-height: 400px;
              min-height: 300px;
              width: 100%;
              overflow: auto;
            "
          >
            <div v-if="remark_data.length > 0">
              <single-remark
                v-for="(remark, i) in remark_data"
                :key="i"
                :remark="remark"
                :small-remark="true"
                @deleteHandler="deleteHandler"
                @changeReactionCount="changeReactionCount(i, $event)"
              />
            </div>
            <div
              v-else
              class="text-center"
              style="
                position: absolute;
                top: 50%;
                transform: translate(-50%, -50%);
                left: 50%;
              "
            >
              <no-remark width="140" height="130" />
            </div>
          </div>
          <div class="mt-3">
            <v-form @submit.prevent="submitRemark">
              <v-text-field
                :class="`form-new-item remarks-input`"
                background-color="var(--new-input-bg)"
                outlined
                dense
                hide-details
                :placeholder="$tr('type_here') + '...'"
                v-model.trim="remark"
              >
                <template slot="append">
                  <PopOver
                    v-model="showSubmitErr"
                    :right="true"
                    :indicator="10"
                  >
                    <template v-slot:activator>
                      <v-btn
                        fab
                        x-small
                        text
                        color="primary"
                        class="ma-0"
                        @click="submitRemark"
                      >
                        <v-icon dark size="20"> mdi-send </v-icon>
                      </v-btn>
                    </template>
                    <template v-slot:body>
                      <div>
                        {{
                          $tr("please_wait_30_seconds_before_your_next_remark")
                        }}
                      </div>
                    </template>
                  </PopOver>
                </template>
              </v-text-field>
            </v-form>
          </div>
        </div>
        <!-- remark end -->
      </div>
    </div>
  </div>
</template>
  
  <script>
import htmlLegendPlugin from "~/plugins/chartjs/htmlLegend";

import FilterDateRange from "../../advertisement/FilterDateRange.vue";
import moment from "moment";
import AdvertisementProfileDataTable from "../../advertisement/graph/AdvertisementProfileDataTable.vue";
import SingleRemark from "../../advertisement/remarks/SingleRemark.vue";
import NoRemark from "../../advertisement/remarks/NoRemark.vue";
import PopOver from "../../design/PopOver.vue";

export default {
  components: {
    FilterDateRange,
    AdvertisementProfileDataTable,
    SingleRemark,
    NoRemark,
    PopOver,
  },
  props: {
    // selected_product: {
    //   default: () => {
    //     return { product_code: "PR40", product_name: "dffddfdfd" };
    //   },
    // },
    current_tab: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      selected_product: { product_code: "PR40", product_name: "dffddfdfd" },
      showSubmitErr: false,

      extra_data_loading: false,
      remark: "",

      date_range: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },
      status_header: [
        { text: "status", value: "status_status", with: "30px" },
        { text: "reason ", value: "status_reason" },
        { text: "date ", value: "status_date" },
        { text: "user ", value: "status_creator", align: "center" },
      ],
      label_header: [
        { text: "label", value: "label" },
        { text: "date ", value: "label_date" },
        { text: "user ", value: "label_creator", align: "center" },
      ],
      status_data: [],
      label_data: [],
      remark_data: [],

      // chart
      plugins: [htmlLegendPlugin],

      chartData: {
        labels: [1, 2, 3, 4, 4],
        datasets: [
          {
            label: null,
            data: [1, 2, 3, 4, 4],
            backgroundColor: "rgb(127, 17, 224)",
            borderColor: "rgb(127, 17, 224)",
            pointStyle: "circle",
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
            pointBorderWidth: 1,
          },
        ],
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
    };
  },
  computed: {
    generateId() {
      let target_id = "id";
      switch (this.current_tab) {
        case "country":
          target_id = "country_id";
          break;
        case "company":
          target_id = "company_id";
          break;
        case "project":
          target_id = "project_id";
          break;
        case "sales_type":
          target_id = "sales_type";
          break;
        case "item_code":
          target_id = "product_code";
          break;
        default:
          target_id = "id";
          break;
      }
      return this.selected_product[target_id];
    },
    getHeaderTitle() {
      switch (this.current_tab) {
        case "country":
          return this.selected_product?.country?.name ?? "country";
        case "company":
          return this.selected_product?.company?.name ?? "company";
        case "project":
          return this.selected_product?.project?.name ?? "project";
        case "sales_type":
          return this.selected_product?.sales_type ?? "sales_type";
        case "item_code":
          return this.selected_product?.product_name ?? "item_code";
      }
      return "product";
    },
  },

  methods: {
    FilterByDateRange(date) {
      this.date_range = date;
      this.fetchExtraInfo();
    },

    getOrderType(order) {
      return (
        order.design_request_order.order_type ??
        order.design_request_order.order_type
      );
    },

    async submitRemark() {
      let canComment = this.checkDate();
      if (canComment < 2) {
        try {
          if (this.remark != "") {
            let reqData = {
              model: this.current_tab,
              id: this.generateId,
              remark: this.remark,
              sub_system: "online_sales",
            };
            let { data } = await this.$axios.post("/remarks", reqData);
            if (data.result) {
              this.remark_data.push(data.data);
              this.remark = "";
              this.scrollDown();
            }
          }
        } catch (error) {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
          console.log("remark error", error);
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
        this.showSubmitErr = false;
      } else {
        this.showSubmitErr = true;
      }
    },

    checkDate() {
      let now = moment().format("DD/MM/YYYY HH:mm:ss");
      let comment_count = 0;
      this.remark_data.map((item) => {
        if (item.user_id == this.$auth.user.id) {
          let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(
            moment(item.created_at)
          );
          if (ms < 30000) {
            comment_count++;
          }
        }
      });
      return comment_count;
    },
    async deleteHandler(id) {
      this.remark_data = this.remark_data.filter((item) => item.id != id);
    },
    async fetchExtraInfo(item = this.selected_product) {
      this.selected_product = structuredClone(item);
      this.extra_data_loading = true;
      try {
        const payload = {
          model_name: this.current_tab,
          request_id: this.generateId,
          ...this.date_range,
        };
        const response = await this.$axios.post(
          "online-sales/profile-graph",
          payload
        );
        this.status_data = this.sortData(response.data.status);
        this.label_data = this.sortData(response.data.labels);
        this.remark_data = response.data.remarks;
        const { labels, data } = response.data.graph_data;
        this.chartData.labels = labels;
        this.chartData.datasets[0].data = data;
      } catch (error) {}
      this.extra_data_loading = false;
    },

    sortData(data) {
      let status = [];
      for (const key in data) {
        status.push({ data: data[key] });
      }
      return status;
    },

    changeReactionCount(i, data = null) {
      this.remark_data[i].reactions = this.remark_data[i].reactions.filter(
        (row) => row.user_id != this.$auth.user.id
      );
      if (data) {
        this.remark_data[i].reactions.push(data);
      }
      this.remark_data = [...this.remark_data];
    },
    scrollDown() {
      setTimeout(() => {
        this.$refs.remarksContainer.scrollTo(
          0,
          this.$refs.remarksContainer.scrollHeight
        ),
          20;
      });
    },
  },
};
</script>
  
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