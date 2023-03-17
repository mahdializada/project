<template>
  <v-card v-if="showDialog">
    <v-card-text class="overflow-auto" style="height: 50vh">
      <div v-if="budget_api_loading">
        <v-skeleton-loader
          v-for="i in 5"
          :key="i"
          type="list-item-avatar-two-line"
        >
        </v-skeleton-loader>
      </div>
      <v-timeline
        v-else
        align-top
        dense
        flat
        class="customTimeLine custom-timeLine"
      >
        <v-timeline-item
          class="pb-1"
          v-for="(item, j) in history_data"
          :key="j"
          color="primary"
        >
          <template v-slot:icon>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 34.999 35.346"
            >
              <path
                id="budget-icon"
                d="M267.113-3529.552a11.4,11.4,0,0,1-3.635-2.451,11.4,11.4,0,0,1-2.451-3.635,11.353,11.353,0,0,1-.9-4.451,11.352,11.352,0,0,1,.9-4.45,11.4,11.4,0,0,1,2.451-3.635,11.4,11.4,0,0,1,3.635-2.451,11.35,11.35,0,0,1,4.451-.9,11.351,11.351,0,0,1,4.451.9,11.4,11.4,0,0,1,3.635,2.451,11.4,11.4,0,0,1,2.451,3.635,11.353,11.353,0,0,1,.9,4.45,11.354,11.354,0,0,1-.9,4.451A11.4,11.4,0,0,1,279.65-3532a11.4,11.4,0,0,1-3.635,2.451,11.371,11.371,0,0,1-4.451.9A11.37,11.37,0,0,1,267.113-3529.552Zm1.935-7.492-1.024,1a.458.458,0,0,0-.14.36.492.492,0,0,0,.193.361,4.158,4.158,0,0,0,2.545.883v1.413a.472.472,0,0,0,.471.471h.941a.472.472,0,0,0,.472-.471v-1.418a3.322,3.322,0,0,0,3.109-2.139,3.2,3.2,0,0,0-.172-2.544,3.241,3.241,0,0,0-1.961-1.625l-3.178-.93a.871.871,0,0,1-.624-.833.869.869,0,0,1,.868-.868H272.5a1.8,1.8,0,0,1,1.006.31.445.445,0,0,0,.248.075.47.47,0,0,0,.327-.134l1.024-1a.46.46,0,0,0,.139-.36.492.492,0,0,0-.193-.361,4.164,4.164,0,0,0-2.545-.883v-1.413a.472.472,0,0,0-.472-.471h-.941a.472.472,0,0,0-.471.471v1.413h-.074a3.231,3.231,0,0,0-2.379,1.051,3.192,3.192,0,0,0-.829,2.468,3.371,3.371,0,0,0,2.466,2.842l3.017.884a.874.874,0,0,1,.624.833.87.87,0,0,1-.868.869h-1.951a1.8,1.8,0,0,1-1.006-.31.445.445,0,0,0-.247-.075A.469.469,0,0,0,269.048-3537.044Zm-10.468,3.539h-7.114A3.469,3.469,0,0,1,248-3536.97v-18.366a3.469,3.469,0,0,1,3.465-3.465h1.386v-2.079A3.122,3.122,0,0,1,255.97-3564a3.122,3.122,0,0,1,3.118,3.119v2.079h9.01v-2.079a3.123,3.123,0,0,1,3.119-3.119,3.123,3.123,0,0,1,3.118,3.119v2.079h1.039a3.469,3.469,0,0,1,3.465,3.465v2.64a14.548,14.548,0,0,0-7.276-1.947,14.571,14.571,0,0,0-14.555,14.554,14.4,14.4,0,0,0,1.57,6.583h0Z"
                transform="translate(-248 3564)"
                fill="white"
              />
            </svg>
          </template>
          <v-expansion-panels
            accordion
            flat
            class="customExpand"
            v-model="panel_model[j]"
          >
            <v-expansion-panel>
              <v-expansion-panel-header
                @click="togglepanel(j, item)"
                class="ps-0"
              >
                <div>
                  <p class="mb-0 text-caption">{{ item.data_date }}</p>
                  <div class="text-body-1 my-1 font-weight-regular">
                    <!-- for other tabs -->
                    <span class="">{{ item.name }}</span>
                    <!-- <span class="primary--text d-inline-block m2-3">{{
											item.name
										}}</span> -->

                    <v-chip
                      class="mx-3 px-1"
                      color="blue lighten-5"
                      small
                      label
                      text-color="primary"
                      >{{ item.daily_budget ? item.daily_budget : 0 }}</v-chip
                    >
                  </div>
                  <span class="text-caption">
                    <v-avatar size="20">
                      <v-img :src="item.platform.logo"></v-img>
                    </v-avatar>
                    <span class="primary--text ms-1">{{
                      item.ad_account.name
                    }}</span>
                  </span>
                </div>
                <template v-slot:actions>
                  <span class="primary--text">{{ $tr("show_spends") }}</span>
                  <v-icon color="primary"> mdi-chevron-down </v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <div v-if="details_loading">
                  <v-skeleton-loader
                    v-for="i in 2"
                    :key="i"
                    type="table-heading"
                  >
                  </v-skeleton-loader>
                </div>
                <v-data-table
                  v-if="!details_loading"
                  :headers="headers"
                  :items="item.ads"
                  fixed-header
                  max-height="20vh"
                  dense
                  item-key="text"
                  hide-default-footer
                >
                  <template v-slot:[`item.ad_name`]="{ item }">
                    <span>
                      {{ item.ad_name }}
                    </span>
                  </template>
                  <template v-slot:[`item.spend`]="{ item }">
                    <span>
                      {{ item.spend }}
                    </span>
                  </template>
                </v-data-table>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
          <v-divider class="me-4"></v-divider>
        </v-timeline-item>
      </v-timeline>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      showDialog: false,
      budget_api_loading: false,
      details_loading: false,
      budgetDate: "today",

      headers: [
        {
          text: this.$tr("ad_name"),
          align: "start",
          value: "ad_name",
          width: "75%",
        },
        { text: this.$tr("spend"), value: "spend" },
      ],

      history_data: [],
      panels: null,
      panel_model: [],
      total_spends: 0,
      total_budget: 0,
    };
  },

  props: {
    item_data: {
      type: Object,
      require: true,
    },
  },
  created() {
    this.showDialog = true;
    this.fetchBudgetHistory(this.item_data.dateRange);
    this.panel_model = [];
  },
  methods: {
    closeDialog() {
      this.showDialog = false;
    },
    openDialog(data) {
      this.item_data = data;
      this.showDialog = true;
      this.fetchBudgetHistory();
      this.panel_model = [];
    },
    generateId() {
      switch (this.item_data.model) {
        case "campaign":
          return this.item_data.item.campaign_id;

        case "item_code":
          return this.item_data.item.pcode;
        case "sales_type":
          return this.item_data.item.sales_type;

        case "ad_set":
          return this.item_data.item.adset_id;
        default:
          return this.item_data.item.id;

          break;
      }
    },
    async fetchBudgetHistory(date_range = {}) {
      try {
        this.budget_api_loading = true;
        let data = { item_id: this.generateId(), model: this.item_data.model };
        data = { ...data, ...date_range };

        const response = await this.$axios.post(
          "advertisement/budget-history",
          data
        );
        this.history_data = response.data.budgets;
        this.total_spends = response.data.extra_data.total_spend;
        this.total_budget = response.data.extra_data.total_budget;
        this.currency = response.data.currency;

        let totals = {
          totals: [
            { name: " Total Spends", total: this.total_spends + " USD" },
            { name: " Total Budget", total: this.total_budget + " USD" },
          ],
        };
        this.$emit("setTitleData", totals);
      } catch (error) {}
      this.budget_api_loading = false;
    },
    togglepanel(index, data) {
      if (!this.history_data[index].ads) {
        this.fetchHistoryDetails(data, index);
      }

      this.panels = index;
      this.panel_model = this.panel_model.map((row, index) => {
        if (index != this.panels) return null;
        return row;
      });
    },

    async fetchHistoryDetails(data, index) {
      try {
        this.details_loading = true;
        let temp_data = {
          adset_id: data.adset_id,
          model: this.item_data.model,
          date: data.data_date,
        };
        const response = await this.$axios.post(
          "advertisement/budget-history/details",
          temp_data
        );
        this.history_data[index].ads = response.data;
        this.history_data = [...this.history_data];
        // this.$emit("saveDetails", { data: response.data, index: this.panels });
      } catch (error) {}
      this.details_loading = false;
    },
    onDateRangeSubmit(range) {
      const timeRange = {
        end_date: range.end_date,
        start_date: range.start_date,
      };
      this.fetchBudgetHistory(timeRange);
    },
    getBudgetFilterName() {
      switch (this.item_data.model) {
        case "platform":
          return this.item_data.item.platform_name;
        case "sales_type":
          return this.item_data.item.sales_type.charAt(0);
        default:
          return this.item_data.item.name;
      }
    },
    getAvatar() {
      switch (this.item_data.model) {
        case "country":
          return this.item_data.item.iso2.toLowerCase();
        case "company":
          return this.item_data.item.logo;
        case "sales_type":
          return this.item_data.item.sales_type.charAt(0);
        case "item_code":
          return this.item_data.item.pname.charAt(0);
        case "platform":
          return this.item_data.item.logo;
        default:
          return this.item_data.item.name.charAt(0);
      }
    },
  },
  components: {},
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
