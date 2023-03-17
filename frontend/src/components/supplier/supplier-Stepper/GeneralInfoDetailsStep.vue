<template>
  <div>
    <CTitle :text="'general_info_details'" />
    <div class="h-full d-flex">
      <div class="w-full">
        <CAutoComplete
          v-model="form.supply_type.$model"
          @blur="form.supply_type.$touch()"
          :rules="
            validateRule(
              form.supply_type, // validate objec
              $root, // context
              $tr('supply_type') // lable for feedback
            )
          "
          :items="supplyTypes"
          item-value="type"
          item-text="type"
          :placeholder="$tr('choose_item', $tr('supply_type'))"
          :invalid="form.supply_type.$invalid"
          prepend-inner-icon="mdi-account-supervisor"
          :isCheckNeeded="true"
          title="supply_type"
        />
      </div>
    </div>
    <div class="h-full d-flex mt-2">
      <div class="w-full">
        <CAutoComplete
          v-model="form.commercial_type.$model"
          @blur="form.commercial_type.$touch()"
          :rules="
            validateRule(
              form.commercial_type, // validate objec
              $root, // context
              $tr('commercial_type') // lable for feedback
            )
          "
          :items="commercialTypes"
          item-value="type"
          item-text="type"
          :placeholder="$tr('choose_item', $tr('commercial_type'))"
          :invalid="form.commercial_type.$invalid"
          prepend-inner-icon="mdi-format-list-bulleted-type"
          :isCheckNeeded="true"
          title="commercial_type"
        />
      </div>
    </div>
    <div class="h-full d-flex mt-2">
      <div class="w-full">
        <CAutoComplete
          v-model="form.legal_type.$model"
          @blur="form.legal_type.$touch()"
          :rules="
            validateRule(
              form.legal_type, // validate objec
              $root, // context
              $tr('legal_type') // lable for feedback
            )
          "
          :items="legalTypes"
          item-value="type"
          item-text="type"
          :placeholder="$tr('choose_item', $tr('legal_type'))"
          :invalid="form.legal_type.$invalid"
          prepend-inner-icon="#"
          :isCheckNeeded="true"
          title="legal_type"
        />
      </div>
    </div>

    <div class="h-full d-flex mt-2">
      <div class="w-full">
        <CAutoComplete
          v-model="form.country_type.$model"
          @blur="form.country_type.$touch()"
          :rules="
            validateRule(
              form.country_type, // validate objec
              $root, // context
              $tr('country_type') // lable for feedback
            )
          "
          :items="countryTypes"
          item-value="type"
          item-text="type"
          :placeholder="$tr('choose_item', $tr('country_type'))"
          :invalid="form.country_type.$invalid"
          prepend-inner-icon="#"
          :isCheckNeeded="true"
          title="country_type"
        />
      </div>
    </div>

    <div class="h-full d-flex mt-2">
      <div class="w-full">
        <CAutoComplete
          v-model="form.sourcerIds.$model"
          @blur="form.sourcerIds.$touch()"
          :rules="
            validateRule(
              form.sourcerIds, // validate objec
              $root, // context
              $tr('sourcer') // lable for feedback
            )
          "
          :items="sourcers"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('sourcer'))"
          :invalid="form.sourcerIds.$invalid"
          prepend-inner-icon="mdi-source-branch"
          :isCheckNeeded="true"
          :multiple="true"
          title="supplier_source"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import HorizontalItemContainer from "../../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
export default {
  // computed: {
  //   ...mapGetters({
  //     sourcers: "sourcers/getSourcers",
  //   }),
  //   filterSourcer: function () {
  //     if (this.searchSourcer) {
  //       const filter = (item) =>
  //         item?.name
  //           ?.toLowerCase()
  //           ?.includes(this.searchSourcer?.toLowerCase());
  //       return this.sourcers.filter(filter);
  //     }
  //     return this.sourcers;
  //   },
  // },
  props: { form: Object },
  components: {
    CAutoComplete,
    CTitle,
    InputCard,
    HorizontalItemContainer,
    NoCheckboxItemContainer,
},
  data() {
    return {
      commercialTypes: [
        { id: "1", type: "Distributer" },
        { id: "2", type: "Brand" },
        { id: "3", type: "Factory" },
      ],
      countryTypes: [
        { id: "1", type: "Local" },
        { id: "2", type: "International" },
      ],
      supplyTypes: [
        { id: "1", type: "Row Material" },
        { id: "2", type: "Ready Made" },
      ],
      legalTypes: [
        { id: "1", type: "Company" },
        { id: "2", type: "Indivitual" },
      ],
      validateRule: GlobalRules.validate,
      sourcers: [],
      isFetchingSourcer: false,
      searchSourcer: "",
    };
  },
  methods: {
    // ...mapActions({
    //   fetchSourcers: "sourcers/fetchSourcers",
    // }),
    async getSourcersData(){
      const sourcer_data = await this.$axios.get("sourcers?key=all");
      console.log('sourcer_data',sourcer_data);
      this.sourcers = sourcer_data.data.data;
    },
    async validate() {
      this.form.supply_type.$touch();
      this.form.commercial_type.$touch();
      this.form.legal_type.$touch();
      this.form.country_type.$touch();
      this.form.sourcerIds.$touch();
      return (
        !this.form.supply_type.$invalid &&
        !this.form.commercial_type.$invalid &&
        !this.form.legal_type.$invalid &&
        !this.form.country_type.$invalid &&
        !this.form.sourcerIds.$invalid
      );
    },
    async loaded() {
      this.getSourcersData();
    },
  },
};
</script>

<style></style>
