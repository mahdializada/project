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
          :multi="true"
        ></HorizontalItemContainer>
      </InputCard>
    </div>
    <div class="w-full mt-2">
      <InputCard
        :title="this.$tr('page_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-for="item in pageType"
            :key="item.id"
            :item="item"
            @blur="form.page_Type.$touch()"
            :selected="item.id == form.page_Type.$model"
            :disable="!item.active"
            @click="onpageTypeSelected(item)"
            :rules="
              validateRule(
                form.page_Type, // validate objec
                $root, // context
                $tr('pageType') // lable for feedback
              )
            "
            :invalid="form.page_Type.$invalid"
          />
        </div>
      </InputCard>
    </div>
    <template-link-pop-up ref="TemplateLinkRef" @click="add_link" />
    <div class="w-full mt-2">
      <InputCard
        :title="this.$tr('landing_page_template')"
        :hasSearch="false"
        :hasPagination="false"
      >
        <CustomButton
          type="primary"
          icon="fa-plus"
          text="add link"
          class="float-right"
          @click="OpenTemplateDialog"
        />
        <div class="d-flex flex-wrap">
          <span
            class="image-card text-center cursor-pointer mt-1 mr-1 position-relative"
            v-for="(item, i) in form.template_id.$model"
            :key="i"
            @click="onSelected(i , item)"
            @blur="form.template_id.$touch()"
            :rules="
              validateRule(
                form.template_id, // validate objec
                $root, // context
                $tr('template_id') // lable for feedback
              )
            "
            :invalid="form.template_id.$invalid"
            style="width: 180px; height: 200px"
          >
            <iframe
              :class="`pa-1 ${selected == i ? 'selected' : ''}`"
              loading="lazy"
              style="
                width: 100%; 
                height: 100%;
                top: 0;
                left: 0;
                border: 2px solid #f5f5f5;
                padding: 0;
                margin: 0;
                border-radius: 20px;  
              " 
              :src="item" 
              allowfullscreen="allowfullscreen"
              allow="fullscreen"
            >
            </iframe>

          </span>
          <!-- <div v-if="selected == i" class="d-flex justify-center align-center position-absolute rounded-pill" style="width:18px; height:18px;background-color: var(--v-primary-base);bottom:18%; right:47%">
              <v-icon  size="14" color="white" >mdi-check-bold</v-icon>
            </div> -->
          <!-- <img
              :src="item.image"
              width="170px"
              height="130px"
              :class="`pa-1 ${selected == item.id ? 'selected' : ''}`"
              style="border: 2px solid grey; border-radius: 20px;"
            />

            <br /><span :style="`${selected == item.id ? 'color:var(--v-primary-base)' : ''}`">{{ item.title }}</span> -->
        </div>
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
import CustomButton from "../../design/components/CustomButton.vue";
import TemplateLinkPopUp from "./TemplateLinkPopUp.vue";

export default {
  components: {
    InputCard,
    NoCheckboxItemContainer,
    HorizontalItemContainer,
    CTitle,
    SelectItem,
    CustomButton,
    TemplateLinkPopUp,
  },
  props: {
    form: Object,
  },
  watch: {
    "form.country_id.$model": function (countryId) {
      if (!this.isEdit) {
        this.form.company_id.$model = [];
      }
      if (countryId) {
        this.companies = [];
        // this.fetchCompanies({ country_id: this.form.country_id.$model });
        this.fetchItems({ type: "companies", id: countryId });
      }
    },
    "form.company_id.$model": function (companyId) {
      if (!this.isEdit) {
        this.form.template_id.$model = [];
      }
      if (companyId) {
        this.templates = [];
        // this.fetchCompanies({ country_id: this.form.country_id.$model });
        this.fetchTemplates({ type: "templates", id: companyId });
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
      pageType: [
        { id: "Whatsapp", name: "Whatsapp", active: true },
        { id: "Lead", name: "Lead", active: true },
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
    filterTemplate() {
      if (this.searchTemplate) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchTemplate?.toLowerCase());
        return this.templates.filter(filter);
      }
      return this.templates;
    },
  },
  methods: {
    async validate() {
      this.form.page_Type.$touch();
      let isValid =
        !this.form.country_id.$invalid &&
        !this.form.company_id.$invalid &&
        !this.form.page_Type.$invalid &&
        !this.form.template_id.$invalid;
      console.log(this.form);
      return isValid;
    },
    async loaded() {
      await this.fetchCountries();
      this.products = [];
      console.log(this.form);
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
    // async fetchCompanies(param) {
    //   try {
    //     this.isFetchingCompany = true;

    //     const url =  `advertisement/connection/generate_link/${type}/${id}`;
    //     const { data } = await this.$axios.get(url);
    //     this.companies = data.items;
    //   } catch (error) {}
    //   this.isFetchingCompany = false;
    // },

    async fetchItems({ type, id }) {
      try {
        this.isFetchingCompanies = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        this[data.type] = data.items;
        this.isFetchingCompanies = false;
        return data;
      } catch (error) {
        this.isFetchingCompanies = false;
      }
    },
    async fetchTemplates({ id, type }) {
      try {
        this.isFetchingTemplate = true;

        // const url = `advertisement/connection/generate_link/${type}/${id}`;
        // const { data } = await this.$axios.get(url);

        this[data.type] = this.templates;
      } catch (error) {}
      this.isFetchingTemplate = false;
    },
    onpageTypeSelected(item) {
      if (item.active == true) this.form.page_Type.$model = item.id;
      // if ((item.id == "Whatsapp", "Lead")) {
      //   this.fetchTemplates();
      // }
    },
    onSelected(i , item) {
      this.form.template_id.$model = item;
      this.selected = i;
      console.log(this.form.template_id.$model = item);
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