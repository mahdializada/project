<template>
  <v-row class="d-flex flex-column flex-md-row">
    <!-- input company -->
    <v-col cols="12" md="6" class="py-0">
      <div class="d-flex flex-column flex-md-row">
      <div class="w-full">
        <CustomInput
          :items="companies"
          v-model="supplier.company"
          item-text="name"
          item-value="id"
          type="autocomplete"
          :label="$tr('label_required',$tr('company'))"
          :placeholder="$tr('choose_item',$tr('company'))"
          has-logo
          class="me-md-4 mb-md-2 mb-0"

          :error-messages="companyErrors"
        />
      </div>

      <div class="w-100">
        <FormBtn @click="addCompany" title="add_plus" class="mt-md-4 mb-2" />
      </div>
      </div>
      <div class="selected d-flex flex-wrap">
          <SelectedItem
          v-for="(sub, index) in supplier.selectedCompanies"
            @close="removeCompany(index)"
            :key="index"
            :title="sub.name"
            :logo-url="sub.logo"
          />
      </div>
    </v-col>
       <!-- input supply_type-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :items="supplier.supplyTypes"
          :label="$tr('label_required',$tr('supply_type'))"
          :placeholder="$tr('choose_item',$tr('supply_type'))"
          type="autocomplete"
          v-model="supplierProp.supply_type.$model"
          item-text="type"
          item-value="type"
          :rules="rules.supplyTypeRule(supplierProp.supply_type, $root)"
        />
      </div>
    </v-col>
      <!-- input commercial-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :items="supplier.commercialTypes"
          :label="$tr('label_required',$tr('commercial_type'))"
          :placeholder="$tr('choose_item',$tr('commercial_type'))"
          type="autocomplete"
          v-model="supplierProp.commercial_type.$model"
          item-text="type"
          item-value="type"
          :rules="rules.commercialTypeRule(supplierProp.commercial_type, $root)"
        />
      </div>
    </v-col>
      <!-- input legal_type-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :items="supplier.legalTypes"
          :label="$tr('label_required',$tr('legal_type'))"
          :placeholder="$tr('choose_item',$tr('legal_type'))"
          type="autocomplete"
          v-model="supplierProp.legal_type.$model"
          item-text="type"
          item-value="type"
          :rules="rules.legalTypeRule(supplierProp.legal_type, $root)"
        />
      </div>
    </v-col>
      <!-- input country_type-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :items="supplier.countryTypes"
          :label="$tr('label_required',$tr('country_type'))"
          :placeholder="$tr('choose_item',$tr('country_type'))"
          type="autocomplete"
          v-model="supplierProp.country_type.$model"
          item-text="type"
          item-value="type"
          :rules="rules.countryTypeRule(supplierProp.country_type, $root)"
        />
      </div>
    </v-col>

      <!-- input supplier_source -->
    <v-col cols="12" md="6" class="py-0">
      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="sourcers"
            v-model="supplier.sourcer"
            :label="$tr('label_required',$tr('supplier_source'))"
            :placeholder="$tr('choose_item',$tr('supplier_source'))"
            type="autocomplete"
            item-text="name"
            item-value="id"
            class="me-md-4 mb-md-2 mb-0"
            :error-messages="sourcerErrors"
          />
        </div>
        <div class="w-100">
          <FormBtn
            @click="addSourcer"
            title="add_plus"
            class="mt-md-4 mb-2"
          />
        </div>
      </div>
      <div class="selected d-flex flex-wrap">
          <SelectedItem
          v-for="(sub, index) in supplier.selectedSourcers"
            @close="removeSourcer(index)"
            :key="index"
            :title="sub.name"
          />
      </div>
    </v-col>
  </v-row>
</template>

<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
import TextField from "../common/TextField";
import FormBtn from "~/components/design/components/FormBtn.vue";
import SelectedItem from "~/components/design/components/SelectedItem.vue";
import { mapActions, mapGetters } from "vuex";

export default {
  components: { CustomInput, FormBtn, TextField, SelectedItem },
  computed: {
    ...mapGetters({
      companies: "companies/getCompanies",
      sourcers: "sourcers/getSourcers",
    }),
  },
  props: {
    supplierProp: {
      required: true,
      type: Object,
    },

    rules: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {
      supplier: {
        // models
        company: this.companies,
        sourcer: this.sourcers,
        selectedCompanies: [],
        selectedSourcers: [],

        // data
        commercialTypes: [
          { id: "1", type: "bussiness1" },
          { id: "2", type: "bussiness2" },
        ],
        countryTypes: [
          { id: "1", type: "poor" },
          { id: "2", type: "rich" },
        ],
        supplyTypes: [
          { id: "1", type: "aa" },
          { id: "2", type: "bb" },
        ],
        legalTypes: [
          { id: "1", type: "type1" },
          { id: "2", type: "type2" },
        ],
      },
      sourcerErrors: "",
      companyErrors: "",
    };
  },
  methods: {
    ...mapActions({
      fetchCompanies: "companies/fetchCompanies",
      fetchSourcers: "sourcers/fetchSourcers",
      insertData: "suppliers/setSupplierData",
    }),
    addCompany() {
      if (!this.supplier.company) {
        this.touchCompany();
        return;
      }
      this.supplier.selectedCompanies.push(
        this.companies.find((ele) => ele.id === this.supplier.company)
      );
      var set = new Set(this.supplier.selectedCompanies);
      this.supplier.selectedCompanies = Array.from(set);
      this.supplier.company = "";
      this.touchCompany();
    },
    removeCompany(index) {
      this.supplier.selectedCompanies.splice(index, 1);
      this.touchCompany();
    },
    touchCompany() {
      this.companyErrors =
        this.supplier.selectedCompanies.length === 0
          ? this.$tr("please_select_one_option")
          : "";
    },
    addSourcer() {
      if (!this.supplier.sourcer) {
        this.touchSourcer();
        return;
      }
      this.supplier.selectedSourcers.push(
        this.sourcers.find((ele) => ele.id === this.supplier.sourcer)
      );
      var set = new Set(this.supplier.selectedSourcers);
      this.supplier.selectedSourcers = Array.from(set);
      this.supplier.sourcer = "";
      this.touchSourcer();
    },
    removeSourcer(index) {
      this.supplier.selectedSourcers.splice(index, 1);
      this.touchSourcer();
    },

    touchSourcer() {
      this.sourcerErrors =
        this.supplier.selectedSourcers.length === 0
          ? this.$tr("please_select_one_option")
          : "";
    },
    // check validation
    checkValidation() {
      const isStepOneValid =
        !this.companyErrors &&
        !this.sourcerErrors &&
        !this.supplierProp.supply_type.$invalid &&
        !this.supplierProp.commercial_type.$invalid &&
        !this.supplierProp.legal_type.$invalid &&
        !this.supplierProp.country_type.$invalid
          ? true
          : false;

      return isStepOneValid;
    },
    // check if compant selected
  },
  created() {
    if (this.companies.length == 0) {
      this.fetchCompanies({ key: "all" });
    }

    if (this.sourcers.length == 0) {
      this.fetchSourcers();
    }
  },
};
</script>
