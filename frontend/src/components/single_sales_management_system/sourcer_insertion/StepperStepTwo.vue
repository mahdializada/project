<!-- <template>
  <div>
    <div class="h-full d-flex align-center-justify-center">
      <div class="w-full">
        <CAutoComplete
          prepend-inner-icon="#"
          @blur="form.sourcing_type.$touch()"
          v-model="form.sourcing_type.$model"
          :label="$tr('label_required', $tr('sourcing type'))"
          :rules="
            validateRule(
              form.sourcing_type, // validate objec
              $root, // context
              $tr('Sourcing type') // lable for feedback
            )
          "
          :items="sourcingTypes"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('sourcing type'))"
          class="me-md-2"
          :invalid="form.sourcing_type.$invalid"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CAutoComplete
          @blur="form.importing_country_id.$touch()"
          v-model="form.importing_country_id.$model"
          :rules="
            validateRule(form.importing_country_id, $root, 'importing country')
          "
          :label="$tr('importing country')"
          :items="countries"
          item-value="id"
          item-text="name"
          country
          :placeholder="$tr('choose_item', $tr('importing country'))"
          :invalid="form.importing_country_id.$invalid"
          prepend-inner-icon="fa fa-globe"
        />
      </div>
      <div class="w-full ml-2">
        <CAutoComplete
          @blur="form.receiving_country_id.$touch()"
          v-model="form.receiving_country_id.$model"
          :rules="
            validateRule(form.receiving_country_id, $root, 'importing country')
          "
          :label="$tr('receiving country')"
          :items="countries"
          item-value="id"
          item-text="name"
          country
          :placeholder="$tr('choose_item', $tr('receiving country'))"
          :invalid="form.receiving_country_id.$invalid"
          prepend-inner-icon="fa fa-globe"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextArea
          @blur="form.reason_for_search.$touch()"
          v-model="form.reason_for_search.$model"
          :rules="
            validateRule(form.reason_for_search, $root, 'reason_for_search')
          "
          prepend-inner-icon="mdi-information"
          :title="$tr('Reason For Search')"
          :placeholder="$tr('choose_item', $tr('Reason For Search'))"
          :invalid="form.reason_for_search.$invalid"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextField
          @blur="form.approx_quantity.$touch()"
          :label="$tr('label_required', $tr('Approx quantity'))"
          placeholder="approx_quantity"
          v-model="form.approx_quantity.$model"
          :rules="validateRule(form.approx_quantity, $root, 'approx_quantity')"
          :invalid="form.approx_quantity.$invalid"
          prepend-inner-icon="fa fa-tag mdi-rotate-90"
        />
      </div>
      <div class="w-full ml-2">
        <CTextField
          @blur="form.target_cost.$touch()"
          :label="$tr('label_required', $tr('Target Cost'))"
          placeholder="target_cost"
          v-model="form.target_cost.$model"
          :rules="validateRule(form.target_cost, $root, 'target_cost')"
          :invalid="form.target_cost.$invalid"
          prepend-inner-icon="fa fa-tag mdi-rotate-90"
        />
      </div>
    </div>

    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CAutoComplete
          @blur="form.required_shipping_method.$touch()"
          v-model="form.required_shipping_method.$model"
          :rules="
            validateRule(
              form.required_shipping_method,
              $root,
              'Shipping Methods'
            )
          "
          :label="$tr('Shipping Methods')"
          :items="countries"
          item-value="name"
          item-text="name"
          country
          :placeholder="$tr('choose_item', $tr('Shipping Methods'))"
          type="autocomplete"
          class="me-md-2"
          :invalid="form.required_shipping_method.$invalid"
          prepend-inner-icon="fa fa-tag  mdi-rotate-90"
        />
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CustomInput from "../../design/components/CustomInput.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
export default {
  components: {
    CustomInput,
    CSelect,
    CTextArea,
    CAutoComplete,
    CTextField,
  },
  props: {
    form: Object, // default prop
  },
  data() {
    return {
      validateRule: GlobalRules.validate, // gloabl function fro validate
      sourcingTypes: [
        { id: "source1", name: this.$tr("sourcer_1") },
        { id: "source2", name: this.$tr("sourcer_2") },
      ],
      isLoadingProducts: false,
      countries: [],
    };
  },

  created() {
    this.fetchCountries();
  },
  methods: {
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.sourcing_type.$touch();
      this.form.importing_country_id.$touch();
      this.form.receiving_country_id.$touch();
      this.form.approx_quantity.$touch();
      this.form.target_cost.$touch();
      this.form.required_shipping_method.$touch();
      this.form.reason_for_search.$touch();
      return (
        !this.form.sourcing_type.$invalid &&
        !this.form.importing_country_id.$invalid &&
        !this.form.receiving_country_id.$invalid &&
        !this.form.approx_quantity.$invalid &&
        !this.form.target_cost.$invalid &&
        !this.form.required_shipping_method.$invalid &&
        !this.form.reason_for_search.$invalid
      );
    },
    // async loaded() {
    // 	this.fetchProductByCategory(this.form.$model.category_id);
    // },
    async fetchCountries() {
      try {
        const url = "common/countries";
        const { data } = await this.$axios.get(url);
        this.countries = data.countries;
      } catch (error) {}
    },
  },
};
</script> -->

<template>
  <div class="h-full mt-5">
    <CTitle :text="$tr('general_info')" />
    <div class="w-full d-flex">
      <InputCard
        :title="$tr('Country')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCountry = v)"
      >
        <NoCheckboxItemContainer
          :items="filterCountry"
          :loading="isFetchingCountry"
          :no_data="filterCountry.length < 1 && !isFetchingCountry"
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
          :items="filterCompany"
          :loading="isFetchingCompanies"
          :no_data="filterCompany.length < 1 && !isFetchingCompanies"
          height="200px"
        ></HorizontalItemContainer>
      </InputCard>
    </div>
    <div class="w-full d-flex mt-2">
      <div class="w-full">
        <CTextField
          :title="$tr('label_required', $tr('name'))"
          :placeholder="$tr('name')"
          prepend-inner-icon="mdi-link-variant"
        />
      </div>
      <div class="w-full">
        <CTextField
          :title="$tr('label_required', $tr('lastname'))"
          :placeholder="$tr('lastname')"
          prepend-inner-icon="mdi-link-variant"
        />
      </div>
    </div>
    <div class="w-full d-flex mt-2">
      <div class="w-full">
        <CTextField
          :title="$tr('label_required', $tr('phone'))"
          :placeholder="$tr('phone')"
          prepend-inner-icon="mdi-link-variant"
        />
      </div>
      <div class="w-full">
        <CTextField
          :title="$tr('label_required', $tr('email'))"
          :placeholder="$tr('email')"
          prepend-inner-icon="mdi-link-variant"
        />
      </div>
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
import CTextField from '../../new_form_components/Inputs/CTextField.vue';

export default {
  components: {
    InputCard,
    NoCheckboxItemContainer,
    HorizontalItemContainer,
    CTitle,
    SelectItem,
    CTextField,
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
      console.log(this.form);
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
        this.form.$model.companyIds = this.form.$model.editData.companies.map(
          (i) => {
            return i.id;
          }
        );
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
