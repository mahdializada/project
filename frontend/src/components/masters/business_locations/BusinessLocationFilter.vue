<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_business_locations')"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.country_id"
          :label="$tr('country')"
          :type="`autocomplete`"
          :items="countries.data"
          @change="selectCountry"
        />
        <FilterInput v-model="form.company_id" :label='$tr("company")' :type="`autocomplete`"
          :items="companies" :loading="isFetchingCompany" /> 
        <FilterInput v-model="form.name" :label='$tr("name") ' />
        
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
  </div>
</template>

<script>
import FilterInput from "../../design/components/FilterInput.vue";
import CustomFilter from "../../common/CustomFilter.vue";
import { mapGetters, mapActions } from "vuex";
export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  data() {
    return {
       form: {
        country_id: "",
        company_id: "",
        created_date: null,
        updated_date: null,
        name: "",
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      isFetchingCompany: false,
    };
  },
  computed: {
    ...mapGetters({
      countries: "departments/countries",
      companies: "departments/companies",
    }),
  },
  created() {
    if (this.countries?.length == 0) {
      this.getCountries();
    }
    if (this.companies?.length == 0) {
      this.getCompanies();
    }
  },
  methods: {
    ...mapActions({
      getCountries: "departments/fetchCountries",
      getCompanies: "departments/fetchCompanies",
      fetchCompaniesOfCountries: "departments/fetchCompaniesOfCountries",
    }),
    async selectCountry() {
      this.isFetchingCompany = true;
      await this.fetchCompaniesOfCountries([this.form.country_id]);
      this.isFetchingCompany = false;
    },
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },

    sortData() {
    
      this.sortedData = {};
      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;

      if (this.form.country_id)
        this.sortedData.country_id = "exact@@" + this.form.country_id;
      if (this.form.company_id)
        this.sortedData.company_id = "exact@@" + this.form.company_id; 
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
        this.$emit("getRecord", this.sortedData);
        this.$emit("close");
      }
    },
    onClear() {
      this.form = {
        country_id: "",
        company_id: "",
        created_date: null,
        updated_date: null,
        name: "",
        include: 1,
        ids: [],
      },
      this.clearInput = true;
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
