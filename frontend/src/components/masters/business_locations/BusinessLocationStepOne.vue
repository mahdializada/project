<template>
  <div>
    <div class="countries">
      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="countries"
            v-model="selectedCountryId"
            item-value="id"
            item-text="name"
            :label="$tr('label_required', $tr('country'))"
            :placeholder="$tr('choose_item', $tr('country'))"
            country
            ref="countryInput"
            type="autocomplete"
            class="me-md-4 mb-md-2 mb-0"
            :error-messages="countryErrorMessage"
          />
        </div>
        <div class="w-100">
          <FormBtn
            @click="addSelectedCountry"
            :title="$tr('add_plus')"
            class="mt-md-4 mb-2 top-minus-4"
          />
        </div>
      </div>
      <div class="selected d-flex flex-wrap">
        <SelectedItem
          v-for="selectedCountry in selectedCountries"
          :key="selectedCountry.id"
          @close="() => removeSelectedCountry(selectedCountry.id)"
          :title="selectedCountry.name"
          :flag="`${selectedCountry.iso2.toLowerCase()}`"
        />
      </div>
    </div>
    <div class="companies and Departments">
      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="companies"
            v-model="selectedCompanyId"
            :label="$tr('label_required', $tr('company'))"
            :placeholder="$tr('choose_item', $tr('company'))"
            item-value="id"
            item-text="name"
            logo-name=""
            has-logo
            type="autocomplete"
            class="me-md-4 mb-md-2"
            ref="projectInput"
            :loading="isFetchingCompany"
            :error-messages="companyErrorMessage"
          />
        </div>
        <div class="w-100">
          <FormBtn
            @click="addCompanyAndDepartment"
            :title="$tr('add_plus')"
            class="mt-md-4 mb-2 top-minus-4"
          />
        </div>
      </div>
      <div class="selected d-flex flex-wrap">
        <SelectedItem
          v-for="selectedCompany in selectedCompanies"
          :key="selectedCompany.id"
          @close="() => removeSelectedCompany(selectedCompany.id)"
          :title="selectedCompany.name"
          logo-name=""
          :logo-url="selectedCompany.logo"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FormBtn from '../../design/components/FormBtn';
