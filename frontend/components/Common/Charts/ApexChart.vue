<template>
  <client-only>
    <div>
      <v-card>
        <v-card-title
          class="pa-0 overflow-hidden"
          style="border-radius: 0px !important"
        >
          <slot />
          <v-row> </v-row>
        </v-card-title>
        <v-card-text>
          <apexchart
            type="area"
            height="350"
            :options="chartOptions"
            :series="series"
          ></apexchart>
          <v-row class="mx-1">
            <v-btn
              @click="$emit('prev')"
              color="primary"
              class="rounded-sm pe-2"
              ><v-icon>mdi-chevron-left</v-icon> Prev</v-btn
            >
            <v-spacer></v-spacer>
            <v-btn
              @click="$emit('next')"
              color="primary"
              class="rounded-sm ps-2"
              >Next <v-icon>mdi-chevron-right</v-icon></v-btn
            >
          </v-row>
        </v-card-text>
      </v-card>
    </div>
  </client-only>
</template>

<script>
import CustomButton from "../../design/components/CustomButton.vue";
import CustomCard from "../CustomCard.vue";
export default {
  components: { CustomCard, CustomButton },
  name: "ApexChart",
  props: {
    data: {
      type: Array,
      require: true,
    },
    categories: {
      type: Array,
      require: true,
    },
  },
  data() {
    return {
      today: new Date(),
      changeDate: new Date(),
      date: new Date().toISOString().substr(0, 10),
      menu: false,
      filter_type: "week",
      tabIndex: 0,
      filteredData: [],
      key: 0,

      menu: false,
      series: [
        {
          name: "Users",
          data: this.data,
        },
      ],

      chartOptions: {
        chart: {
          zoom: {
            enabled: true,
          },
        },

        fill: {
          type: "gradient",
          gradient: {
            enabled: true,
            opacityFrom: 0.75,
            opacityTo: 0,
          },
        },

        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
        },
        markers: {
          size: 5,
          colors: ["#fff"],
          strokeColors: "#008ffb",
          strokeWidth: 3,
        },
        //  title: {
        //   text: 'Product Trends by Month',
        //   align: 'left'
        // },
        grid: {
          row: {
            colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
            opacity: 0.5,
          },
        },
        xaxis: {
          categories: this.categories,
          tickAmount: 16,
        },
      },
    };
  },
  methods: {
    getDate() {
      this.menu = false;
      this.$emit("filterByMonth", this.date);
    },

    next() {
      if (this.filter_type == "week") {
        this.filterByWeek("next");
      }
      if (this.filter_type == "month") {
        this.filterByMonth("next");
      }
      if (this.filter_type == "year") {
        this.filterByYear("next");
      }
    },
    prev() {
      if (this.filter_type == "week") {
        this.filterByWeek("prev");
      }
      if (this.filter_type == "month") {
        this.filterByMonth("prev");
      }
      if (this.filter_type == "year") {
        this.filterByYear("prev");
      }
    },
    async filterByMonth(next) {
      let number = next == "next" ? 1 : next == "manual" ? 0 : -1;
      Date.UTC(2019, 5, 11);
      this.changeDate = new Date(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth() + number,
        this.changeDate.getDate()
      );
      let date = this.changeDate.toISOString().slice(0, 10);
      this.date = date;
      await this.monthFilter(date);
      this.key++;
    },
    async filterByYear(next) {
      let number = next == "next" ? 1 : next == "manual" ? 0 : -1;
      this.changeDate = new Date(
        this.changeDate.getFullYear() + number,
        this.changeDate.getMonth(),
        this.changeDate.getDate()
      );
      let date = this.changeDate.toISOString().slice(0, 10);
      await this.yearFilter(date);
      this.date = date;
      this.key++;
    },

    async filterByWeek(next) {
      let number = next == "next" ? 7 : next == "manual" ? 0 : -7;
      this.changeDate = new Date(
        this.changeDate.getFullYear(),
        this.changeDate.getMonth(),
        this.changeDate.getDate() + number
      );
      let date = this.changeDate.toISOString().slice(0, 10);
      this.date = date;
      await this.weekFilter(date);
      this.key++;
    },
  },
};
</script>

<style>
</style>
