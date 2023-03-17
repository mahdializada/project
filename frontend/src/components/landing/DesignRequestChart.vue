     <template>
  <v-card elevation="1" outlined>
    <!-- <v-btn @click="getData()">fetch Data</v-btn> -->
    <v-card-title class="">
      <span>{{ $tr("design_request") }}</span>
      <v-spacer></v-spacer>
      <div class="text-center">
        <v-btn-toggle
          v-model="selected_type"
          rounded
          dense
          color="primary"
          background-color="grey lighten-2"
        >
          <v-btn
            class="pa-2 text-capitalize font-weight-medium"
            :loading="is_prev == 'current' && is_loading && selected_type == 0"
          >
            {{ $tr("day") }}
          </v-btn>
          <v-btn
            class="pa-2 text-capitalize font-weight-medium"
            :loading="is_prev == 'current' && is_loading && selected_type == 1"
          >
            {{ $tr("week") }}
          </v-btn>
          <v-btn
            class="pa-2 text-capitalize font-weight-medium"
            :loading="is_prev == 'current' && is_loading && selected_type == 2"
          >
            {{ $tr("month") }}
          </v-btn>
          <v-btn
            class="pa-2 text-capitalize font-weight-medium"
            :loading="is_prev == 'current' && is_loading && selected_type == 3"
          >
            {{ $tr("year") }}
          </v-btn>
        </v-btn-toggle>
      </div>
    </v-card-title>
    <v-card-text>
      <client-only>
        <apexchart
          :key="key"
          :title="$tr('design_request')"
          height="260"
          :options="chartOptions"
          :series="chartSeries"
        ></apexchart>
      </client-only>
      <div class="d-flex justify-center align-center">
        <!-- left -->

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              class="primary white--text"
              small
              v-bind="attrs"
              v-on="on"
              @click="prev()"
              :loading="is_loading && is_prev == 'true'"
            >
              <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
          </template>
          <span v-if="selected_type == 0">
            {{
              day_name[changeDate.getDay() == 0 ? 5 : changeDate.getDay() - 1]
            }}
          </span>
          <span v-else-if="selected_type == 2"
            >{{
              month_name[
                changeDate.getMonth() == 0 ? 11 : changeDate.getMonth() - 1
              ]
            }}
          </span>
          <span v-else-if="selected_type == 3">
            {{ changeDate.getFullYear() - 1 }}
          </span>
          <span v-else> {{ prev_week }}</span>
        </v-tooltip>

        <v-chip class="mx-1" v-if="selected_type == 2">
          {{ chartOptions.labels[0] }} -
          {{ chartOptions.labels[chartOptions.labels.length - 1] }}
        </v-chip>
        <v-chip class="mx-1" v-else-if="selected_type == 3">
          {{ changeDate.getFullYear() }}
        </v-chip>
        <v-chip class="mx-1" v-else-if="selected_type == 1"
          >{{ month_name[changeDate.getMonth()] }}
          {{ changeDate.getFullYear() }}</v-chip
        >
        <v-chip class="mx-1" v-else
          >{{ day_name[changeDate.getDay()] }}

          {{ changeDate.getDate() }}
          {{ month_name[changeDate.getMonth()] }}
          {{ changeDate.getFullYear() }}</v-chip
        >

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              :class="
                count == 0 ? 'grey lighten-2 grey--text' : 'primary white--text'
              "
              small
              @click="next()"
              :loading="is_loading && is_prev == 'false'"
              v-bind="attrs"
              v-on="on"
            >
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </template>
          <span v-if="selected_type == 0">
            {{
              count != 0
                ? day_name[
                    changeDate.getDay() == 0
                      ? 5
                      : changeDate.getDay() == 5
                      ? 0
                      : changeDate.getDay() + 1
                  ]
                : $tr("no_more_data")
            }}
          </span>
          <span v-else-if="selected_type == 2">{{
            count != 0
              ? month_name[
                  changeDate.getMonth() == 11 ? 0 : changeDate.getMonth() + 1
                ]
              : $tr("no_more_data")
          }}</span>
          <span v-else-if="selected_type == 3">
            {{
              count != 0 ? changeDate.getFullYear() + 1 : $tr("no_more_data")
            }}
          </span>
          <span v-else>
            {{ count != 0 ? next_week : $tr("no_more_data") }}
          </span>
        </v-tooltip>
      </div>
    </v-card-text>
  </v-card>
</template>

     <script>
