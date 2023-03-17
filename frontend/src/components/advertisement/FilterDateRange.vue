<template>
  <v-menu
    ref="menu"
    v-model="menu"
    :offset-overflow="true"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="auto"
    :nudge-left="nudge_left"
  >
    <template v-slot:activator="{ on, attrs }" v-if="!custom_design">
      <CustomInput
        :label="hideTitle ? '' : $tr('date_range')"
        placeholder="pick_a_date"
        v-model="dateRangeText"
        readonly
        v-bind="attrs"
        append-icon="mdi-calendar-range"
        v-on="on"
        type="textfield"
        :hideDetails="true"
        class="date_range_input"
        style="margin-top: 2.4px"
      />
    </template>
    <template v-slot:activator="{ on, attrs }" v-else>
      <v-btn rounded outlined color="primary" v-bind="attrs" v-on="on">
        <v-icon class="primary--text me-1">mdi-calendar-range</v-icon>
        {{ $tr(selected_type) }}
      </v-btn>
    </template>
    <div class="d-flex white">
      <v-date-picker
        v-model="currentDate"
        range
        no-title
        scrollable
        :min="minDate"
        :max="maxDate"
        @change="onDateChanged"
        class="custom__filter__date__picker"
      >
      </v-date-picker>
      <div>
        <v-list dense>
          <v-list-item-group
            v-model="selectedDateIndex"
            mandatory
            color="primary"
          >
            <v-list-item
              link
              v-for="(item, i) in date_lists"
              :key="i"
              @click="onDateSelected(i)"
            >
              <v-list-item-title>
                {{ $tr(item.text) }}
              </v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </div>
    </div>
  </v-menu>
</template>

<script>
import moment from "moment";
import CustomInput from "../design/components/CustomInput.vue";

export default {
  props: {
    hideTitle: {
      type: Boolean,
      default: false,
    },
    dateRangeProp: {
      type: Object,
    },
    custom_design: {
      type: Boolean,
      default: false,
    },
    nudge_left: {
      type: Number,
      default: 0,
    },
    customSelectDate: {
      type: Object,
      default: () => ({ type: "today", index: 0 }),
    },
  },

  data() {
    return {
      x: 0,
      y: 0,
      selected_type: "today",
      menu: false,
      currentDate: [
        this.dateRangeProp
          ? this.dateRangeProp.start_date
          : moment().format("YYYY-MM-DD"),
        this.dateRangeProp
          ? this.dateRangeProp.end_date
          : moment().format("YYYY-MM-DD"),
      ],
      selectedDateIndex: 0,
      date_lists: [
        {
          id: "TODAY",
          text: "today",
        },
        {
          id: "YESTERDAY",
          text: "yesterday",
        },
        {
          id: "LAST_7_DAYS",
          text: "last_7_days",
        },
        {
          id: "LAST_30_DAYS",
          text: "last_30_days",
        },
        {
          id: "LAST_3_MONTHS",
          text: "last_3_months",
        },
        {
          id: "LIFETIME",
          text: "lifetime",
        },
      ],
    };
  },
  computed: {
    maxDate: function () {
      return moment().format("YYYY-MM-DD");
    },
    minDate: function () {
      return "2023-01-12";
    },
    dateRangeText() {
      return this.currentDate.join(" ~ ");
    },
  },
  methods: {
    clear() {
      this.currentDate = [moment().format("YYYY-MM-DD")];
      this.selectedDateIndex = 0;
    },
    onDateSelected(index) {
      const selectedDate = this.date_lists[index];
      switch (selectedDate.id) {
        case "TODAY":
          this.currentDate = [
            moment().format("YYYY-MM-DD"),
            moment().format("YYYY-MM-DD"),
          ];
          break;
        case "YESTERDAY":
          this.currentDate = [
            moment().subtract(1, "day").format("YYYY-MM-DD"),
            moment().subtract(1, "day").format("YYYY-MM-DD"),
          ];
          break;
        case "LAST_7_DAYS":
          this.currentDate = [
            moment().subtract(6, "day").format("YYYY-MM-DD"),
            moment().format("YYYY-MM-DD"),
          ];
          break;
        case "LAST_30_DAYS":
          this.currentDate = [
            moment().subtract(1, "months").add(1, "day").format("YYYY-MM-DD"),
            moment().subtract(0, "day").format("YYYY-MM-DD"),
          ];
          break;
        case "LAST_3_MONTHS":
          this.currentDate = [
            moment()
              .subtract(3, "months")
              .subtract(1, "day")
              .format("YYYY-MM-DD"),
            moment().subtract(0, "day").format("YYYY-MM-DD"),
          ];
          break;
        case "LIFETIME":
          this.currentDate = [this.minDate, this.maxDate];
          break;
        default:
          break;
      }
      this.menu = false;
      this.onDateChanged(this.date_lists[index].text);
    },
    onDateChanged(type = "custom") {
      if (this.currentDate.length == 1) {
        const newDate = { start_date: this.currentDate[0] };
        this.lastPickedDate = newDate;
        if (this.dateRangeProp) {
          if (this.dateRangeProp.start_date == newDate.start_date) {
            return;
          }
          this.dateRangeProp.start_date = newDate.start_date;
          this.dateRangeProp.end_date = newDate.start_date;
        }

        this.selected_type = type;
        this.$emit("filterType", type);

        this.$emit("dateChanged", newDate);
      } else if (this.currentDate.length == 2) {
        const newDate = {
          start_date: this.currentDate[0],
          end_date: this.currentDate[1],
        };
        this.lastPickedDate = newDate;
        if (this.dateRangeProp) {
          if (
            this.dateRangeProp.start_date == newDate.start_date &&
            this.dateRangeProp.end_date == newDate.end_date
          ) {
            return;
          }
          this.dateRangeProp.start_date = newDate.start_date;
          this.dateRangeProp.end_date = newDate.end_date;
        }
        this.$emit("filterType", type);

        this.selected_type = type;
        if (typeof type == "object") {
          this.selected_type =
            this.currentDate[0] + " ~ " + this.currentDate[1];
        }
        this.$emit("dateChanged", newDate);
      }
    },
  },
  created() {
    const custom = structuredClone(this.customSelectDate);
    this.selected_type = custom.type;
    this.selectedDateIndex = custom.index;
  },
  components: { CustomInput },
};
</script>

<style>
.custom__filter__date__picker .v-date-picker-table {
  height: 260px;
}

.date_range_input input {
  text-align: center;
  width: 210px;
}

.date_range_input .v-input__append-inner {
  margin-top: 8px !important;
}
</style>
