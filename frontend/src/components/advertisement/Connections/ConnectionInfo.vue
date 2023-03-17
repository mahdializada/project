<template>
  <div class="connection__container">
    <CTitle text="connection_information" />
    <div class="mb-3">
      <InputCard
        :title="this.$tr('country')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCountry = v)"
      >
        <ItemsContainer
          v-model="form.country_id.$model"
          :items="filterCountry"
          :loading="fetchingCountries"
          :no_data="filterCountry.length < 1 && !fetchingCountries"
          height="150px"
        ></ItemsContainer>
      </InputCard>
    </div>
    <div class="mb-3">
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
          height="150px"
        ></HorizontalItemContainer>
      </InputCard>
    </div>
    <div class="mb-3">
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
    <div class="mb-3">
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
            :selected="item.id == form.sales_type.$model"
            :disable="!getIsActive(item)"
            @click="onSalesTypeSelected(item)"
          />
        </div>
      </InputCard>
    </div>
    <div class="mb-3">
      <CAutoComplete2
        @blur="form.pcode.$touch()"
        :rules="validateRule(form.pcode, $root, $tr('product'))"
        :title="$tr('label_required', $tr('product'))"
        :items="products"
        item-value="pcode"
        item-text="pcode"
        p_name="name"
        return-object
        :loading="isFetchingproducts"
        :placeholder="$tr('choose_item', $tr('product'))"
        prepend-inner-icon="fa fa-box"
        @change="onProductSelected"
      />
    </div>
    <div class="mb-3">
      <CAutoComplete
        @blur="form.ispp_id.$touch()"
        v-model="form.ispp_id.$model"
        :rules="validateRule(form.ispp_id, $root, $tr('ispp'))"
        :title="$tr('label_required', $tr('ispp'))"
        :items="ispps"
        item-value="id"
        item-text="product_name"
        :loading="isFetchingispps"
        :placeholder="$tr('choose_item', $tr('ispp'))"
        prepend-inner-icon="fa fa-globe"
      />
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CAutoComplete2 from "../../new_form_components/Inputs/CAutoComplete2.vue";

export default {
  props: {
    form: Object,
    data: Object,
    fetchCountry: Boolean,
  },

  watch: {
    "form.country_id.$model": function (countryId) {
      if (this.form.shouldReset.$model) {
        this.form.company_id.$model = null;
        this.form.ispp_id.$model = null;
        this.form.project_id.$model = null;
        this.form.platform_id.$model = null;
        this.form.application_id.$model = null;
        this.form.pcode.$model = null;
        this.form.landing_page_link.$model = null;
        this.form.product_has_link.$model = false;
      }
      if (countryId) {
        this.companies = [];
        this.ispps = [];
        this.projects = [];
        this.fetchItems({ type: "companies", id: countryId });
      }
    },
    "form.company_id.$model": function (company_id) {
      if (this.form.shouldReset.$model) {
        this.form.ispp_id.$model = null;
        this.form.project_id.$model = null;
        this.form.platform_id.$model = null;
        this.form.application_id.$model = null;
        this.form.pcode.$model = null;
        this.form.landing_page_link.$model = null;
        this.form.product_has_link.$model = false;
      }

      if (company_id) {
        this.ispps = [];
        this.projects = [];
        this.products = [];

        this.fetchProducts(company_id);
        this.fetchItems({ type: "ispps", id: company_id });
        this.fetchItems({ type: "projects", id: company_id });
      }
    },
    "form.sales_type.$model": async function (type) {
      if (this.form.company_id.$model != null) {
        let page_type = 0;
        switch (type) {
          case "WhatsApp Sales":
            page_type = 1;
            break;
          case "Quick Card Sales":
            page_type = 2;
            break;
        }

        let response = await this.fetchItems({
          type: "products",
          id:
            this.form.company_id.$model +
            "," +
            this.form.country_id.$model +
            "," +
            page_type,
        });
      }
    },
  },

  data() {
    return {
      searchCountry: "",
      searchCompany: "",
      searchProject: "",
      fetchingCountries: false,
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
      selected_company_name: "",
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
    async fetchProducts(company_id) {
      this.selectSalesTypeAccordingToCompany(company_id);
      let page_type = 0;
      switch (this.form.sales_type.$model) {
        case "WhatsApp Sales":
          page_type = 1;
          break;
        case "Quick Card Sales":
          page_type = 2;
          break;
      }
      let response = await this.fetchItems({
        type: "products",
        id: company_id + "," + this.form.country_id.$model + "," + page_type,
      });
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
    async loaded() {
      this.form.shouldReset.$model = true;
      this.countries = [];
      this.companies = [];
      this.projects = [];
      this.products = [];
      this.ispps = [];
      this.salesType[0].active = true;
      this.salesType[1].active = true;
      this.salesType[2].active = false;
      await this.fetchCountries();
    },

    validate() {
      let isValid =
        !this.form.country_id.$invalid &&
        !this.form.company_id.$invalid &&
        !this.form.project_id.$invalid &&
        !this.form.sales_type.$invalid &&
        !this.form.pcode.$invalid &&
        !this.form.ispp_id.$invalid;
      return isValid;
    },
    onProductSelected(item) {
      if (item) {
        this.form.pcode.$model = item.pcode;
        this.form.landing_page_link.$model = item.landing_link;
        if (item.landing_link) {
          this.form.product_has_link.$model = true;
        }
      } else {
        this.form.pcode.$model = null;
        this.form.landing_page_link.$model = null;
        this.form.product_has_link.$model = false;
      }
    },
    async fetchCountries() {
      try {
        this.countries = [];
        this.fetchingCountries = true;
        const url = "advertisement/connection/generate_link/country/all";
        const { data } = await this.$axios.get(url);
        this.countries = data.items;
      } catch (error) {}
      this.fetchingCountries = false;
    },

    async fetchItems({ type, id }) {
      try {
        this["isFetching" + type] = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        this[data.type] = data.items;
        if (type == "companies") {
          this.data.companies = data.items;
        }
        this["isFetching" + type] = false;
        return data;
      } catch (error) {
        this["isFetching" + type] = false;
      }
    },
    onSalesTypeSelected(item) {
      if (this.getIsActive(item)) this.form.sales_type.$model = item.id;
    },
  },
  components: {
    CTitle,
    InputCard,
    ItemsContainer,
    HorizontalItemContainer,
    NoCheckboxItemContainer,
    SelectItem,
    CSelect,
    CAutoComplete,
    CAutoComplete2,
  },
};
</script>

<style scoped lang="scss">
.sales__type__container {
  display: flex;
  justify-content: space-between;
}
</style>
