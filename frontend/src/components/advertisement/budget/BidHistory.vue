<template>
  <!-- body -->
  <div class="overflow-auto">
    <div v-if="!loading">
      <v-timeline
        dense
        flat
        class="custom-timeLine customTimeLine"
        align-top
        v-if="allData != null"
      >
        <v-timeline-item
          v-for="history in historyData"
          :key="history.id"
          color="primary"
          icon="mdi-book"
        >
          <v-expansion-panels accordion flat class="customExpand m">
            <v-expansion-panel>
              <v-expansion-panel-header>
                <div>
                  <p class="mb-0 text-caption">
                    <!-- {{ localeHumanReadableTime(history.created_at) }} -->
                  </p>
                  <div class="text-body-1 my-1 font-weight-regular">
                    <span class="primary--text">{{
                      history.name ? history.name : $tr("Campaign")
                    }}</span>
                    <span>{{ $tr("total_bid") }}</span>
                    <v-chip
                      class="mx-2 px-1"
                      color="blue lighten-5"
                      small
                      label
                      text-color="primary"
                    >
                      {{ history.total }}$
                    </v-chip>
                  </div>
                  <span class="text-caption">
                    <v-avatar size="20">
                      <img :src="history.connections[0].platform.logo" />
                      <!-- <v-icon>mdi-facebook</v-icon> -->
                    </v-avatar>
                    <span class="ms-1">{{
                      history.connections[0].platform.platform_name
                        ? history.connections[0].platform.platform_name
                        : $tr("ad_platform")
                    }}</span>
                  </span>
                </div>
                <template v-slot:actions>
                  <span class="primary--text">{{ $tr("show_details") }}</span>
                  <v-icon color="primary"> mdi-chevron-down </v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <v-data-table
                  :headers="headers"
                  :items="history.campaign_adsets"
                  fixed-header
                  height="20vh"
                  dense
                  item-key="id"
                  hide-default-footer
                ></v-data-table>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
          <v-divider color="#edebeb"></v-divider>
        </v-timeline-item>
      </v-timeline>
    </div>

    <div v-else class="px-5">
      <v-skeleton-loader
        v-for="i in 2"
        :key="i"
        type=" list-item-avatar, list-item-three-line"
      >
      </v-skeleton-loader>
    </div>
  </div>
</template>

<script>
import moment from "moment-timezone";
import FlagIcon from "../../common/FlagIcon.vue";
export default {
  data() {
    return {
      dialogModel: false,
      api_loading: false,
      budgetDate: "today",
      budgetDates: [
        { value: "today", text: this.$tr("today") },
        { value: "yesterday", text: this.$tr("yesterday") },
        { value: "last_7_days", text: this.$tr("last_7_days") },
        { value: "last_30_days", text: this.$tr("last_30_days") },
        { value: "last_3_months", text: this.$tr("last_3_months") },
        { value: "lifetime", text: this.$tr("lifetime") },
      ],

      headers: [
        {
          text: this.$tr("name"),
          align: "start",
          value: "name",
        },
        { text: this.$tr("date"), value: "data_date" },
        { text: this.$tr("bid"), value: "bid" },
      ],
      loading: false,
      historyData: [],
      allData: [],
      propData: {},
      titleData: {},
      totalBid: 0,
    };
  },
  props: {
    data: {
      type: Object,
      require: true,
    },
  },
  created() {
    this.getId(this.data.item, this.data.model, this.data.dateRange);
  },
  methods: {
    toggleDialog(item, currentTab, filterDate) {
      this.titleData = {};

      this.dialogModel = !this.dialogModel;

      this.getId(item, currentTab, filterDate);
    },
    onClose() {
      this.clearData();
      this.dialogModel = !this.dialogModel;
    },
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
          break;
        case "sales_type":
          param = { sales_type_id: item.sales_type };
          break;
        default:
          break;
      }
      param = { ...param, ...filterDate };
      this.getHistory(param);
    },

    async getHistory(prop) {
      this.clearData();
      this.propData = prop;
      this.loading = true;
      try {
        const response = await this.$axios.post(
          "advertisement/get-bids-history",
          prop
        );
        this.loading = false;
        this.historyData = response.data.data;
        this.historyData = this.historyData.map((item) => {
          item.total = this.getTotal(item.campaign_adsets);
          this.totalBid = this.totalBid + item.total;
          return item;
        });
        this.titleData.totals = [
          { total: this.totalBid, name: this.$tr("total_bid") },
        ];

        this.$emit("setTitleData", this.titleData);
      } catch (error) {
        this.loading = false;
      }
    },
    prepareData() {},

    clearData() {
      this.historyData = {};
      this.allData = [];

      this.totalBid = 0;
    },

    getTotal(adserts) {
      let sum = 0;
      adserts.forEach((element) => {
        sum = parseFloat(element.bid) + sum;
      });
      return sum;
    },
    onDateRangeSubmit(range) {
      const timeRange = {
        country_id: this.propData.country_id,
        company_id: this.propData.company_id,
        project_id: this.propData.project_id,
        platform_id: this.propData.platform_id,
        application_id: this.propData.application_id,
        ad_account_id: this.propData.ad_account_id,
        item_code_id: this.propData.item_code_id,
        sales_type_id: this.propData.sales_type_id,
        end_date: range.end_date,
        start_date: range.start_date,
      };

      this.getHistory(timeRange);
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
  },
  components: { FlagIcon },
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
</style>
