<template>
  <v-dialog v-if="dialogModel" v-model="dialogModel" width="800" persistent>
    <v-card>
      <div class="px-2 pt-2 pb-2 d-flex" style="color: #777">
        <div class="text-capitalize dialog-title">{{ $tr("history") }}</div>
        <v-spacer />
        <div class="d-flex">
          <span style="font-weight: normal !impotant; color: red">
            <FilterDateRange
              ref="filterDateRange"
              :hideTitle="true"
              @dateChanged="onDateRangeSubmit"
            />
          </span>

          <svg
            @click="onClose"
            width="42px"
            height="42px"
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
      </div>
      <v-card-title
        class="pa-0 font-weight-regular"
        style="box-shadow: unset !important"
      >
        <v-card
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

            <span class="ps-1 font-weight-medium">{{ titleData.name }}</span>
          </div>

          <v-spacer></v-spacer>
          <div class="pe-4">
            <span class="text-body-1 px-1 text-center">
              {{ $tr("total_bid") }} : {{ totalBid }}</span
            >
          </div>
        </v-card>
      </v-card-title>
      <!-- body -->
      <v-card-text class="overflow-auto" style="height: 50vh">
        <div v-if="!loading">
          <v-timeline
            align-top
            dense
            flat
            class="customTimeLine"
            v-if="allData != null"
          >
            <v-timeline-item
              class="pb-1"
              v-for="history in historyData"
              :key="history.id"
              color="primary"
              icon="mdi-book"
            >
              <v-expansion-panels accordion flat class="customExpand">
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
                        <span>{{
                          history.connections
                            ? history.connections[0].pname
                            : ""
                        }}</span>
                        <v-chip
                          class="mx-2 px-1"
                          color="blue lighten-5"
                          small
                          label
                          text-color="primary"
                        >
                          {{ history.total }} $
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
                      <span class="primary--text">{{ $tr("show_cost") }}</span>
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
              <v-divider></v-divider>
            </v-timeline-item>
          </v-timeline>
        </div>

        <div v-else class="px-5">
          <v-skeleton-loader
            v-for="i in 2"
            :key="i"
            type=" list-item-avatar, list-item-three-line"
          ></v-skeleton-loader>
        </div>
      </v-card-text>
      <v-card-actions class="justify-end">
        <v-btn class="primary" @click="onClose">{{ $tr("close") }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import CustomInput from "../design/components/CustomInput.vue";
import FilterDateRange from "./FilterDateRange.vue";
import moment from "moment-timezone";
import FlagIcon from "../common/FlagIcon.vue";
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
          text: "name",
          align: "start",
          value: "name",
        },
        { text: "date", value: "data_date" },
        { text: "bid", value: "bid" },
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
  methods: {
    toggleDialog(item, currentTab, filterDate) {
      this.titleData = {};
      console.log("tab", currentTab);
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

      this.titleData =
        currentTab == "platform" || currentTab == "item_code"
          ? ""
          : { name: item.name, logo: item.name[0]?.toUpperCase() };

      switch (currentTab) {
        case "project":
          param = { project_id: id };
          break;
        case "platform":
          this.titleData = { name: item.platform_name, img: item.logo };
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
          this.titleData = { name: item.name, flag: item.iso2.toLowerCase() };
          break;
        case "company":
          param = { company_id: id };
          this.titleData = { name: item.name, img: item.logo };
          break;
        case "item_code":
          this.titleData = {
            name: item.pname,
            logo: item.pname[0]?.toUpperCase(),
          };
          param = { item_code_id: item.pcode };
          break;
        case "sales_type":
          this.titleData = {
            name: item.sales_type,
            logo: item.sales_type[0]?.toUpperCase(),
          };
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
        sum = parseInt(element.bid) + sum;
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
  components: { CustomInput, FilterDateRange, FlagIcon },
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
  margin-top: 36% !important ;
}
</style>
