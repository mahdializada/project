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
            @blur="form.country_id.$touch()"
            :rules="
              validateRule(
                form.country_id, // validate objec
                $root, // context
                $tr('country') // lable for feedback
              )
            "
            :invalid="form.country_id.$invalid"
            height="250px"
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
            v-model="form.companyIds.$model"
            :items="filterCompany"
            :loading="isFetchingCompanies"
            :multi="true"
            :no_data="filterCompany.length < 1 && !isFetchingCompanies"
            @blur="form.companyIds.$touch()"
            height="200px"
            :rules="
              validateRule(
                form.companyIds, // validate objec
                $root, // context
                $tr('company') // lable for feedback
              )
            "
            :invalid="form.companyIds.$invalid"
          ></HorizontalItemContainer>
        </InputCard>
      </div>
    </div>
  </template>
  
  <script>
  import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
  import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
  import InputCard from "../../new_form_components/components/InputCard.vue";
  import SelectItem from "../../new_form_components/components/SelectItem.vue";
  import CTitle from "../../new_form_components/Inputs/CTitle.vue";
  import GlobalRules from "~/helpers/vuelidate";
  
  export default {
    components: {
      InputCard,
      NoCheckboxItemContainer,
      HorizontalItemContainer,
      CTitle,
      SelectItem,
    },
    props: {
      form: Object,
    },
    watch: {
      "form.country_id.$model": function (countryId) {
        if (!this.isEdit) {
          this.form.companyIds.$model = [];
        }
        if (countryId) {
          this.companies = [];
          this.fetchItems({ type: "companies", id: countryId });
        }
      },
    },
    data() {
      return {
        searchCompany: "",
        searchCountry: "",
        isFetchingCompanies: false,
        isFetchingCountry: false,
        selected: "",
        countries: [],
        companies: [],
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
        this.form.country_id.$touch();
        this.form.companyIds.$touch();
        let isValid =
          !this.form.country_id.$invalid && !this.form.companyIds.$invalid;
        return isValid;
      },
      async loaded() {
        await this.fetchCountries();
      },
  
      async fetchCountries() {
        try {
          this.isFetchingCountry = true;
          this.countries = [];
          const url = "supplier-country";
          const { data } = await this.$axios.get(url);
          this.countries = data;
          // selected first index of country
          this.form.$model.country_id = this.countries[0].id;
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
          this.form.$model.companyIds = this.form.$model.editData.companies.map((i)=>{
            return i.id;
          });
          return data;
        } catch (error) {
          this.isFetchingCompanies = false;
        }
      },
      async fetchTemplates({ id, type }) {
        try {
          this.isFetchingTemplate = true;
          this[data.type] = this.templates;
        } catch (error) {}
        this.isFetchingTemplate = false;
      },
      onpageTypeSelected(item) {
        if (item.active == true) this.form.page_Type.$model = item.id;
      },
      onSelected(i, item) {
        this.form.template_id.$model = item;
        this.selected = i;
      },
      OpenTemplateDialog() {
        this.$refs.TemplateLinkRef.openDialog();
      },
      add_link(item) {
        this.form.template_id.$model.push(item);
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