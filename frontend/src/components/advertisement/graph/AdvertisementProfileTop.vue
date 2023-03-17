<template>
  <div
    class="
      w-full
      ps-2
      d-flex
      align-center
      justify-space-between
      white
      graph-header
    "
    style="height: 80px"
  >
    <div class="d-flex align-center">
      <v-btn fab small class="me-1 btn-background" @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-btn fab small class="btn-background" @click="$emit('fullScreen')">
        <v-icon>mdi-fullscreen</v-icon>
      </v-btn>
      <div class="ps-3">
        <v-avatar size="40" class="grey lighten-3 me-۱">
          <v-avatar size="35" color="white">
            <v-avatar size="30" color="white">
              <img
                :src="getSelectedInfo().avatar"
                alt=""
                v-if="['company', 'platform'].includes(current_tab)"
              />
              <FlagIcon
                v-else-if="['country'].includes(current_tab)"
                :flag="getSelectedInfo().avatar"
                :round="true"
              />
              <v-avatar v-else size="30" class="text-h6 primary white--text">
                {{ getSelectedInfo().avatar }}
              </v-avatar>
            </v-avatar>
          </v-avatar>
        </v-avatar>

        <span class="custom-title"> {{ getSelectedInfo().name }}</span>
      </div>
    </div>
    <div>
      <slot name="date_slot"></slot>
    </div>
  </div>
</template>

<script>
import FilterDateRange from "../FilterDateRange.vue";
import moment from "moment";
import FlagIcon from "../../common/FlagIcon.vue";

export default {
  props: {
    current_tab: {
      type: String,
      default: "",
    },
    selected_item: {
      type: Array,
      require: true,
    },
  },
  data() {
    return {
      filterByDate: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      refreshDates: "",
      showCustomDate: false,
      selectedDate: null,
      menu: 0,
      selectedDateIndex: 0,
      date_lists: [
        {
          id: "TODAY",
          text: "today",
          icon: "mdi-clock",
        },
        {
          id: "YESTERDAY",
          text: "yesterday",
          icon: "mdi-clock",
        },
        {
          id: "LAST_7_DAYS",
          text: "last_7_days",
          icon: "mdi-calendar",
        },
        {
          id: "LAST_30_DAYS",
          text: "last_30_days",
          icon: "mdi-calendar",
        },
        {
          id: "LIFETIME",
          text: "lifetime",
          icon: "mdi-calendar",
        },
      ],
    };
  },
  methods: {
    onDateRangeSubmit(range) {
      const timeRange = {
        start_date: range.start_date,
        end_date: range.end_date,
      };
      this.filterByDate = timeRange;

      this.$emit("dateChanged", this.filterByDate);
    },
    filterType(type) {
      this.$emit("TypeChanged", type);
    },
    getSelectedInfo() {
      if (this.selected_item.length == 0) return { avatar: "", name: "" };
      const item = this.selected_item[0];

      switch (this.current_tab) {
        case "country":
          return {
            name: item.name,
            avatar: item.iso2.toLowerCase(),
          };
        case "company":
          return {
            name: item.name,
            avatar: item.logo,
          };
        case "platform":
          return {
            name: item.platform_name,
            avatar: item.logo,
          };
        case "item_code":
          return {
            name: item.pname,
            avatar: item.pname?.charAt(0),
          };
        case "sales_type":
          return {
            name: "sales Type",
            avatar: "W",
          };

        case "ispp_code":
          return item.ispp_code;

        case "ad":
          return {
            name: item.ad_name,
            avatar: item.ad_name?.charAt(0),
          };
        default:
          return {
            name: item.name,
            avatar: item.name?.charAt(0),
          };
      }
      return [];
    },
  },
  components: { FilterDateRange, FlagIcon },
};
</script>

<style scoped>
.btn-background {
  background: #2e7be622 !important;
  color: var(--v-primary-base) !important;
}

.graph-header {
  border-radius: 30px 0 0 0;
}

.custom-title {
  font-size: 20px !important;
  font-weight: 500 !important;
  color: #777 !important;
}
</style>
