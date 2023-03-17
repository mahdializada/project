<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('users'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.country_id"
          @change="filterCompanies"
          :type="`autocomplete`"
          :items="countries.data"
          :label="$tr('country')"
        />
        <FilterInput
          v-model="form.current_country_id"
          :type="`autocomplete`"
          :items="countries.data"
          :label="$tr('current_country')"
        />
        <FilterInput
          v-model="form.team_id"
          :type="`autocomplete`"
          :items="teams"
          :label="$tr('team')"
        />
        <FilterInput
          v-model="form.role_id"
          :type="`autocomplete`"
          :items="roles"
          :label="$tr('role')"
        />
        <FilterInput
          v-model="form.company_id"
          :type="`autocomplete`"
          :items="companies"
          :loading="isFetchingCompany"
          :label="$tr('company')"
        />
        <FilterInput v-model="form.firstname" :label="$tr('firstname')" />
        <FilterInput v-model="form.lastname" :label="$tr('lastname')" />
        <FilterInput v-model="form.username" :label="$tr('username')" />
        <FilterInput v-model="form.address" :label="$tr('address')" />
      </template>
      <template v-slot:date_range>
        <FilterInput
          v-model="form.created_date"
          @getDate="getDate"
          :label="$tr('select_created_date')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.updated_date"
          @getDate="getDate"
          :label="$tr('last_edited')"
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
  data() {
    return {
      form: {
        country_id: "",
        current_country_id: "",
        team_id: "",
        role_id: "",
        company_id: "",
        created_date: null,
        updated_date: null,
        firstname: "",
        lastname: "",
        username: "",
        address: "",
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      isFetchingCompany: false,
    };
  },
  created() {
    if (this.countries?.length == 0) {
      this.getCountries({key: 'all'});
    }
    if (this.companies?.length == 0) {
      this.getCompanies({key: 'all'});
    }
    if (this.teams?.length == 0) {
      this.getTeams({key: 'all'});
    }
    if (this.roles?.length == 0) {
      this.getRoles({key: 'all'});
    }
  },
  computed: {
    ...mapGetters({
      countries: "departments/countries",
      companies: "departments/companies",
      roles: "roles/getItems",
      teams: "teams/getTeams",
    }),
  },

  methods: {
    ...mapActions({
      getCountries: "departments/fetchCountries",
      getCompanies: "departments/fetchCompanies",
      fetchCompaniesOfCountries: "departments/fetchCompaniesOfCountries",
      getRoles: "roles/fetchItems",
      getTeams: "teams/fetchTeams",
    }),
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.country_id)
        this.sortedData.country_id = "exact@@" + this.form.country_id;

      if (this.form.current_country_id)
        this.sortedData.current_country_id =
          "exact@@" + this.form.current_country_id;

      if (this.form.team_id)
        this.sortedData.team_id = ['whereHas', 'teams', this.form.team_id]

      if (this.form.role_id)
         this.sortedData.role_id = ['whereHas', 'roles', this.form.role_id]


      if (this.form.company_id)
        this.sortedData.company_id = ['whereHas', 'companies', this.form.company_id]

      if (this.form.firstname)
        this.sortedData.firstname = "like@@" + this.form.firstname;

      if (this.form.lastname)
        this.sortedData.lastname = "like@@" + this.form.lastname;

      if (this.form.username)
        this.sortedData.username = "like@@" + this.form.username;

      if (this.form.address)
        this.sortedData.address = "like@@" + this.form.address;

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
      (this.form = {
        country_id: "",
        current_country_id: "",
        team_id: "",
        role_id: "",
        company_id: "",
        created_date: null,
        updated_date: null,
        firstname: "",
        lastname: "",
        username: "",
        address: "",
        include: 1,
        ids: [],
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
    async filterCompanies() {
      this.isFetchingCompany = true;
      await this.fetchCompaniesOfCountries([this.form.country_id]);
      this.isFetchingCompany = false;
    },
  },
};
</script>
