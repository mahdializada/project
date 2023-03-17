<template>
  <div class="">
    <CTitle :text="'general_info'" />
    <div class="w-full d-flex">
      <InputCard
        :title="this.$tr('Country')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCountry = v)"
      >
        <NoCheckboxItemContainer
          v-model="form.country_id.$model"
          :items="filterCountry"
          :loading="isFetchingCountry"
          :no_data="filterCountry.length < 1 && !isFetchingCountry"
          :rules="
            validateRule(
              form.country_id, // validate objec
              $root, // context
              $tr('country') // lable for feedback
            )
          "
          :invalid="form.country_id.$invalid"
          height="150px"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <div class="w-full d-flex mt-2">
      <InputCard
        :title="this.$tr('company')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCompany = v)"
      >
        <HorizontalItemContainer
          v-model="form.company_id.$model"
          :items="filterCompany"
          :loading="isFetchingcompanies"
          :no_data="filterCompany.length < 1 && !isFetchingcompanies"
          height="200px"
          :rules="
            validateRule(
              form.company_id, // validate objec
              $root, // context
              $tr('company') // lable for feedback
            )
          "
          :invalid="form.company_id.$invalid"
        ></HorizontalItemContainer>
      </InputCard>
    </div>
    <div class="mt-3">
      <InputCard
        :title="this.$tr('project')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchProject = v)"
      >
        <NoCheckboxItemContainer
          v-model="form.project_id.$model"
          :items="filterProject"
          :loading="isFetchingprojects"
          :no_data="filterProject.length < 1 && !isFetchingprojects"
          height="150px"
          nameLogo="name"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <div class="mt-3">
      <InputCard
        :title="this.$tr('sales_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="d-flex justify-space-between"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-for="item in salesType"
            :key="item.id"
            :item="item"
            @blur="form.sales_type.$touch()"
            :selected="item.id == form.sales_type.$model"
            :disable="!getIsActive(item)"
            @click="onSalesTypeSelected(item)"
            :rules="
              validateRule(
                form.sales_type, // validate objec
                $root, // context
                $tr('pageType') // lable for feedback
              )
            "
            :invalid="form.sales_type.$invalid"
          />
        </div>
      </InputCard>
    </div>
  </div>
</template>
  
  <script>
import GlobalRules from "~/helpers/vuelidate";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
export default {
  props: {
    form: Object, // default prop
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      searchCountry: "",
      searchCompany: "",
      searchProject: "",
      isFetchingCountry: false,
      isFetchingcompanies: false,
      isFetchingprojects: false,
      isFetchingProductCode: false,
      countries: [],
      companies: [],
      projects: [],
      salesType: [
        {
          id: "Landing Page Sales",
          name: "landing_page_sales",
          active: true,
          not_allowed_companies: ["uae-magicstylish", "uae-aegallery"],
        },
        {
          id: "WhatsApp Sales",
          name: "whatsapp_sales",
          active: true,
          allowed_companies: ["uae-magicstylish", "uae-aegallery"],
        },
        {
          id: "Quick Card Sales",
          name: "quick_card_sales",
          active: true,
          allowed_companies: ["uae-magicstylish", "uae-aegallery"],
        },
      ],
      selected_company_name: "",
    };
  },
  watch: {
    "form.country_id.$model": function (countryId) {
      if (countryId) {
        this.companies = [];
        this.projects = [];
        this.fetchItems({ type: "companies", id: countryId });
      }
    },
    "form.company_id.$model": function (company_id) {
      if (company_id) {
        this.projects = [];
        this.selectSalesTypeAccordingToCompany(company_id);
        this.fetchItems({ type: "projects", id: company_id });
      }
    },
  },
  computed: {
    filterCountry: function () {
      if (this.searchCountry) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchCountry?.toLowerCase());
        return this.countries.filter(filter);
      }
      return this.countries;
    },
    filterCompany() {
      if (this.searchCompany) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchCompany?.toLowerCase());
        return this.companies.filter(filter);
      }
      return this.companies;
    },
    filterProject() {
      if (this.searchProject) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchProject?.toLowerCase());
        return this.projects.filter(filter);
      }
      return this.projects;
    },
  },
  methods: {
    async loaded() {
      this.fetchCountries();
      // this.FetchProductCode();
    },

    async validate() {
      this.form.country_id.$touch();
      this.form.company_id.$touch();
      this.form.project_id.$touch();
      // this.form.product_name.$touch();
      let isValid =
        !this.form.country_id.$invalid &&
        !this.form.company_id.$invalid &&
        !this.form.project_id.$invalid;
      // !this.form.product_name.$invalid;
      return isValid;
    },
    onSalesTypeSelected(item) {
      if (this.getIsActive(item)) this.form.sales_type.$model = item.id;
    },
    async fetchCountries() {
      try {
        this.isFetchingCountry = true;
        this.countries = [];
        const url = "advertisement/connection/generate_link/country/all";
        const { data } = await this.$axios.get(url);
        this.countries = data.items;
      } catch (error) {}
      this.isFetchingCountry = false;
    },
    async fetchItems({ type, id }) {
      try {
        this["isFetching" + type] = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        this[data.type] = data.items;
        if (type == "companies") this.data.companies = data.items;
        this["isFetching" + type] = false;
        return data;
      } catch (error) {
        this["isFetching" + type] = false;
      }
    },
    getIsActive(item) {
      return true;
      if (item.allowed_companies) {
        if (item.allowed_companies?.includes(this.selected_company_name))
          return true;
      } else if (item.not_allowed_companies) {
        if (!item.not_allowed_companies?.includes(this.selected_company_name))
          return true;
      }
      return false;
    },
    selectSalesTypeAccordingToCompany(company_id) {
      const company = this.companies.find((row) => row.id == company_id);
      if (company) {
        this.selected_company_name = company.name;
        this.salesType.forEach((row) => {
          if (row.allowed_companies) {
            if (row.allowed_companies?.includes(this.selected_company_name)) {
              this.form.sales_type.$model = "WhatsApp Sales";
              return true;
            }
          } else if (row.not_allowed_companies) {
            if (
              !row.not_allowed_companies?.includes(this.selected_company_name)
            ) {
              this.form.sales_type.$model = "Landing Page Sales";
              return true;
            }
          }
        });
      }
    },
  },
  components: {
    CTitle,
    InputCard,
    NoCheckboxItemContainer,
    HorizontalItemContainer,
    SelectItem,
  },
};
</script>
  
  <style>
</style>
  