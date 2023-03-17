<template>
  <v-autocomplete
    :class="`custom-input auto-complete small`"
    autocomplete="no"
    allow-overflow
    ref="rangePickerMenu"
    :items="[]"
    dense
    :placeholder="rangePlaceholder"
    :menu-props="{ bottom: true, offsetY: true, maxHeight: 500 }"
    outlined
    v-bind="$attrs"
    v-on="$listeners"
  >
    <template v-slot:no-data>
      <div>
        <v-row justify="space-around" class="px-2" v-if="type === 'date'">
          <v-date-picker v-model="startRange" :max="endRange" no-title>
          </v-date-picker>
          <v-date-picker
            v-model="endRange"
            :min="startRange"
            :show-current="currentDate(2)"
            no-title
          ></v-date-picker>
        </v-row>

        <v-row
          justify="space-around"
          class="px-3 mb-2"
          v-else-if="type === 'time'"
        >
          <v-time-picker v-model="startRange" :max="endRange" format="ampm">
          </v-time-picker>
          <v-time-picker
            v-model="endRange"
            :min="startRange"
            format="ampm"
          ></v-time-picker>
        </v-row>

        <div class="d-flex">
          <v-spacer />
          <v-btn
            @click="onCancelClicked"
            text
            small
            color="primary"
            class="stepper-btn px-2"
          >
            {{ $tr("cancel") }}
          </v-btn>
          <v-btn
            @click="onOkayClicked"
            small
            color="primary"
            class="stepper-btn px-3 me-1"
            :type="'button'"
          >
            {{ $tr("ok") }}
          </v-btn>
        </div>
      </div>
    </template>
  </v-autocomplete>
</template>

<script>
import Helper from "../../helpers/helpers";

export default {
  name: "RangePicker",

  props: {
    type: {
      type: String,
      default: "date",
    },
    placeholder: {
      type: String,
    },
  },
  watch: {
    placeholder: function (value) {
      this.rangePlaceholder = value;
    },
  },
  data() {
    const to = this.$tr("to");
    const defaultTimeRange = `10:00 am ${to} 9:00 pm`;
    const startDate = this.currentDate();
    const endDate = this.currentDate(2);
    const defaultDateRange = `${startDate} ${to} ${endDate}`;

    const defaultPlaceholder =
      this.type === "time" ? defaultTimeRange : defaultDateRange;

    return {
      timePickerDialog: false,
      datePickerDialog: false,
      rangePlaceholder: this.placeholder
        ? this.placeholder
        : defaultPlaceholder,
      startRange: "",
      endRange: "",
    };
  },

  methods: {
    // fired on cancel button clicked
    onCancelClicked() {
      this.$refs.rangePickerMenu.blur();
    },

    // set range date or time
    setRangeDate(startRange, endRange) {
      this.startRange = startRange;
      this.endRange = endRange;
    },

    // return selected range data
    getRangeData() {
      if (this.type === "time") {
        const startTime = Helper.convertTime(this, this.startRange);
        const endTime = Helper.convertTime(this, this.endRange);
        const data = {
          type: this.type,
          startRange: startTime,
          endRange: endTime,
        };
        return data;
      }

      const data = {
        type: this.type,
        startRange: this.startRange,
        endRange: this.endRange,
      };

      return data;
    },

    // fired on okay button event
    onOkayClicked() {
      this.$refs.rangePickerMenu.blur();
      if (this.type === "date") {
        if (this.startRange && this.endRange) {
          const to = this.$tr("to");
          this.rangePlaceholder = `${this.startRange} ${to} ${this.endRange}`;
        }
      } else {
        if (this.startRange && this.endRange) {
          const to = this.$tr("to");
          const startTime = Helper.convertTime(this, this.startRange);
          const endTime = Helper.convertTime(this, this.endRange);
          this.rangePlaceholder = `${startTime} ${to} ${endTime}`;
        }
      }

      if (this.startRange && this.endRange) {
        this.$emit("getRangeData", this.getRangeData());
      }
    },

    // return current date + 1 month
    currentDate(defaultMonth = 1) {
      const date = new Date();

      let year = date.getFullYear();
      let month = date.getMonth() + defaultMonth;

      if (month < 10) {
        month = "0" + month;
      }
      let parsedData = `${year}-${month}-01`;
      return parsedData;
    },
  },
};
</script>
<style scoped>
.stepper-btn {
  font-size: 14px;
  height: 32px !important;
  font-weight: 400;
  border-radius: 4px;
}
</style>