export default {
  data() {
    return {
      month_name: [
        this.$tr("January"),
        this.$tr("February"),
        this.$tr("March"),
        this.$tr("April"),
        this.$tr("May"),
        this.$tr("June"),
        this.$tr("July"),
        this.$tr("August"),
        this.$tr("September"),
        this.$tr("October"),
        this.$tr("November"),
        this.$tr("December"),
      ],
      month_name2: [
        this.$tr("Feb"),
        this.$tr("Jan"),
        this.$tr("Mar"),
        this.$tr("Apr"),
        this.$tr("May"),
        this.$tr("Jun"),
        this.$tr("Jul"),
        this.$tr("Aug"),
        this.$tr("Sep"),
        this.$tr("Oct"),
        this.$tr("Nov"),
        this.$tr("Dec"),
      ],
      day_name: [
        this.$tr("Mon"),
        this.$tr("Tue"),
        this.$tr("Wed"),
        this.$tr("Thu"),
        this.$tr("Fri"),
        this.$tr("Sut"),
        this.$tr("Sun"),
      ],
      is_loading: false,
      is_prev: "current",
      selected_type: 0,
      current_type: 0,
      filter_menu: false,
      dateRange: null,
      today: new Date(),
      changeDate: new Date(),
      date: new Date().toISOString().substr(0, 10),
      filter_types: ["day", "week", "month", "year"],
      key: 0,

      prev_week: "",
      next_week: "",
      count: 0,

      // Graph
      chartSeries: [
        +{
          name: this.$tr("waiting"),
          data: [],
        },
        {
          name: this.$tr("in_progress"),
          data: [],
        },
        {
          name: this.$tr("cancelled"),
          data: [],
        },
        {
          name: this.$tr("completed"),
          data: [],
        },
      ],

      chartOptions: {
        responsive: [
          {
            breakpoint: 1000,
            options: {
              plotOptions: {
                bar: {
                  horizontal: false,
                },
              },
              legend: {
                position: "top",
                offsetY: 12,
              },
            },
          },
        ],

        colors: ["#DDB099", "#1E6AD4", "#FF0000", "#00B41D", "#00B41D"],
        // xaxis: {
        //   categories: [],
        // },
        markers: {
          size: 5,
          colors: "#fff",
          strokeColors: ["#DDB099", "#1E6AD4", "#FF0000", "#00B41D", "#00B41D"],
          strokeWidth: 2,
          hover: { size: 7 },
        },

        dataLabels: {
          enabled: false,
        },
        labels: [],
        noData: {
          text: this.$tr("no_data_available"),
          align: "center",
          verticalAlign: "middle",
        },
        legend: {
          show: true,
          itemMargin: {
            horizontal: 10,
            vertical: 7,
          },
          position: "right",
          fontFamily: "Poppins",
          fontWeight: 500,
          offsetY: 40,
        },
        theme: {
          mode: this.$vuetify.theme.dark ? "dark" : "light",
        },

        chart: {
          toolbar: {
            show: true,
          },
          type: "area",
          stacked: false,
        },
        dataLabels: {
          enabled: false,
        },

        stroke: {
          curve: "straight",
        },
        fill: {
          type: "gradient",
          gradient: {
            opacityFrom: 0.8,
            opacityTo: 0,
            type: "vertical",
          },
        },
      },
    };
  },

  watch: {
    selected_type: function () {
      this.changeDate = new Date();
      this.count = 0;
      this.is_prev = "current";
      if (this.selected_type == 0) {
        this.filterByDay("manual");
      }
      if (this.selected_type == 1) {
        this.filterByWeek("manual");
      }
      if (this.selected_type == 2) {
        this.filterByMonth("manual");
      }
      if (this.selected_type == 3) {
        this.filterByYear("manual");
      }
    },
  },
  created() {
    this.filterByDay("manual");
  },
  async mounted() {
    // chart real time
    this.$echo.private(`update.design-request`).listen("Updated", async (e) => {
      const today = new Date(); // get today date
      if (e.action == "created") {
        // change chart when new record added
        if (this.selected_type == 0) {
          this.chartSeries[0].data[today.getHours() - 1]++;
          this.key++;
        } else if (this.selected_type == 1) {
          this.chartSeries[0].data[today.getDay() - 1]++;
          this.key++;
        } else if (this.selected_type == 2) {
          this.chartSeries[0].data[today.getDate() - 1]++;
          this.key++;
        } else if (this.selected_type == 3) {
          this.chartSeries[0].data[today.getMonth()]++;
          this.key++;
        }
      } else if (e.action == "status-changed") {
        // change record status with real time
        var current_status_index = null;
        var new_status_index = null;
        // find current status
        if (e.data.current_status == "waiting") {
          current_status_index = 0;
        } else if (e.data.current_status == "in storyboard") {
          current_status_index = 1;
        } else if (e.data.current_status == "cancelled") {
          current_status_index = 2;
        } else if (e.data.current_status == "completed") {
          current_status_index = 3;
        }
        // find new status
        if (e.data.new_status == "waiting") {
          new_status_index = 0;
        } else if (e.data.new_status == "in storyboard") {
          new_status_index = 1;
        } else if (e.data.new_status == "cancelled") {
          new_status_index = 2;
        } else if (e.data.new_status == "completed") {
          new_status_index = 3;
        }
        // check if new status is removed
        if (e.data.new_status == "removed") {
          if (this.selected_type == 0) {
            this.chartSeries[current_status_index].data[today.getHours() - 1]--;
            this.key++;
          } else if (this.selected_type == 1) {
            this.chartSeries[current_status_index].data[today.getDay() - 1]--;
            this.key++;
          } else if (this.selected_type == 2) {
            this.chartSeries[current_status_index].data[today.getDate() - 1]--;
            this.key++;
          } else if (this.selected_type == 3) {
            this.chartSeries[current_status_index].data[today.getMonth() - 1]--;
            this.key++;
          }
        }
        // if new status is not removed
        else if (current_status_index != null && new_status_index != null) {
          if (this.selected_type == 0) {
            this.chartSeries[current_status_index].data[today.getHours() - 1]--;
            this.chartSeries[new_status_index].data[today.getHours() - 1]++;
            this.key++;
          } else if (this.selected_type == 1) {
            this.chartSeries[current_status_index].data[today.getDay() - 1]--;
            this.chartSeries[new_status_index].data[today.getDay() - 1]++;
            this.key++;
          } else if (this.selected_type == 2) {
            this.chartSeries[current_status_index].data[today.getDate() - 1]--;
            this.chartSeries[new_status_index].data[today.getDate() - 1]++;
            this.key++;
          } else if (this.selected_type == 3) {
            this.chartSeries[current_status_index].data[today.getMonth() - 1]--;
            this.chartSeries[new_status_index].data[today.getMonth() - 1]++;
            this.key++;
          }
        }
      }
    });
  },

  beforeDestroy() {
    this.$echo.leave("update.design-request");
  },
  methods: {
    next() {
      if (this.is_loading || this.count == 0) {
        return;
      }
      this.is_prev = "false";
      this.count++;
      if (this.selected_type == 0) {
        this.filterByDay("next");
      }
      if (this.selected_type == 1) {
        this.filterByWeek("next");
      }
      if (this.selected_type == 2) {
        this.filterByMonth("next");
      }
      if (this.selected_type == 3) {
        this.filterByYear("next");
      }
    },
    prev() {
      if (this.is_loading) {
        return;
      }
      this.count--;
      this.is_prev = "true";
      if (this.selected_type == 0) {
        this.filterByDay("prev");
      }
      if (this.selected_type == 1) {
        this.filterByWeek("prev");
      }
      if (this.selected_type == 2) {
        this.filterByMonth("prev");
      }
      if (this.selected_type == 3) {
        this.filterByYear("prev");
      }
    },
    async filterByMonth(next) {
      let number = next == "next" ? 1 : next == "manual" ? 0 : -1;
      this.changeDate.setFullYear(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth() + number,
        this.changeDate.getDate()
      );
      let date = this.changeDate.toISOString().slice(0, 10);
      this.date = date;
      await this.getData();
      this.key++;
    },
    async filterByYear(next) {
      let number = next == "next" ? 1 : next == "manual" ? 0 : -1;
      this.changeDate.setFullYear(
        this.changeDate.getFullYear() + number,
        this.changeDate.getMonth(),
        this.changeDate.getDate()
      );

      let date = this.changeDate.toISOString().slice(0, 10);
      this.date = date;
      await this.getData();
      this.key++;
    },

    async filterByWeek(next) {
      let number = next == "next" ? 7 : next == "manual" ? 0 : -7;
      this.changeDate.setFullYear(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth(),
        this.changeDate.getDate() + number
      );
      const prev_week_date = new Date();
      prev_week_date.setFullYear(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth(),
        this.changeDate.getDate() - 7
      );
      const next_week_date = new Date();
      next_week_date.setFullYear(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth(),
        this.changeDate.getDate() + 7
      );
      this.prev_week =
        this.day_name[prev_week_date.getDay()] +
        " " +
        prev_week_date.getDate() +
        " " +
        this.month_name2[prev_week_date.getMonth()];
      this.next_week =
        this.day_name[next_week_date.getDay()] +
        " " +
        next_week_date.getDate() +
        " " +
        this.month_name2[next_week_date.getMonth()];
      let date = this.changeDate.toISOString().slice(0, 10);
      this.date = date;
      await this.getData();
      this.key++;
    },

    async filterByDay(next) {
      let number = next == "next" ? 1 : next == "manual" ? 0 : -1;

      const d = this.changeDate;
      this.changeDate.setFullYear(
        d.getFullYear(),
        d.getMonth(),
        d.getDate() + number
      );
      let date = this.changeDate.toISOString().slice(0, 10);
      await this.getData();

      this.key++;
    },

    async getData() {
      try {
        this.is_loading = true;
        const response = await this.$axios.get("design-requests-chart", {
          params: {
            date: this.date,
            filter_type: this.filter_types[this.selected_type],
          },
        });
        this.is_loading = false;
        const data = response.data;
        this.chartSeries[0].data = data.waiting;
        this.chartSeries[1].data = data.inprogress;
        this.chartSeries[2].data = data.cancelled;
        this.chartSeries[3].data = data.completed;
        this.chartOptions.labels = data.labels;
        this.key++;
      } catch (error) {}
    },
  },
};
</script>

     <style>
.apexcharts-toolbar {
  z-index: 0 !important;
}
</style>
