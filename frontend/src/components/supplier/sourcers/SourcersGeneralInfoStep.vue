<template>
    <div class="h-full mt-5">
      <CTitle :text="$tr('general_info')" />
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
            :loading="isFetchingCompanies"
            :no_data="filterCompany.length < 1 && !isFetchingCompanies"
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
    </div>
  </template>
  
  <script>
  import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
  import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
  import InputCard from "../../new_form_components/components/InputCard.vue";
  import CTitle from "../../new_form_components/Inputs/CTitle.vue";
  import GlobalRules from "~/helpers/vuelidate";
  import CustomButton from "../../design/components/CustomButton.vue";
  
  export default {
    components: {
      InputCard,
      NoCheckboxItemContainer,
      HorizontalItemContainer,
      CTitle,
      CustomButton,
    },
    props: {
      form: Object,
    },
    watch: {
      "form.country_id.$model": function (countryId) {
        if (!this.isEdit) {
          this.form.company_id.$model = '';
        }
        if (countryId) {
          this.companies = [];
          // this.fetchCompanies({ country_id: this.form.country_id.$model });
          this.fetchItems({ type: "companies", id: countryId });
        }
      },
    },
    data() {
      return {
        searchCompany: "",
        searchCountry: "",
        searchTemplate: "",
        isFetchingCompanies: false,
        isFetchingCountry: false,
        isFetchingTemplate: false,
        selected: "",
        countries: [],
        companies: [],
        template_id: [],
        products: [],
        validateRule: GlobalRules.validate,
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
    methods: {
      async validate() {
        let isValid =
          !this.form.country_id.$invalid &&
          !this.form.company_id.$invalid 
        return isValid;
      },
      async loaded() {
        console.log(this.form,"hashmat");
        await this.fetchCountries();
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
          this.isFetchingCompanies = true;
          const url = `advertisement/connection/generate_link/${type}/${id}`;
          const { data } = await this.$axios.get(url);
          this[data.type] = data.items;
          this.isFetchingCompanies = false;
          if (this.form.$model.editData) {
            this.form.$model.company_id = this.form.$model.editData.company_id
          }
          return data;
        } catch (error) {
          this.isFetchingCompanies = false;
        }
      },
    }, 
  };
  </script>
  
  <style >
  .sales__type__container {
    display: flex;
    justify-content: space-between;
  }
  .image-card .selected {
    border-color: var(--v-primary-base) !important;
    box-shadow: 0 0 10px #2e7be633;
  }
  </style>