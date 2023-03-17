<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('storage_requests'))"
    >
      <template v-slot:data>
        <div>
          <FilterInput
            v-model="form.name"
            :type="`textfield`"
            :label="$tr('name')"
          />
        </div>
        <div>
          <FilterInput
            v-model="form.last_name"
            :type="`textfield`"
            :label="$tr('last_name')"
          />
        </div>
        <div>
          <FilterInput
            v-model="form.phone_number"
            :type="`textfield`"
            :label="$tr('phone_number')"
          />
        </div>
        <div>
          <FilterInput
            v-model="form.country_id"
            :type="`autocomplete`"
            :items="countries"
            :label="$tr('country_id')"
            item-value="id"
            item-text="name"
          />
        </div>
        <div>
          <FilterInput
            v-model="form.company_id"
            :type="`autocomplete`"
            :items="companies"
            :label="$tr('company_id')"
            item-value="id"
            item-text="name"
          />
        </div>
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
          :label="$tr(tabKey) + ' ' + $tr('id')"
          :type="'id_filtering'"
        />
      </template>
    </CustomFilter>
  </v-dialog>
</template>

<script>
import CustomFilter from "../../../common/CustomFilter.vue";
import FilterInput from "../../../design/components/FilterInput.vue";

export default {
  components: { CustomFilter, FilterInput },

  data() {
    return {
      tabKey: "",
      model: false,
      countries: [],
      companies: [],

      form: {
        name: "",
        last_name: "",
        phone_number: "",
        country_id: "",
        company_id: "",
        created_date:"",
        updated_date:"",
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      products: [],
    };
  },

  methods: {
    changeModel(item) {
      this.model = !this.model;
      this.tabKey = item;
      console.log("item", item);
      if (this.model) {
         this.fetchCountries();
      }
    },
    // fetch all users
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },

    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};

      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;
      if (this.form.last_name) this.sortedData.last_name = "like@@" + this.form.last_name;
      if (this.form.phone_number) this.sortedData.phone_number = "like@@" + this.form.phone_number;
      if (this.form.country_id)
      this.sortedData.country_id = [
          "whereHas",
          "country",
          this.form.country_id,
        ];
      if (this.form.company_id)
      this.sortedData.company_id = [
          "whereHas",
          "company",
          this.form.company_id,
        ];

      if (this.form.ids.length > 0) {
        this.sortedData.ids = this.form.ids;
        this.sortedData.include = this.form.include;
      }
      if (this.form.created_date)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_date
        );
        if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );
    },

    onSubmit() {
      if (!this.isAlreadySubmited()) {
        console.log("sort data", this.sortedData);
        this.$emit("filterRecords", this.sortedData);
        this.changeModel();
      }
    },
    onClear() {
      this.form = {
        name: "",
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
    async fetchCountries() {
      try {
        const url = `get-company-country-in-sourcer`;
        const { data } = await this.$axios.get(url);
        this.countries = data.country;
        this.companies = data.company;
        console.log(data);
      } catch (error) {}
    },
    async fetchCompanies() {
      try {
        if (this.companies.length > 0) {
          return;
        }

        const { data } = await this.$axios.get("online-sales/filter/companies");
        this.companies = data.items;
      } catch (error) {}
    },
  },
};
</script>
