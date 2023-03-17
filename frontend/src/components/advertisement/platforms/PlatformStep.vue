<template>
  <div class="h-full mt-5">
    <div class="w-full d-flex">
      <InputCard
        :title="this.$tr('platforms')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchPlaform = v)"
      >
        <NoCheckboxItemContainer
        v-model="form.platform_name.$model"
          :items="filterPlatform"
          :loading="isFetchingplatforms"
          :no_data="filterPlatform.length < 1 && !isFetchingplatforms"
          height="150px"
          :hasLogo="true"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <div class="w-full d-flex mt-2">
      <CTextField
        v-model="form.platform_key.$model"
        :title="$tr('label_required', $tr('platform_key'))"
        :rules="
          validateRule(
            form.platform_key, // validate objec
            $root, // context
            $tr('platform_key') // lable for feedback
          )
        "
        :placeholder="$tr('platform_key')"
        :invalid="form.platform_key.$invalid"
        prepend-inner-icon="fa fa-atom"
      />
    </div>
    <div class="w-full d-flex mt-2">
      <InputCard
        :title="this.$tr('company')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchCompany = v)"
      >
        <HorizontalItemContainer
          v-model="form.companies.$model"
          :items="filterCompany"
          :loading="isFetchingCompany"
          :no_data="filterCompany.length < 1 && !isFetchingCompany"
          height="200px"
          :multi="true"
        ></HorizontalItemContainer>
      </InputCard>
    </div>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
export default {
  props: {
    form: Object,
    editData:{
      type: String,
      default:'',
    }
  },
  components: {
    InputCard,
    NoCheckboxItemContainer,
    CTextField,
    HorizontalItemContainer,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      isFetchingplatforms: false,
      isFetchingCompany: false,
      platforms: [],
      searchPlaform: "",
      searchCompany: "",
      companies: [],
    };
  },
  computed: {
    filterPlatform() {
      if (this.searchPlaform) {
        const filter = (item) =>
          item?.platform_name
            ?.toLowerCase()
            ?.includes(this.searchPlaform?.toLowerCase());
        return this.platforms.filter(filter);
      }

      return this.platforms;
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
      this.form.platform_name.$touch();
      this.form.platform_key.$touch();
      this.form.companies.$touch();
      if(this.form.platform_name.$model)
      this.form.platform_name.$model = this.platforms.find((item)=>item.id == this.form.platform_name.$model).name;
      return (
        !this.form.platform_name.$invalid &&
        !this.form.platform_key.$invalid &&
        !this.form.companies.$invalid
      );
    },
    async loaded() {
      this.fetchCompanies();
      await this.fetchPlatformNames();
      if(this.editData)
        this.form.platform_name.$model = this.platforms.find((item)=>item.name == this.editData).id;
    },
    async fetchPlatformNames() {
      try {
        this.isFetchingplatforms = true;
        const url = "advertisement/platform_names";
        const { data } = await this.$axios.get(url);
        this.platforms = data;
      } catch (error) {}
      this.isFetchingplatforms = false;
    },
    async fetchCompanies() {
      try {
        this.isFetchingCompany = true;

        const url = "common/companies/";
        const { data } = await this.$axios.get(url);
        this.companies = data.companies;
      } catch (error) {}
      this.isFetchingCompany = false;
    },
  },
};
</script>

<style>
</style>
 