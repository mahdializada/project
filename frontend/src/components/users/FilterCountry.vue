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
            :title="'add_plus'"
            :class="`mt-md-4 mb-2 ${topMinus ? 'top-minus-4' : ''}`"
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
            logoName=""
            has-logo
            type="autocomplete"
            class="me-md-4 mb-md-2"
            ref="projectInput"
            :loading="isFetchingCompany"
            @change="onCompanyChange"
            :error-messages="companyErrorMessage"
          />
          <FormBtn
            @click="addCompanyAndDepartment"
            :title="'add_plus'"
            :class="`mt-md-4 mb-2 d-md-none ${topMinus ? 'top-minus-4' : ''}`"
            v-if="!isDepartment"
          />
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="selectedCompany in selectedCompanies"
              :key="selectedCompany.id"
              @close="() => removeSelectedCompany(selectedCompany.id)"
              :title="selectedCompany.name"
              logoName=""
              :logo-url="selectedCompany.logo"
            />
          </div>
        </div>
        <div class="w-full" v-if="!isDepartment">
          <CustomInput
            :items="departments"
            v-model="selectedDepartmentId"
            :label="$tr('label_required', $tr('department'))"
            :placeholder="$tr('choose_item', $tr('department'))"
            item-value="id"
            item-text="name"
            has-logo
            type="autocomplete"
            class="me-md-4 mb-md-2"
            ref="departmentInput"
            :loading="isFetchingDepartment"
            :error-messages="departmentErrorMessage"
          />

          <FormBtn
            @click="addCompanyAndDepartment"
            :title="'add_plus'"
            class="mt-md-4 mb-2 d-md-none"
          />
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="selectedDepartment in selectedDepartments"
              :key="selectedDepartment.id"
              @close="() => removeSelectedDepartment(selectedDepartment.id)"
              :title="selectedDepartment.name"
              logo-url="logo"
            />
          </div>
        </div>
        <div :class="`w-100 ${showCompanyBtn ? '' : 'd-none'} d-md-block`">
          <FormBtn
            @click="addCompanyAndDepartment"
            :title="'add_plus'"
            :class="`${showCompanyBtn ? 'mt-md-4' : 'mt-4'} ${
              topMinus ? 'top-minus-4' : ''
            }`"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FormBtn from "../design/components/FormBtn";
import SelectedItem from "../design/components/SelectedItem";
import CustomInput from "../design/components/CustomInput";
import { mapActions, mapGetters, mapMutations } from "vuex";

export default {
  name: "FilterCountry",
  components: { CustomInput, SelectedItem, FormBtn },
  props: {
    topMinus: {
      type: Boolean,
    },
    skipDepartment: {
      type: Boolean,
      default: false,
    },
    skipCountry: {
      type: Boolean,
      default: false,
    },

    skipCompany: {
      type: Boolean,
      default: false,
    },

    countryProps: {
      type: Array,
      default: () => [],
    },

    companyProps: {
      type: Array,
      default: () => [],
    },

    departmentProps: {
      type: Array,
      default: () => [],
    },

    isDepartment: {
      type: Boolean,
      default: false,
    },
    loadCountries: {
      type: Boolean,
      default: true,
    },
    showCompanyBtn: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      // selected necessary
      selectedCountries: this.countryProps,
      selectedCountryId: "",
      countryErrorMessage: "",

      selectedDepartments: this.departmentProps,
      selectedDepartmentId: "",
      departmentErrorMessage: "",
      isFetchingDepartment: false,

      selectedCompanies: this.companyProps,
      selectedCompanyId: "",
      companyErrorMessage: "",
      isFetchingCompany: false,
    };
  },

  computed: {
    ...mapGetters({
      countries: "filterData/getCountriesWithCompanies",
      companies: "filterData/getCompaniesOfTheCountries",
      departments: "filterData/getDepartmentsOfTheCompanies",
    }),
  },

  created() {
    if (this.loadCountries) {
      this.fetchCountries();
    }
    if (this.companyProps.length && !this.isDepartment) {
      this.fetchDepartments({
        company_ids: this.selectedCompanies?.map((company) => company?.id),
        selected_departments: this.selectedDepartments.map((d) => d.id),
      });
    }
    if (this.countryProps.length) {
      this.fetchCompanies({
        country_ids: this.selectedCountries?.map((country) => country?.id),
        selected_company_ids: this.selectedCompanies.map((c) => c.id),
      });
    }
  },

  methods: {
    ...mapActions({
      fetchCountries: "filterData/fetchCountriesWithCompanies",
      fetchCompanies: "filterData/fetchCompaniesOfCountries",
      fetchDepartments: "filterData/fetchDepartmentsOfCompanies",
    }),
    ...mapMutations({
      filterSelectedCountry: "filterData/FILTER_SELECTED_COUNTRY",
      insertSelectedCountry: "filterData/INSERT_SELECTED_COUNTRY",
      filterSelectedCompany: "filterData/FILTER_SELECTED_COMPANY",
      insertSelectedCompany: "filterData/INSERT_SELECTED_COMPANY",
      filterSelectedDepartment: "filterData/FILTER_SELECTED_DEPARTMENT",
      insertSelectedDepartment: "filterData/INSERT_SELECTED_DEPARTMENT",
    }),
    // check validations
    checkValidation() {
      // validate country
      const CountryRequiredText = this.$tr(
        "item_is_required",
        this.$tr("country")
      );
      this.selectedCountries?.length < 1
        ? (this.countryErrorMessage = CountryRequiredText)
        : (this.countryErrorMessage = "");

      // validate company
      const companyRequiredText = this.$tr(
        "item_is_required",
        this.$tr("company")
      );
      this.selectedCompanies?.length < 1
        ? (this.companyErrorMessage = companyRequiredText)
        : (this.companyErrorMessage = "");

      // validate department
      const departmentRequiredText = this.$tr(
        "item_is_required",
        this.$tr("department")
      );

      this.selectedDepartments?.length < 1
        ? (this.departmentErrorMessage = departmentRequiredText)
        : (this.departmentErrorMessage = "");

      // check  validations
      let isDepartmentInvalid = this.selectedDepartments.length < 1;
      let isCountryInvalid = this.selectedCountries.length < 1;
      let isCompanyInvalid = this.selectedCompanies.length < 1;

      if (this.skipDepartment) {
        isDepartmentInvalid = false;
      }

      if (this.skipCountry) {
        isCountryInvalid = false;
      }

      if (this.skipCompany) {
        isCompanyInvalid = false;
      }

      // validate general
      const isGenerateValid =
        isDepartmentInvalid || isCountryInvalid || isCompanyInvalid;

      return !isGenerateValid;
    },

    // add new country to selected countries
    async addSelectedCountry() {
      const requiredText = this.$tr("item_is_required", this.$tr("country"));
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
        country.index = this.countries.findIndex(
          (c) => c.id == this.selectedCountryId
        );
        this.isFetchingCompany = true;
        this.selectedCountries?.unshift(country);
        this.filterSelectedCountry(this.selectedCountryId);
        this.selectedCountryId = "";
        await this.fetchCompanies({
          country_ids: this.selectedCountries?.map((country) => country?.id),
          selected_company_ids: this.selectedCompanies.map((c) => c.id),
        });
        this.selectedCountries?.length < 1
          ? (this.countryErrorMessage = requiredText)
          : (this.countryErrorMessage = "");
        this.isFetchingCompany = false;
      } else {
        this.countryErrorMessage = requiredText;
      }
    },
    removeSelectedDepartments() {
      this.selectedDepartments = this.selectedDepartments.filter(
        (department) => {
          const isExists = department.companies.every((com) => {
            return this.selectedCompanies.map((con) => con.id).includes(com.id);
          });
          if (isExists) {
            return department;
          }
        }
      );
    },
    // remove selected countries
    async removeSelectedCountry(countryId) {
      try {
        const selectedCountry = this.selectedCountries.find(
          (c) => c.id == countryId
        );
        const length = this.selectedCountries.length;
        this.insertSelectedCountry({ selectedCountry, length });
        this.selectedCountries = this.selectedCountries?.filter(
          (country) => country?.id !== countryId
        );
        if (this.selectedCountries.length !== 0) {
          this.selectedCompanies = this.selectedCompanies?.filter((company) => {
            if (!company?.countries?.map((c) => c.id)?.includes(countryId)) {
              return company;
            }
          });

          await this.fetchCompanies({
            country_ids: this.selectedCountries?.map((country) => country?.id),
            selected_company_ids: this.selectedCompanies?.map((c) => c.id),
          });

          if (!this.isDepartment) {
            this.removeSelectedDepartments();
            const company_ids = this.selectedCompanies?.map(
              (company) => company?.id
            );
            await this.fetchDepartments({
              company_ids: company_ids ?? [],
              selected_departments: this.selectedDepartments.map((d) => d.id),
            });
          }
        } else {
          this.$store.commit("filterData/REMOVE_ALL_COMPANIES");
          this.selectedCompanies = [];
          if (!this.isDepartment) {
            this.$store.commit("filterData/REMOVE_ALL_DEPARTMENTS");
            this.selectedDepartments = [];
          }
        }

        const requiredText = this.$tr("item_is_required", this.$tr("country"));

        this.selectedCountries?.length < 1
          ? (this.countryErrorMessage = requiredText)
          : (this.countryErrorMessage = "");
      } catch (error) {}
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
      if (this.selectedDepartmentId && !this.isDepartment) {
        this.addSelectedDepartment();
      }
    },

    // add new department to selected departments
    addSelectedDepartment() {
      const requiredText = this.$tr("item_is_required", this.$tr("department"));
      const CompanyRequiredText = this.$tr(
        "item_is_required",
        this.$tr("company")
      );
      this.selectedCompanies?.length > 0
        ? (this.companyErrorMessage = "")
        : (this.companyErrorMessage = CompanyRequiredText);

      if (this.selectedDepartmentId) {
        const department = this.departments?.find(
          (department) => department?.id === this.selectedDepartmentId
        );
        if (
          this.selectedDepartments?.some(
            (departmentItem) => departmentItem?.id === department?.id
          )
        ) {
          return;
        }

        this.selectedDepartments?.unshift(department);
        this.$emit("onDepartmentChange", this.selectedDepartments);
        this.filterSelectedDepartment(this.selectedDepartmentId);
        this.selectedDepartmentId = "";
        this.selectedDepartments?.length < 1
          ? (this.departmentErrorMessage = requiredText)
          : (this.departmentErrorMessage = "");
      } else if (this.selectedDepartments?.length < 1) {
        this.departmentErrorMessage = requiredText;
      }
    },

    async onCompanyChange(selectedId) {
      if (!this.isDepartment) {
        this.isFetchingDepartment = true;
        const selectedIds = this.selectedCompanies.map((company) => company.id);
        selectedIds.push(selectedId);
        await this.fetchDepartments({
          company_ids: selectedIds,
          selected_departments: this.selectedDepartments.map((d) => d.id),
        });
        this.isFetchingDepartment = false;
      }
    },

    // add new department to selected companies
    async addSelectedCompany() {
      const CompanyRequiredText = this.$tr(
        "item_is_required",
        this.$tr("company")
      );

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
        this.filterSelectedCompany(this.selectedCompanyId);
        this.selectedCompanyId = "";
        this.selectedCompanies?.length < 1
          ? (this.companyErrorMessage = CompanyRequiredText)
          : (this.companyErrorMessage = "");
      } else if (this.selectedCompanies?.length < 1) {
        this.companyErrorMessage = CompanyRequiredText;
      }
    },

    // remove selected company
    async removeSelectedCompany(companyId) {
      const selectedCompany = this.selectedCompanies.find(
        (c) => c.id == companyId
      );
      const length = this.selectedCompanies.length;
      this.insertSelectedCompany({ selectedCompany, length });
      this.selectedCompanies = this.selectedCompanies?.filter(
        (company) => company?.id !== companyId
      );
      if (this.selectedCompanies.length !== 0 && !this.isDepartment) {
        await this.fetchDepartments({
          company_ids: this.selectedCompanies?.map((company) => company?.id),
          selected_departments: this.selectedDepartments.map((d) => d.id),
        });
        this.selectedDepartments = this.selectedDepartments.filter(
          (department) => {
            if (!department.companies.map((c) => c.id).includes(companyId)) {
              return department;
            }
          }
        );
      } else {
        if (!this.isDepartment) {
          this.$store.commit("filterData/REMOVE_ALL_DEPARTMENTS");
          this.selectedDepartments = [];
        }
      }
      const requiredText = this.$tr("item_is_required", this.$tr("company"));
      this.selectedCompanies?.length < 1
        ? (this.companyErrorMessage = requiredText)
        : (this.companyErrorMessage = "");
    },

    // remove selected department
    removeSelectedDepartment(departmentId) {
      const selectedDepartment = this.selectedDepartments.find(
        (c) => c.id == departmentId
      );
      const length = this.selectedDepartments.length;
      this.insertSelectedDepartment({ selectedDepartment, length });
      this.selectedDepartments = this.selectedDepartments?.filter(
        (department) => department?.id !== departmentId
      );
      const requiredText = this.$tr("item_is_required", this.$tr("department"));
      this.selectedDepartments?.length < 1
        ? (this.departmentErrorMessage = requiredText)
        : (this.departmentErrorMessage = "");
    },

    // clear all selected items and also error messages
    clearItems() {
      this.$store.commit("filterData/REMOVE_ALL_COMPANIES");
      if (!this.isDepartment) {
        this.$store.commit("filterData/REMOVE_ALL_DEPARTMENTS");
      }

      // selected necessary
      this.selectedCountries = [];
      this.selectedCountryId = "";
      this.countryErrorMessage = "";

      this.selectedDepartments = [];
      this.selectedDepartmentId = "";
      this.departmentErrorMessage = "";

      this.selectedCompanies = [];
      this.selectedCompanyId = "";
      this.companyErrorMessage = "";
    },
  },
};
</script>

<style scoped></style>
