<template>
  <!-- timeline -->
  <div class="py-3 history-body" style="position: relative">
    <v-timeline
      :reverse="reverse"
      class="customTimeLine custom-timeLine"
      dense
      v-if="!loading && has_status > 0"
      flat
      align-top
    >
      <v-timeline-item
        v-for="history in data"
        :key="history[0].id"
        :color="history[0].status == 'inactive' ? 'grey' : 'primary'"
      >
        <template v-slot:icon>
          <v-icon light size="15" color="white">mdi-clipboard-check</v-icon>
        </template>

        <v-expansion-panels>
          <v-expansion-panel>
            <v-expansion-panel-header>
              <div class="">
                <p class="mb-0 text-caption">
                  {{ localeHumanReadableTime(history[0].created_at) }}
                </p>

                <div class="text-body-1 my-1 d-flex font-weight-regular">
                  <v-avatar size="20">
                    <img :src="history[0].creator.image" />
                    <!-- <v-icon>mdi-facebook</v-icon> -->
                  </v-avatar>
                  <span class="ms-1 primary--text">
                    {{
                      history[0].creator.firstname +
                      " " +
                      history[0].creator.lastname
                    }}</span
                  >
                  <span class="ms-1"> {{ $tr("changed_status_to") }}</span>
                  <!-- <v-chip
										class="mx-2 px-1"
										color="blue lighten-5"
										small
										label
										:text-color="
											history[0].status == 'inactive' ? 'grey' : 'primary'
										"
									>
										{{ history[0].status }}
									</v-chip> -->

                  <v-switch
                    :input-value="history[0].status.toLowerCase() == 'active'"
                    inset
                    color="#06d7a0"
                    hide-details
                    readonly
                    dense
                    class="mt-0 mx-2 display-inline"
                    style="margin-top: -2px !important"
                  />
                </div>
              </div>

              <template v-slot:actions>
                <span class="primary--text mt-1"> See Reasons</span>
                <v-icon color="primary"> mdi-chevron-down </v-icon>
              </template>
            </v-expansion-panel-header>

            <v-expansion-panel-content>
              <v-list-item v-for="reason in history" :key="reason.id">
                <v-list-item-icon>
                  <v-icon> mdi-subdirectory-arrow-right </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  {{ reason.reasons.title }}
                </v-list-item-content>
              </v-list-item>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <v-divider></v-divider>
      </v-timeline-item>
    </v-timeline>
    <div
      v-if="!loading && has_status == 0"
      class="text-center"
      style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
      "
    >
      <NoStatus />
      <p
        class="mb-0 text-h6"
        style="font-size: 20px; font-weight: 500; color: #777"
      >
        {{ $tr("no_status_activity") }}
      </p>
    </div>
    <div v-if="loading" class="px-5">
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
import NoStatus from "./NoStatus.vue";

export default {
  data() {
    return {
      model: false,
      active: true,
      reverse: false,
      panel: [],
      data: [],
      grouped: [],
      props: {},
      loading: false,
      has_status: 0,
    };
  },
  props: {
    statusData: {
      type: Object,
      require: true,
    },
  },
  created() {
    let data = {
      model: this.statusData.model,
      id:
        this.statusData.model == "item_code"
          ? this.statusData.item.pcode
          : this.statusData.model == "sales_type"
          ? this.statusData.item.sales_type
          : this.statusData.item.id,
    };
    data = { ...data, ...this.statusData.dateRange };
    this.fetchAllData(data);
  },
  methods: {
    onClose() {
      this.model = !this.model;
    },
    async onToggleDialog(data) {
      this.model = !this.model;
      await this.fetchAllData(data);
    },

    async fetchAllData(data) {
      this.props = data;
      try {
        this.loading = true;
        this.data = [];
        let res = await this.$axios.post("/get-reason-history", data);
        this.has_status = res.data.length;
        this.data = await this.groupBy(res.data, "uuid");

        this.loading = false;
        // this.$emit("setTitleData", {
        // 	totals: [
        // 		{
        // 			name: "current status",
        // 			total: statusData.item.advertisement_status,
        // 		},
        // 	],
        // });
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("something_went_wrong"));

        this.loading = false;
      }
    },
    groupBy(arr, criteria) {
      const newObj = arr.reduce(function (acc, currentValue) {
        if (!acc[currentValue[criteria]]) {
          acc[currentValue[criteria]] = [];
        }
        acc[currentValue[criteria]].push(currentValue);
        return acc;
      }, {});
      return newObj;
    },
    onDateRangeSubmit(range) {
      const timeRange = {
        id: this.props.id,
        model: this.props.model,
        start_date: range.start_date,
        end_date: range.end_date,
      };
      this.fetchAllData(timeRange);
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
  },
  components: { NoStatus },
};
</script>

<style scoped>
.history-body {
  height: 40vh;

  overflow-y: auto;
}
</style>
<style>
#custom-scroll::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

#custom-scroll::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}

#custom-scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2);
}

.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}

.customTimeLine .v-timeline-item__dot {
  box-shadow: unset !important;
  margin-top: 36% !important;
}
</style>
