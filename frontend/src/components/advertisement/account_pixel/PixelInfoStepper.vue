<template>
     <div class="connection__container">
          <CTitle text="connection_information" />
          <div class="mb-3">
               <InputCard :title="this.$tr('country')" :hasSearch="true" :hasPagination="false"
                    @search="(v) => (searchCountry = v)">
                    <ItemsContainer v-model="form.country_id.$model" :items="filterCountry" :loading="fetchingCountries"
                         :no_data="filterCountry.length < 1 && !fetchingCountries" height="150px"></ItemsContainer>
               </InputCard>
          </div>
          <div class="mb-3">
               <InputCard :title="this.$tr('company')" :hasSearch="true" :hasPagination="false"
                    @search="(v) => (searchCompany = v)">
                    <HorizontalItemContainer v-model="form.company_id.$model" :items="filterCompany"
                         :loading="isFetchingcompanies" :no_data="filterCompany.length < 1 && !isFetchingcompanies"
                         height="150px"></HorizontalItemContainer>
               </InputCard>
          </div>

     </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";


export default {
     props: {
          form: Object,
          fetchCountry: Boolean,
     },

     watch: {
          "form.country_id.$model": function (countryId) {
               if (this.form.shouldReset.$model) {
                    this.form.company_id.$model = null;
                    this.form.platform_id.$model = null;
                    this.form.application_id.$model = null;
               }
               if (countryId) {
                    this.companies = [];
                    this.ispps = [];
                    this.projects = [];
                    this.fetchItems({ type: "companies", id: countryId });
                    if (this.products.length < 1) {
                         this.fetchItems({ type: "products", id: "all" });
                    }
               }
          },
          "form.company_id.$model": function (company_id) {
               if (this.form.shouldReset.$model) {
                    this.form.platform_id.$model = null;
                    this.form.application_id.$model = null;
               }

               if (company_id) {
                    this.ispps = [];
                    this.projects = [];
                    this.fetchItems({ type: "ispps", id: company_id });
                    this.fetchItems({ type: "projects", id: company_id });
               }
          },

          // fetchCountry: function (value) {
          //   if (value) {
          //     this.fetchCountries();
          //   }
          // },
     },

     data() {
          return {
               searchCountry: "",
               searchCompany: "",
               searchProject: "",
               fetchingCountries: false,
               salesType: [
                    { id: "Single Sale", name: "single_sales" },
                    { id: "Shopping Cart", name: "shopping_cart" },
               ],
               validateRule: GlobalRules.validate,
               isFetchingcompanies: false,
               isFetchingprojects: false,
               isFetchingproducts: false,
               isFetchingispps: false,

               countries: [],
               companies: [],
               projects: [],
               products: [],
               ispps: [],
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
          async loaded() {
               this.form.shouldReset.$model = true;
               await this.fetchCountries();
          },

          validate() {
               let isValid =
                    !this.form.country_id.$invalid &&
                    !this.form.company_id.$invalid;
               return isValid;
          },

          async fetchCountries() {
               if (this.countries.length > 0) return;
               try {
                    this.fetchingCountries = true;
                    const url = "advertisement/connection/generate_link/country/all";
                    const { data } = await this.$axios.get(url);
                    this.countries = data.items;
               } catch (error) { }
               this.fetchingCountries = false;
          },

          async fetchItems({ type, id }) {
               try {
                    this["isFetching" + type] = true;
                    const url = `advertisement/connection/generate_link/${type}/${id}`;
                    const { data } = await this.$axios.get(url);
                    this[data.type] = data.items;
               } catch (error) { }
               this["isFetching" + type] = false;
          },
     },
     components: {
          CTitle,
          InputCard,
          ItemsContainer,
          HorizontalItemContainer
     },
};
</script>
   
<style scoped lang="scss">
.sales__type__container {
     display: flex;
     justify-content: space-between;
}
</style>
   