<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('activities'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.subsystem"
          :items="subsystems"
          :type="`autocomplete`"
          :label="$tr('sub_system')"
        />
        <FilterInput
          v-model="form.event"
          :items="eventItems"
          :label="$tr('event')"
        />
        <FilterInput v-model="form.username" :label="$tr('username')" />
      </template>
      <template v-slot:date_range>
        <FilterInput
          @getTimeRange="
            (startTime, endTime) => {
              form.startTime = startTime;
              form.endTime = endTime;
            }
          "
          :clearInput.sync="clearInput"
          :type="`time-range`"
          :label="$tr('activity_time')"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.date"
          @getDate="getDate"
          :label="$tr('date')"
          :type="'date-range'"
        />
      </template>
      <template v-slot:id_filtering>
        <FilterInput
          :clearInput.sync="clearInput"
          @isInclude="(isInclude) => (form.include = isInclude)"
          @getIds="(ids) => (form.ids = ids)"
          :label="$tr('id')"
          :type="'id_filtering'"
        />
      </template>
    </CustomFilter>
  </div>
</template>

<script>
import FilterInput from "../design/components/FilterInput.vue";
import CustomFilter from "../common/CustomFilter.vue";
import { mapGetters, mapActions } from "vuex";

export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  props: {
    system: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      subsystems: [],
      eventItems: ["delete", "insert", "update", "block", "active"],
      form: {
        username: "",
        include: 1,
        ids: [],
        startTime: "",
        endTime: "",
        date: "",
        endDate: "",
        time: "",
        event: "",
        subsystem: "",
      },

      sortedData: {},
      clearInput: false,
    };
  },
  created() {
    switch (this.system) {
      case "users":
        this.subsystems = ["department", "user", "team"];
        break;
      case "masters":
        this.subsystems = ["country", "department"];
        break;
      case "landing":
        this.subsystems = [];
        break;
      default:
        break;
    }
  },
  computed: {
    ...mapGetters({
      countries: "countries/getAscCountries",
      companies: "companies/getCompanies",
      roles: "roles/getItems",
      teams: "teams/getTeams",
    }),
  },

  methods: {
    ...mapActions({
      getCountries: "countries/fetchAscCountries",
      getCompanies: "companies/fetchCompanies",
      getRoles: "roles/fetchItems",
      getTeams: "teams/fetchTeams",
    }),
    getDate(date, selected) {
  
        this.form.date = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.subsystem) this.sortedData.subsystem = this.form.subsystem;

      if (this.form.event) this.sortedData.event = this.form.event;

      if (this.form.username) this.sortedData.username = this.form.username;

      if (this.form.endTime) this.sortedData.endTime = this.form.endTime;

      if (this.form.startTime) this.sortedData.startTime = this.form.startTime;
      if (this.form.date)
        this.sortedData.date = this.form.date;

      if (this.form.ids.length > 0) {
        this.sortedData.ids = this.form.ids;
        this.sortedData.include = this.form.include;
      }
    },

    onSubmit() {
      if (!this.isAlreadySubmited()) {
        this.$emit("getRecord", this.sortedData);
        this.$emit("close");
      }
    },
    onClear() {
      (this.form = {
        username: "",
        include: 1,
        ids: [],
        startTime: "",
        endTime: "",
        startDate: "",
        endDate: "",
        time: "",
        event: "",
        subsystem: "",
      }),
        (this.clearInput = true);
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited()) this.$emit("getRecord", this.sortedData);
      this.sortedData = {};
    },

    isAlreadySubmited() {
      const obj1 = this.sortedData;
      this.sortData();
      const obj2 = this.sortedData;
      return JSON.stringify(obj1) === JSON.stringify(obj2);
    },
  },
};
</script>
