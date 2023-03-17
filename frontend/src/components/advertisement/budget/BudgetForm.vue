<template>
  <v-dialog v-if="showDialog" v-model="showDialog" width="1200" persistent>
    <v-card>
      <v-card-title class="pa-2 pb-1 font-weight-regular" style="color: #777">
        <span class="dialog-title"> {{ $tr("budget_history") }}</span>
        <v-spacer />
        <div class="d-flex align-center">
          <span style="font-weight: normal !impotant">
            <FilterDateRange
              ref="filterDateRange"
              :hideTitle="true"
              @dateChanged="onDateRangeSubmit"
              :dateRangeProp="item_data.dateRange"
            />
          </span>
          <svg
            @click="closeDialog()"
            width="48px"
            height="48px"
            viewBox="0 0 700 560"
            fill="currentColor"
            style="transform: scaleY(-1); cursor: pointer"
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
      <!-- total parts -->
      <v-card-title
        class="w-full mb-2 pa-2 d-flex align-center card_Page_header"
        elevation="1"
        outlined
      >
        <div class="ps-4">
          <v-avatar
            size="30"
            color="primary darken-2"
            class="white--text text-h6 text-uppercase"
            outlined
          >
            <img v-if="titleData.img" :src="titleData.img" alt="" />
            <FlagIcon
              v-else-if="titleData.flag"
              :flag="titleData.flag"
              :round="true"
              medium
            />
            <span v-else>{{ titleData.logo }}</span>
          </v-avatar>

          <span class="ps-1 font-weight-regular" style="color: #777">{{
            titleData.name
          }}</span>
        </div>

        <v-spacer></v-spacer>
        <div class="pe-4" style="color: #777">
          <span
            class="text-body-1 px-1 text-center"
            v-for="total in titleData.totals"
            :key="total.name"
          >
            <span v-if="Array.isArray(total.total)">
              <span class="mx-3">{{ total.name }}:</span>
              <v-avatar
                v-for="label in total.total"
                :key="label.id"
                :color="label.color"
                size="25"
                class="ml-n1 white--text"
                style="border: 1px solid white"
              >
                {{ label.label.charAt(0) }}
              </v-avatar>
            </span>

            <span v-else>{{ total.name + ": " + total.total }}</span>
          </span>
        </div>
      </v-card-title>
      <!-- tabs :background-color="tabItems.length < 3 ? `primary` : `tabBackground`" -->
      <v-tabs
        v-model="tabIndex"
        height="40"
        active-class="activeClass"
        show-arrows
        :hide-slider="true"
        center-active
        class="px-1"
      >
        <v-tab
          v-for="(item, i) in tabItems"
          :key="i"
          :class="`px-3 me-1 customTab3 ${!$vuetify.rtl ? 'ltrTab' : 'rtlTab'}`"
          :style="`z-index: ${
            item.isSelected ? tabItems.length + 1 : tabItems.length - i
          }`"
        >
          <p
            :class="`${
              item.isSelected ? 'selected' : 'not-selected'
            } tab-title text-capitalize mb-0`"
          >
            {{ $tr(item.text) }}
          </p>
        </v-tab>
      </v-tabs>
      <!-- body -->
      <div class="overflow-auto pr-4 pl-2 mt-5" style="height: 50vh">
        <AdsetBidHistory
          ref="AdsetbidHistoryRefs"
          v-if="selected_tab == 'bid_history' && item_data.model == 'ad_set'"
          @setTitleData="setTitleData"
          :data="item_data"
        ></AdsetBidHistory>
        <BidHistory
          ref="bidHistoryRefs"
          v-else-if="
            selected_tab == 'bid_history' && item_data.model != 'campaign'
          "
          @setTitleData="setTitleData"
          :data="item_data"
        />
        <CompaignBidHistory
          ref="campaignBidHistoryRefs"
          v-if="selected_tab == 'bid_history' && item_data.model == 'campaign'"
          :data="item_data"
          @setTitleData="setTitleData"
        />
        <LabelHistory
          ref="labelRefs"
          v-if="selected_tab == 'label'"
          :data="item_data"
          @setTitleData="setTitleData"
        ></LabelHistory>

        <BudgetHistoryList
          ref="budgetHistoryRefs"
          v-if="selected_tab == 'budget_history'"
          :item_data.sync="item_data"
          @setTitleData="setTitleData"
        ></BudgetHistoryList>
        <RemarkHistory
          v-if="selected_tab == 'remark'"
          :selected_items="item_data"
          ref="remarkRefs"
          @setTitleData="setTitleData"
        />
        <StatusHistory
          ref="statusHistoryRefs"
          v-if="selected_tab == 'status_history'"
          :statusData="item_data"
        />

        <ProductStatusHistory
          ref="productStatusHistoryRefs"
          v-if="selected_tab == 'product_status_history'"
          :statusData="item_data"
        />
      </div>
      <v-card-actions class="justify-end">
        <v-btn class="primary" @click="closeDialog()">{{ $tr("close") }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import CustomInput from "../../design/components/CustomInput.vue";
import FilterDateRange from "../FilterDateRange.vue";
import FlagIcon from "../../common/FlagIcon.vue";
import TabHistory from "./TabHistory.vue";
import BudgetHistoryList from "./BudgetHistoryList.vue";
import BidHistory from "./BidHistory.vue";
import LabelHistory from "./LabelHistory.vue";
import RemarkHistory from "./RemarkHistory.vue";
import CompaignBidHistory from "./CompaignBidHistory.vue";
import StatusHistory from "./StatusHistory.vue";
import AdsetBidHistory from "./AdsetBidHistory.vue";
import ProductStatusHistory from "./ProductStatusHistory.vue";
export default {
  data() {
    return {
      showDialog: false,
      budget_api_loading: false,

      item_data: {},
      history_data: [],
      totals: { total_budget: 0, total_spend: 0 },
      titleData: {},
      selected_tab: "budget_history",

      tabIndex: 2,
      tabItems: [],
      defaultTabItems: [
        {
          text: "budget_history",
          icon: "fa-table",
          isSelected: false,
          key: "budget_history",
        },
        {
          text: "bid_history",
          icon: "fa-ban",
          isSelected: false,
          key: "bid_history",
        },
        {
          text: "status_history",
          icon: "fa-ban",
          isSelected: false,
          key: "status_history",
        },
        {
          text: "label",
          icon: "fa-thumbs-up",
          isSelected: false,
          key: "label",
        },
        {
          text: "remark",
          icon: "fa-ban",
          isSelected: false,
          key: "remark",
        },
        {
          text: "product_history",
          icon: "fa-ban",
          isSelected: false,
          key: "product_status_history",
        },
      ],
      dateRange: {},
    };
  },
  watch: {
    tabIndex: function (val, oldValue) {
      this.tabItems[val].isSelected = true;
      this.tabItems[oldValue].isSelected = false;

      this.getSelectedTabRecords(this.tabItems[val].key);
    },
  },
  methods: {
    closeDialog() {
      this.showDialog = false;
      this.tabIndex = 2;
    },
    async openDialog(data) {
      await this.filterTabs(data);
      this.titleData = {};
      this.item_data = data;
      this.showDialog = true;

      const index = this.tabItems.findIndex((object) => {
        return object.key === this.item_data.tab;
      });
      this.dateRange = this.item_data.dateRange;
      this.tabIndex = index;
      this.tabItems.forEach((object) => {
        object.isSelected = false;
      });
      this.tabItems[index].isSelected = true;
      this.gettitleData();
    },

    filterTabs(data) {
      let removeTabs = [];
      switch (data.model) {
        case "country":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "company":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "project":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "item_code":
          removeTabs = ["product_status_history"];
          break;
        case "platform":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "organization":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "ad_account":
          removeTabs = ["budget_history", "product_status_history"];
          break;
        case "campaign":
          removeTabs = ["product_status_history"];
          break;
        case "ad_set":
          removeTabs = ["product_status_history"];
          break;
        case "ad":
          removeTabs = ["bid_history", "product_status_history"];
          break;

        default:
          break;
      }
      this.tabItems = this.defaultTabItems.filter((item) => {
        if (!removeTabs.includes(item.key)) {
          return item;
        }
      });
    },

    onDateRangeSubmit(range) {
      switch (this.selected_tab) {
        case "bid_history":
          if (this.item_data.model == "campaign")
            this.$refs.campaignBidHistoryRefs.onDateRangeSubmit(range);
          else if (this.item_data.model == "ad_set")
            this.$refs.AdsetbidHistoryRefs.onDateRangeSubmit(range);
          else this.$refs.bidHistoryRefs.onDateRangeSubmit(range);
          break;
        case "budget_history":
          this.$refs.budgetHistoryRefs.onDateRangeSubmit(range);
          break;
        case "status_history":
          this.$refs.statusHistoryRefs.onDateRangeSubmit(range);
          break;
        case "label":
          this.$refs.labelRefs.onDateRangeSubmit(range);
          break;
        case "remark":
          this.$refs.remarkRefs.onDateRangeSubmit(range);
          break;
        case "product_status_history":
          this.$refs.productStatusHistoryRefs.onDateRangeSubmit(range);
          break;

        default:
          break;
      }
    },

    getSelectedTabRecords(tab) {
      this.selected_tab = tab;
      this.titleData.totals = [];
    },

    setTitleData(titleData) {
      this.titleData = { ...this.titleData, ...titleData };
    },

    gettitleData() {
      let item = this.item_data.item;
      switch (this.item_data.model) {
        case "ad":
          this.titleData = {
            name: item.ad_name,
            logo: item.ad_name[0]?.toUpperCase(),
          };
          break;
        case "platform":
          this.titleData = { name: item.platform_name, img: item.logo };

          break;
        case "organization":
          this.titleData = {
            name: item.name,
            logo: item.name[0]?.toUpperCase(),
          };
          break;
        case "ad_account":
          this.titleData = {
            name: item.name,
            logo: item.name[0]?.toUpperCase(),
          };
          break;
        case "country":
          this.titleData = { name: item.name, flag: item.iso2.toLowerCase() };
          break;
        case "company":
          this.titleData = { name: item.name, img: item.logo };
          break;
        case "item_code":
          this.titleData = {
            name: item.pname,
            logo: item.pname[0]?.toUpperCase(),
          };
          break;
        case "sales_type":
          this.titleData = {
            name: item.sales_type,
            logo: item.sales_type[0]?.toUpperCase(),
          };
          break;
        default:
          this.titleData = {
            name: item.name,
            logo: item.name[0]?.toUpperCase(),
          };
          break;
      }
    },
    getTabCount() {},
    getId(item, currentTab, filterDate) {
      let id = item.id;
      let param = "country";
      switch (currentTab) {
        case "project":
          param = { project_id: id };
          break;
        case "platform":
          param = { platform_id: id };
          break;
        case "organization":
          param = { application_id: id };
          break;
        case "ad_account":
          param = { ad_account_id: id };
          break;
        case "country":
          param = { country_id: id };
          break;
        case "company":
          param = { company_id: id };
          break;
        case "item_code":
          param = { item_code_id: item.pcode };
        case "sales_type":
          param = { sales_type_id: item.sales_type };
          break;
        default:
          break;
      }
      param = { ...param, ...filterDate };
      this.getTabCount(param);
    },
  },
  components: {
    CustomInput,
    FilterDateRange,
    FlagIcon,
    TabHistory,
    BudgetHistoryList,
    BidHistory,
    LabelHistory,
    RemarkHistory,
    CompaignBidHistory,
    StatusHistory,
    AdsetBidHistory,
    ProductStatusHistory,
  },
};
</script>

<style scoped>
.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}

.custom-chip {
  color: #777;
  padding: 5px 5px;
  border-radius: 20px;
  border: solid 1.5px rgb(212, 212, 212);
  cursor: pointer;
}

.custom-chip.selected {
  background: var(--v-primary-base);
  color: var(--bg-white);
  border: unset;
}

.label-color {
  display: inline-block;
  width: 23px;
  height: 23px;

  border-radius: 50px;
}

.custom-chip.selected .label-color {
  border: 2.3px solid var(--bg-white);
  width: 25px !important;
  height: 25px !important;
}

.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}

.custom-wraper {
  height: 500px;
  overflow-y: auto;

  scroll-behavior: smooth;
}

.custom-btn {
  font-size: 14px;
  font-weight: 400;
}
</style>
<style>
.customExpand .v-expansion-panel-header__icon {
  align-items: center !important;
}

.customTimeLine .v-timeline-item__dot {
  box-shadow: unset !important;
  margin-top: 36% !important;
}

.custom-timeLine::before {
  width: 0.5px !important;
  background: #edebeb !important;
}
</style>
