<template>
  <div class="h-full mt-5">
    <CTitle :text="$tr('select_country')" />
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
          :loading="isFetchingCompany"
          :no_data="filterCompany.length < 1 && !isFetchingCompany"
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
    <div class="w-full mt-2">
      <InputCard
        :title="this.$tr('sales_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-for="item in salesType"
            :key="item.id"
            :item="item"
            @blur="form.sales_type.$touch()"
            :selected="item.id == form.sales_type.$model"
            :disable="!getIsActive(item)"
            @click="onpageTypeSelected(item)"
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
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
import GlobalRules from "~/helpers/vuelidate";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      searchCountry: "",
      isFetchingCountry: false,
      countries: [],
      searchCompany: "",
      isFetchingCompany: false,
      companies: [],
      validateRule: GlobalRules.validate,
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
    };
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
  },
  watch: {
    "form.$model.country_id": function () {
      if (this.form.$model.country_id) {
        this.companies = [];
        this.fetchCompanies({
          type: "companies",
          id: this.form.$model.country_id,
        });
      }
    },
    "form.company_id.$model": function (company_id) {
      if (company_id) {
        this.selectSalesTypeAccordingToCompany(company_id);
      }
    },
  },
  methods: {
    async validate() {
      console.log(this.form);
      this.form.country_id.$touch();
      this.form.company_id.$touch();
      this.form.sales_type.$touch();
      return (
        !this.form.country_id.$invalid &&
        !this.form.company_id.$invalid &&
        !this.form.sales_type.$invalid
      );
    },
    async loaded() {
      await this.fetchCountries();
    },
    async fetchCountries() {
      try {
        this.countries = [];
        this.isFetchingCountry = true;
        const url = "advertisement/connection/generate_link/country/all";
        const { data } = await this.$axios.get(url);
        this.countries = data.items;
      } catch (error) {}
      this.isFetchingCountry = false;
    },
    async fetchCompanies({ type, id }) {
      try {
        this.isFetchingCompany = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        this[data.type] = data.items;
        this.isFetchingCompany = false;
        return data;
      } catch (error) {
        this.isFetchingCompany = false;
      }
    },
    onpageTypeSelected(item) {
      if (this.getIsActive(item)) this.form.sales_type.$model = item.id;
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