import SelectedItem from '../../design/components/SelectedItem';
import CustomInput from '../../design/components/CustomInput';
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'BusinessLocationStepOne',
  components: { CustomInput, SelectedItem, FormBtn },
  props: {
    countryProps: {
      type: Array,
      default: () => [],
    },

    companyProps: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      // selected necessary
      selectedCountries: this.countryProps,
      selectedCountryId: '',
      countryErrorMessage: '',

      selectedCompanies: this.companyProps,
      selectedCompanyId: '',
      companyErrorMessage: '',
      isFetchingCompany: false,
    };
  },

  computed: {
    ...mapGetters({
      countries: 'filterData/getCountriesWithCompanies',
      companies: 'filterData/getCompaniesOfTheCountries',
    }),
  },

  created() {
    this.fetchCountries();
    if (this.countryProps.length) {
      this.fetchCompanies(this.countryProps.map((country) => country.id));
    }
  },

  methods: {
    ...mapActions({
      fetchCountries: 'filterData/fetchCountriesWithCompanies',
      fetchCompanies: 'filterData/fetchCompaniesOfCountries',
    }),

    // check validations
    checkValidations() {
      // validate country
      const CountryRequiredText = this.$tr('item_is_required', [
        this.$tr('country'),
      ]);
      this.selectedCountries?.length < 1
        ? (this.countryErrorMessage = CountryRequiredText)
        : (this.countryErrorMessage = '');

      // validate company
      const companyRequiredText = this.$tr('item_is_required', [
        this.$tr('company'),
      ]);
      this.selectedCompanies?.length < 1
        ? (this.companyErrorMessage = companyRequiredText)
        : (this.companyErrorMessage = '');

      // check  validations
      let isCountryInvalid = this.selectedCountries.length < 1;
      let isCompanyInvalid = this.selectedCompanies.length < 1;

      // validate general
      const isGenerateValid = isCountryInvalid || isCompanyInvalid;

      return !isGenerateValid;
    },

    // add new country to selected countries
    async addSelectedCountry() {
      const requiredText = this.$tr('item_is_required', [this.$tr('country')]);
      if (this.selectedCountryId) {
        const country = this.countries?.find(
          (country) => country?.id === this.selectedCountryId
        );
        if (!country) {
          return;
        }
        if (
          this.selectedCountries?.some(
            (countryItem) => countryItem?.id === country?.id
          )
        ) {
          return;
        }
        this.isFetchingCompany = true;
        this.selectedCountries?.unshift(country);
        this.selectedCountryId = '';
        await this.fetchCompanies(
          this.selectedCountries?.map((country) => country?.id)
        );
        this.selectedCountries?.length < 1
          ? (this.countryErrorMessage = requiredText)
          : (this.countryErrorMessage = '');
        this.isFetchingCompany = false;
      } else {
        this.countryErrorMessage = requiredText;
      }
    },

    // remove selected countries
    async removeSelectedCountry(countryId) {
      this.selectedCountries = this.selectedCountries?.filter(
        (country) => country?.id !== countryId
      );
      if (this.selectedCountries.length !== 0) {
        await this.fetchCompanies(
          this.selectedCountries?.map((country) => country?.id)
        );
        this.selectedCompanies = this.selectedCompanies.filter((company) => {
          if (this.isInArray(this.companies, company)) {
            return company;
          }
        });
      } else {
        this.$store.commit('filterData/REMOVE_ALL_COMPANIES');
        this.selectedCompanies = [];
      }

      const requiredText = this.$tr('item_is_required', [this.$tr('country')]);

      this.selectedCountries?.length < 1
        ? (this.countryErrorMessage = requiredText)
        : (this.countryErrorMessage = '');
    },

    // search an item
    isInArray(arr, search) {
      let res = arr.find((item) => {
        return item.id === search.id;
      });
      return res !== undefined;
    },

    // add company and department
    addCompanyAndDepartment(id) {
      if (this.selectedCompanyId) {
        this.addSelectedCompany(id);
      }
    },

    // add new department to selected companies
    async addSelectedCompany() {
      if (this.selectedCompanies.length) {
        return;
      }
      const CompanyRequiredText = this.$tr('item_is_required', [
        this.$tr('company'),
      ]);

      if (this.selectedCompanyId) {
        const company = this.companies?.find(
          (company) => company?.id === this.selectedCompanyId
        );
        if (!company) {
          this.companyErrorMessage = CompanyRequiredText;
          return;
        }
        if (
          this.selectedCompanies?.some(
            (companyItem) => companyItem?.id === company?.id
          )
        ) {
          return;
        }
        this.selectedCompanies?.unshift(company);
        this.selectedCompanyId = '';
        this.selectedCompanies?.length < 1
          ? (this.companyErrorMessage = CompanyRequiredText)
          : (this.companyErrorMessage = '');
      } else if (this.selectedCompanies?.length < 1) {
        this.companyErrorMessage = CompanyRequiredText;
      }
    },

    // remove selected company
    async removeSelectedCompany(companyId) {
      this.selectedCompanies = this.selectedCompanies?.filter(
        (company) => company?.id !== companyId
      );

      const requiredText = this.$tr('item_is_required', [this.$tr('company')]);
      this.selectedCompanies?.length < 1
        ? (this.companyErrorMessage = requiredText)
        : (this.companyErrorMessage = '');
    },
    // clear all selected items and also error messages
    clearItems() {
      this.$store.commit('filterData/REMOVE_ALL_COMPANIES');

      // selected necessary
      this.selectedCountries = [];
      this.selectedCountryId = '';
      this.countryErrorMessage = '';

      this.selectedCompanies = [];
      this.selectedCompanyId = '';
      this.companyErrorMessage = '';
    },
  },
};
</script>

<style scoped></style>
