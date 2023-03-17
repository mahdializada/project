<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('platforms'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.company_id"
          :type="`autocomplete`"
          :items="companies"
          :label="$tr('company')"
          item-text="name"
          has-avatar
        />

        <FilterInput
          v-model="form.platform_name"
          :type="`autocomplete`"
          :items="platformNames"
          :label="$tr('platform_name')"
          item-text="name"
          itemValue="name"
          has-avatar
        />
      </template>
      <template v-slot:date_range>
        <FilterInput
          v-model="form.created_date"
          @getDate="getDate"
          :label="$tr('created_at')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.updated_date"
          @getDate="getDate"
          :label="$tr('updated_at')"
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
  </v-dialog>
</template>

<script>
import FilterInput from "~/components/design/components/FilterInput.vue";
import CustomFilter from "~/components/common/CustomFilter.vue";
export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  data() {
    return {
      model: false,
      platformNames: [],
      companies: [],
      form: {
        company_id: "",
        platform_name: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
    };
  },

  methods: {
    changeModel() {
      this.model = !this.model;
      if (this.model) {
        this.fetchPlatformNames();
        this.fetchCompanies();
      }
    },

    async fetchCompanies() {
      try {
        if (this.companies.length > 0) {
          return;
        }
        const url = `advertisement/platforms/fetch?companies=true`;
        const { data } = await this.$axios.get(url);
        this.companies = data;
      } catch (_) {}
    },

    async fetchPlatformNames() {
      try {
        if (this.platformNames.length > 0) {
          return;
        }
        const url = `advertisement/platform_names`;
        const { data } = await this.$axios.get(url);
        this.platformNames = data;
      } catch (_) {}
    },

    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
      else if (selected.toLowerCase().includes("arrival_time"))
        this.form.arrival_time_date = date;
    },

    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};
      if (this.form.company_id)
        this.sortedData.company_id = [
          "whereHas",
          "company",
          this.form.company_id,
        ];

      if (this.form.platform_name)
        this.sortedData.platform_name = "like@@" + this.form.platform_name;

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );

      if (this.form.created_date)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_date
        );

      if (this.form.ids.length > 0) {
        this.sortedData.ids = this.form.ids;
        this.sortedData.include = this.form.include;
      }
    },

    onSubmit() {
      if (!this.isAlreadySubmited()) {
        this.$emit("filterRecords", this.sortedData);
        this.changeModel();
      }
    },
    onClear() {
      this.form = {
        company_id: "",
        platform_name: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      };
      this.clearInput = true;
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited()) {
        this.$emit("filterRecords", this.sortedData);
        this.changeModel();
      }
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
