<template>
  <!-- ddd -->
  <div>
    <div class="d-flex w-full">
      <!-- :class="`graph-container  d-flex ${!show ? 'hide' : 'show'} ${
      fullScreen ? 'full-screen' : ''
    }`" -->
      <div
        style="width: 72%; height: 100vh; border-radius: 30px 0 0 30px"
        class="pe-2"
      >
        <AdvertisementProfileTop
          @close="$emit('close')"
          @fullScreen="$emit('fullScreen')"
          :selected_item.sync="selected_item"
          :current_tab.sync="current_tab"
        >
          <template slot="date_slot">
            <FilterDateRange
              ref="filterDateRange"
              :dateRangeProp="date_range"
              :hide-title="true"
              @dateChanged="Filter($event)"
              @filterType="typeChange($event)"
              :custom_design="true"
            />
          </template>
        </AdvertisementProfileTop>
        <div
          id="graph-table-scroll"
          style="height: 90vh"
          class="mb-5 overflow-y-auto"
        >
          <TotalInfoCard
            :card_items.sync="card_items"
            class="mt-2 px-1"
            :api_loading.sync="api_loading"
          />
          <div>
            <!-- <SerriesCard /> -->
            <AdvertisementGraph2
              :request_id.sync="generateId"
              :model_name.sync="current_tab"
              ref="graphRefs"
              :date_range.sync="date_range"
              :serries_setting.sync="serries_setting"
            />
          </div>
          <div class="d-flex align-center pa-1">
            <div id="large-table" class="pa-1" style="width: 60%">
              <AdvertisementProfileDataTable
                card_title="related_ads"
                :show_pagination="true"
                :headers="ad_header"
                @onPaginate="paginate"
                :items="related_data"
                :last_page.sync="last_page"
                :loading_api="extra_data_loading"
                ref="largeTableRef"
                :showSelect="true"
                @filterByAd="filterByAd"
              />
            </div>
            <div style="width: 40%" class="pa-1 pe-0">
              <AdvertisementProfileDataTable
                ref="bidTableRefs"
                @onPaginate="paginate"
                :last_page.sync="bid_last_page"
                :show_pagination="true"
                card_title="bid_&_budget_info"
                :items.sync="bid_and_budget_data"
                height="300"
                :headers="bid_and_budget_headers"
                :loading_api="extra_data_loading"
              />
            </div>
          </div>
        </div>
      </div>
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
          :show_label_button="true"
          :model_name.sync="current_tab"
          :selected_item="selected_item"
        />
        <!-- remark start -->
        <div class="mb-5">
          <div
            class="custom-title mb-1 ps-2 py-auto d-flex align-center"
            style="height: 40px; background-color: #f7f8fc"
          >
            Remarks
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
            <v-form @submit.prevent="submit">
              <v-text-field
                :class="`form-new-item remarks-input`"
                background-color="var(--new-input-bg)"
                outlined
                dense
                hide-details
                :placeholder="$tr('type_here') + '...'"
                v-model.trim="remark"
              >
                <template slot="append" class="pe-0">
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
                        @click="submit"
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
import StepperSidebarTop from "../../horizontal_stepper/sidebar/StepperSidebarTop.vue";
import AdvertisementProfileTop from "./AdvertisementProfileTop.vue";
import TotalInfoCard from "./TotalInfoCard.vue";
import SerriesCard from "./SerriesCard.vue";
import AdvertisementGraph2 from "./AdvertisementGraph2.vue";
import AdvertisementProfileDataTable from "./AdvertisementProfileDataTable.vue";
import moment from "moment";
import FilterDateRange from "../FilterDateRange.vue";
import SingleRemark from "../remarks/SingleRemark.vue";
import NoRemark from "../remarks/NoRemark.vue";
import PopOver from "../../design/PopOver.vue";
import { computed } from "vue";

export default {
  props: {
    current_tab: {
      type: String,
      require: true,
    },
  },
  // provide() {
  //   // use function syntax so that we can access `this`
  //   return {
  //     serries_setting: computed(() => this.serries_setting),
  //     selected_serries_injection: computed(() => this.serries_setting),
  //   };
  // },
  data() {
    return {
      serries_setting: [],
      ad_ids: [],
      ad_header: [],
      showSubmitErr: false,
      remark: "",

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
      bid_and_budget_headers: [
        { text: "budget", value: "daily_budget", align: "center" },
        { text: "bid ", value: "bid", align: "center" },
        { text: "date ", value: "bid_data_date", align: "center", width: 100 },
      ],
      selected_item: [],
      show: false,
      fullScreen: false,
      card_items: {},
      api_loading: false,
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      status_data: [],
      label_data: [],
      remark_data: [],
      bid_and_budget_data: [],
      related_data: [],
      last_page: 0,
      bid_last_page: 0,
      extra_data_loading: false,
      filter_by_date: false,
    };
  },
  computed: {
    generateId() {
      if (!this.show) return "00000";
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
    },
  },

  methods: {
    async showProfile(item = {}) {
      this.show = true;
      this.filter_by_date = false;
      this.extra_data_loading = true;
      this.selected_item = item;
      // return;
      await this.fetchHeaders();
      this.$refs.filterDateRange.onDateSelected(5);

      this.$refs.graphRefs.applySavedFilter(
        this.generateId,
        this.custom_selected_serries
      );
    },
    closeProfile() {
      console.log("close graph profile");
      this.date_range = {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      };
      this.$refs.filterDateRange.clear();
      this.show = false;
      this.selected_item = [];
      (this.label_data = []), (this.status_data = []);
      this.label_data = [];
      this.remark_data = [];
      this.bid_and_budget_data = [];
      this.related_data = [];
      this.ad_ids = [];
      this.$refs.largeTableRef.resetDatatable();
    },

    async fetchGraphTab() {
      try {
        this.api_loading = true;
        const data = {
          model_name: this.current_tab,
          request_id: this.generateId,
          ...this.date_range,
        };

        const response = await this.$axios.post(
          "advertisement/graph-profile/total_info",
          data
        );
        this.card_items = response.data;
        console.log("response", response.data);
      } catch (error) {
        console.log("error", error);
      }
      this.api_loading = false;
    },

    Filter(dates) {
      this.$refs.largeTableRef.resetDatatable();
      this.$refs.bidTableRefs.resetDatatable();
      this.date_range = dates;
      this.fetchGraphTab();
      if (!this.filter_by_date) {
        this.fetchExtraInfo();
      } else {
        this.$refs.graphRefs.Filter(dates);
      }
    },

    typeChange(type) {
      this.$refs.graphRefs.addType(type);
      console.log(type);
    },

    async reFetchExtraInfo(param = "label") {
      console.log("label refetch");
      const data = {
        ad_ids: this.ad_ids,
        model_name: this.current_tab,
        request_id: this.generateId,
        ...this.date_range,
        extra_info: param,
      };
      const response = await this.$axios.post(
        "advertisement/graph-profile/extra-info",
        data
      );
      console.log("root ", response.data);
      if (param == "label") this.label_data = this.sortData(response.data);
    },
    async fetchExtraInfo() {
      this.extra_data_loading = true;
      try {
        const data = {
          ad_ids: this.ad_ids,
          model_name: this.current_tab,
          request_id: this.generateId,
          ...this.date_range,
        };
        const response = await this.$axios.post(
          "advertisement/graph-profile/extra-info",
          data
        );
        this.bid_and_budget_data = response.data.bid_and_budget.data;
        console.log("related_data", this.related_data);
        this.last_page = response.data.related_data.last_page;
        this.bid_last_page = response.data.bid_and_budget.last_page;
        this.related_data = response.data.related_data.data;
        this.status_data = this.sortData(response.data.status);
        this.label_data = this.sortData(response.data.labels);
        this.remark_data = response.data.remarks;
        console.log("response", response.data);
      } catch (error) {
        console.log("error1", error);
      }
      this.filter_by_date = true;
      this.extra_data_loading = false;
    },
    sortData(data) {
      let status = [];
      for (const key in data) {
        status.push({ data: data[key] });
      }
      return status;
    },
    async deleteHandler(id) {
      this.remark_data = this.remark_data.filter((item) => item.id != id);
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

    async fetchHeaders() {
      try {
        const response = await this.$axios.get("sub-system-header", {
          params: {
            tab_name: "advertisement_ad",
            serries_name: "graph_profile",
          },
        });
        this.ad_header = response.data.selected_headers;
        this.serries_setting = response.data.settings;
        this.custom_selected_serries =
          response.data.selected_serries == null
            ? ["spend", "cpo", "crm_total_orders"]
            : response.data.selected_serries;
        console.log("serrries_settinn", this.serries_setting);
      } catch (error) {}
    },

    async paginate(params) {
      console.log("params", params);
      try {
        const page = params.page;
        const card = params.card_title;
        const data = {
          page: page,
          model_name: this.current_tab,
          request_id: this.generateId,
          ...this.date_range,
          card: card,
        };
        const response = await this.$axios.post(
          "advertisement/graph-profile/extra-info",
          data
        );
        if (card == "related_ads") {
          this.related_data = response.data.related_data.data;
          this.last_page = response.data.related_data.last_page;
          this.$refs.largeTableRef.togglePaginateLoading();
        } else {
          this.bid_and_budget_data = response.data.bid_and_budget.data;
          this.bid_last_page = response.data.bid_and_budget.last_page;
          this.$refs.bidTableRefs.togglePaginateLoading();
        }
        console.log("console.log", response.data.related_data);
      } catch (error) {
        console.log("error2", error);
      }
    },

    async submit() {
      let canComment = this.checkDate();
      if (canComment < 2) {
        try {
          if (this.remark != "") {
            let reqData = {
              model: this.current_tab,
              id: this.generateId,
              remark: this.remark,
            };
            let { data } = await this.$axios.post("/remarks", reqData);
            if (data.result) {
              this.remark_data.push(data.data);
              // this.$emit("addRemark", this.selected_items[0]?.id);
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
    scrollDown() {
      setTimeout(() => {
        this.$refs.remarksContainer.scrollTo(
          0,
          this.$refs.remarksContainer.scrollHeight
        ),
          20;
      });
    },
    filterByAd(ad_ids) {
      this.ad_ids = ad_ids;
      this.$refs.graphRefs.Filter(this.date_range, ad_ids);
    },
  },

  components: {
    StepperSidebarTop,
    AdvertisementProfileTop,
    TotalInfoCard,
    SerriesCard,
    AdvertisementGraph2,
    AdvertisementProfileDataTable,
    FilterDateRange,
    SingleRemark,
    NoRemark,
    PopOver,
  },
  created() {
    // this.showProfile();
  },
};
</script>

<style scoped>
.custom-title {
  font-size: 16px !important;
  text-transform: uppercase;
  font-weight: 500 !important;
  color: #777 !important;
}

.graph-container {
  background: #f7f8fc;
  z-index: 10000;
  width: 1380px;
  height: 100%;
  position: fixed;
  right: 0;
  bottom: 0;
  border-radius: 30px 0 0 30px;
  transition: all 0.5s;
}

.v-application--is-rtl .graph-container {
  right: unset;
  left: 0;
}

.graph-container.hide {
  transform: translateX(110%);
}

.v-application--is-rtl .graph-container.hide {
  transform: translateX(-110%);
}

@media only screen and (max-width: 1304px) {
  .graph-container {
    width: 100%;
  }
}

.graph-container.full-screen {
  width: 100% !important;
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
