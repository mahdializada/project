<template>
  <v-form
    :key="currentIndex"
    lazy-validation
    ref="supplierUpdateForm"
    @submit.prevent="onSubmit"
  >
    <Edit
      :is-icon="false"
      :icon-or-logo="''"
      :headers="headers"
      :title="title"
      :showTitle="true"
      :topIcon="topIcon"
      :tableName="tableName"
      :isLoading="isLoading"
      :is-submitting="isSubmitting"
      :is-auto-edit="isAutoEdit"
      :totals="editItems.length"
      :current-index="currentIndex"
      ref="editRef"
      @onSubmit="onSubmit"
      @close="closeDialog"
      @onItemChange="onItemChange"
      @onSave="onSave"
      @OnSaveAndNext="onSaveAndNext"
      @onValidate="checkValidations"
      @onChangeTo="onChangeTo"
    >
      <template v-slot:step1>
        <div>
          <HeaderTitle title="general_information" />

          <div class="mx-3">
            <v-row>
              <v-col cols="12" md="6" lg="6" class="py-0">
                <div class="d-flex flex-column flex-md-row">
                  <div class="w-full">
                    <CustomInput
                      :items="allCompanies"
                      v-model="company"
                      @blur="touchCompany"
                      :error-messages="companyErrors"
                      :label="$tr('label_required', $tr('company'))"
                      type="autocomplete"
                      item-text="name"
                      item-value="id"
                      has-logo
                      :placeholder="$tr('choose_item', $tr('company'))"
                      class="me-md-4 mb-md-2 mb-0"
                    />
                  </div>
                  <div class="w-100">
                    <FormBtn
                      @click="addCompany"
                      title="add_plus"
                      class="mt-md-4 mb-2"
                    />
                  </div>
                </div>
                <div class="selected d-flex flex-wrap">
                  <template >
                    <SelectedItem
                    v-for="(com, index) in selectedCompanies"
                      @close="removeCompany(index)"
                      :key="index"
                      :title="com.name"
                      :logo-url="com.logo"
                    />
                  </template>
                </div>
                <div class="w-full">
                  <CustomInput
                    :items="commercialTypes"
                    :label="$tr('label_required', $tr('commercial_type'))"
                    :placeholder="$tr('choose_item', $tr('commercial_type'))"
                    type="autocomplete"
                    item-text="type"
                    item-value="type"
                    v-model.trim="$v.supplier.commercial_type.$model"
                    :rules="
                      rules.commercialTypeRule(
                        $v.supplier.commercial_type,
                        $root
                      )
                    "
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :items="countryTypes"
                    :label="$tr('label_required', $tr('country_type'))"
                    :placeholder="$tr('choose_item', $tr('country_type'))"
                    type="autocomplete"
                    item-text="type"
                    item-value="type"
                    v-model.trim="$v.supplier.country_type.$model"
                    :rules="
                      rules.countryTypeRule($v.supplier.country_type, $root)
                    "
                  />
                </div>
              </v-col>
              <v-col cols="12" md="6" lg="6" class="py-0">
                <div class="w-full">
                  <CustomInput
                    :items="supplyTypes"
                    :label="$tr('label_required', $tr('supply_type'))"
                    :placeholder="$tr('choose_item', $tr('supply_type'))"
                    commercial_type
                    type="autocomplete"
                    item-text="type"
                    item-value="type"
                    v-model.trim="$v.supplier.supply_type.$model"
                    :rules="
                      rules.supplyTypeRule($v.supplier.supply_type, $root)
                    "
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :items="legalTypes"
                    commercial_type
                    :label="$tr('label_required', $tr('legal_type'))"
                    :placeholder="$tr('choose_item', $tr('legal_type'))"
                    type="autocomplete"
                    item-text="type"
                    item-value="type"
                    v-model.trim="$v.supplier.legal_type.$model"
                    :rules="rules.legalTypeRule($v.supplier.legal_type, $root)"
                  />
                </div>
                <div class="d-flex flex-column flex-md-row">
                  <div class="w-full">
                    <CustomInput
                      :items="allSourcers"
                      v-model="sourcer"
                      @blur="touchSourcer"
                      :error-messages="sourcerErrors"
                      :label="$tr('label_required', $tr('sourcers'))"
                      type="autocomplete"
                      item-text="name"
                      item-value="id"
                      :placeholder="$tr('choose_item', $tr('sourcers'))"
                      class="me-md-4 mb-md-2 mb-0"
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
                  <template >
                    <SelectedItem
                    v-for="(source, index) in selectedSourcers"
                      @close="removeSourcer(index)"
                      :key="index"
                      :title="source.name"
                    />
                  </template>
                </div>
              </v-col>
            </v-row>
          </div>
        </div>
      </template>
      <template v-slot:step2>
        <div>
          <HeaderTitle title="supplier_information" />
          <div class="mx-3">
            <v-row>
              <v-col cols="12" md="6" lg="6" class="py-0">
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('supplier_trading_name'))"
                    placeholder="supplier_trading_name"
                    type="textfield"
                    v-model.trim="$v.supplier.supplier_trading_name.$model"
                    :rules="rules.supplierTradingNameRule($v.supplier, $root)"
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('main_phone'))"
                    placeholder="main_phone"
                    type="textfield"
                    v-model.trim="$v.supplier.main_phone.$model"
                    :rules="rules.mainPhoneRule($v.supplier, $root)"
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('purchase_order_phone'))"
                    placeholder="purchase_order_phone"
                    type="textfield"
                    v-model.trim="$v.supplier.purchase_order_phone.$model"
                    :rules="rules.purchaseOrderPhoneRule($v.supplier, $root)"
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('prepration_period'))"
                    placeholder="prepration_period"
                    type="textfield"
                    v-model.trim="$v.supplier.prepration_period.$model"
                    :rules="rules.preprationPeriodRule($v.supplier, $root)"
                  />
                </div>
              </v-col>
              <v-col cols="12" md="6" lg="6" class="py-0">
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('supplier_name'))"
                    placeholder="supplier_name"
                    type="textfield"
                    v-model.trim="$v.supplier.supplier_name.$model"
                    :rules="rules.supplierNameRule($v.supplier, $root)"
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('email'))"
                    placeholder="email"
                    type="textfield"
                    v-model.trim="$v.supplier.email.$model"
                    :rules="rules.emailRule($v.supplier, $root)"
                  />
                </div>
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('website'))"
                    placeholder="website"
                    type="textfield"
                    v-model.trim="$v.supplier.website.$model"
                    :rules="rules.websiteRule($v.supplier, $root)"
                  />
                </div>
              </v-col>
            </v-row>
          </div>
        </div>
      </template>
      <template v-slot:step3>
        <div>
          <HeaderTitle title="location" />

          <div class="d-flex justify-center py-0">
            <v-icon size="80">mdi-warehouse</v-icon>
          </div>
          <div class="d-flex justify-center py-0">
            <v-btn
              color="primary"
              class="px-3 rounded-sm"
              @click="addLocation"
              >{{ $tr("add_location") }}</v-btn
            >
          </div>

          <div>
            <div
              v-for="(location, k) in $v.addedLocations.$each.$iter"
              :key="k"
            >
              <div class="d-flex justify-space-between px-3">
                <span class="text-h5 font-weight-medium"
                  >{{ $tr("location") }} {{ k * 1 + 1 }}</span
                >
                <v-btn elevation="0" @click="removeLocation(k)">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </div>
              <div class="d-flex px-3">
                <v-row>
                  <v-col cols="12" md="6" lg="6" class="py-0">
                    <div class="w-full">
                      <CustomInput
                        :items="allCountries"
                        :label="$tr('label_required', $tr('country'))"
                        @input="fetchCountryStates(k)"  
                        :placeholder="$tr('choose_item', $tr('country'))"
                        type="autocomplete"
                        item-text="name"
                        item-value="id"
                        v-model="location.country_id.$model"
                        :rules="
                          requiredValidations(location.country_id, 'country_id')
                        "
                      />
                    </div>
                    <div class="w-full">
                      <CustomInput label="Location Type" />
                      <v-radio-group
                        v-model.trim="location.location_type.$model"
                        :rules="
                          requiredValidations(
                            location.location_type,
                            'location_type'
                          )
                        "
                        row
                        class="my-1 mb-0"
                      >
                        <v-radio :label="$tr('main')" value="1" />
                        <v-radio :label="$tr('sub')" value="0" />
                      </v-radio-group>
                    </div>
                    <div class="w-full">
                      <CustomInput
                        placeholder="map_link"
                        :label="$tr('label_required', $tr('map_link'))"
                        type="textfield"
                         v-model="location.map_link.$model"
                        :rules="
                          requiredValidations(location.map_link, 'map_link')
                        "
                      />
                    </div>
                    <div class="w-full">
                      <CustomInput
                        :items="crowdStatuses"
                        :placeholder="$tr('choose_item', $tr('crowd_status'))"
                        :label="$tr('label_required', $tr('crowd_status'))"
                        type="autocomplete"
                        item-text="name"
                        item-value="name"
                        v-model.trim="location.crowd_status.$model"
                        :rules="
                          requiredValidations(
                            location.crowd_status,
                            'crowd_status'
                          )
                        "
                      />
                    </div>
                  </v-col>
                  <v-col cols="12" md="6" lg="6" class="py-0">
                    <div class="w-full">
                      <CustomInput
                        :items="addedLocations[k].country_states"
                        :loading="isFetchingStates"
                        :label="$tr('label_required', $tr('state'))"
                        :placeholder="$tr('choose_item', $tr('state'))"
                        type="autocomplete"
                        item-text="name"
                        item-value="id"
                        v-model.trim="location.state_id.$model"
                        :rules="
                          requiredValidations(location.state_id, 'state_id')
                        "
                      />
                    </div>
                    <div class="w-full">
                      <CustomInput
                        placeholder="address"
                        label="address"
                        type="textarea"
                        v-model.trim="location.address.$model"
                        :rules="
                          unRequiredValidations(location.address, 'address')
                        "
                      />
                    </div>
                    <div class="w-full">
                      <CustomInput
                        placeholder="location_phone"
                        :label="$tr('label_required', $tr('location_phone'))"
                        type="textfield"
                        v-model.trim="location.location_phone.$model"
                        :rules="locationPhoneRule(location.location_phone)"
                      />
                    </div>
                    <div class="w-full">
                      <CustomInput
                        placeholder="contact_staff_name"
                        :label="
                          $tr('label_required', $tr('contact_staff_name'))
                        "
                        type="textfield"
                        v-model.trim="location.contact_staff_name.$model"
                        :rules="
                          unRequiredValidations(
                            location.contact_staff_name,
                            'contact_staff_name'
                          )
                        "
                      />
                    </div>
                  </v-col>
                  
                </v-row>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:step4>
        <div class="topDiv">
          <HeaderTitle title="remarks" />
          <!-- <div class="w-md-half mx-4 mt-3">
            <CustomInput
              item-value="id"
              item-text="title"
              :label="$tr('label_required', $tr('label'))"
              :placeholder="$tr('choose_item', $tr('label'))"
              type="autocomplete"
              v-model="$v.supplier.label_id.$model"
              :rules="rules.labelRule($v.supplier, $root)"
            />
          </div> -->
          <!-- <div class="w-full">
            <CustomInput
              :label="$tr('label_required', $tr('map_link'))"
              :placeholder="$tr('choose_item', $tr('map_link'))"
              type="textfield"
              v-model.trim="location.map_link.$model"
              :rules="unRequiredValidations(location.map_link, 'map_link')"
            />
          </div>  -->
          <v-col cols="12" md="12" lg="12" class="py-0">
                    <div class="w-full">
                      <CustomInput
                        placeholder="notes"
                        :label="$tr('label_required', $tr('notes'))"
                        type="textarea"
                        v-model.trim="$v.supplier.note.$model"
                        :rules="rules.noteRule($v.supplier, $root)"
                      />
                        <!-- :rules="unRequiredValidations(location.notes, 'note')" -->
                    </div>
                  </v-col>
        </div>
      </template>
      <template v-slot:step5>
        <div class="topDiv">
          <DoneMessage
            :title="$tr('item_all_set', $tr('supplier'))"
            :subTitle="$tr('you_can_access_your_item', $tr('suppliers'))"
          />
        </div>
      </template>
    </Edit>
  </v-form>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Edit from "../design/Edit.vue";
import HandleException from "../../helpers/handle-exception";
import Alert from "../../helpers/alert";
import DoneMessage from "../design/components/DoneMessage";
import CustomInput from "../design/components/CustomInput.vue";
import FormBtn from "../design/components/FormBtn.vue";
import SelectedItem from "../design/components/SelectedItem";
import SupplierValidations from "../../validations/supplier-validations";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import Validations from "./../../validations/validations";
import HeaderTitle from "../users/HeaderTitle.vue";

export default {
  name: "EditSupplier",
  components: {
    DoneMessage,
    Edit,
    CustomInput,
    FormBtn,
    SelectedItem,
    HeaderTitle,
  },
  props: {
    editItems: {
      require: true,
      type: Array,
    },

    editDialog: {
      required: true,
      type: Boolean,
    },

    isAutoEdit: {
      required: false,
      type: Boolean,
    },
    allCompanies: {
      required: true,
      type: Array,
    },
    // allLabels: {
    //   required: true,
    //   type: Array,
    // },
    allSourcers: {
      required: true,
      type: Array,
    },
    allCountries: {
      required: true,
      type: Array,
    },
  },

  data() {
    const firstIndex = 0;
    return {
      currentIndex: firstIndex,
      topIcon: "mdi-account",
      tableName: this.$tr("supplier"),

      headers: SupplierValidations.headers,
      // supplier:   SupplierValidations.schema,
      rules: SupplierValidations.rules,
      supplier: this.editItems[firstIndex],

      company: "",
      sourcer: "",
      selectedCompanies: [],
      selectedSourcers: [],
      deletedLocations: [],
      addedLocations: [],

      // commercialTypes: [
      //   { id: 1, name: "Teeb al hoor" },
      //   { id: 2, name: "First" },
      //   { id: 3, name: "Cassidy Wunsch Sr." },
      // ],
      commercialTypes: [
        { id: "1", type: "bussiness1" },
        { id: "2", type: "bussiness2" },
      ],

      // supplyTypes: [
      //   { id: 1, name: "Teeb al hoor" },
      //   { id: 2, name: "First" },
      //   { id: 3, name: "Morgan Glovhher" },
      // ],

      supplyTypes: [
        { id: "1", type: "aa" },
        { id: "2", type: "bb" },
      ],

      // legalTypes: [
      //   { id: 1, name: "Teeb al hoor" },
      //   { id: 2, name: "Katlynn Hoeger DVM" },
      //   { id: 3, name: "Second" },
      // ],
        legalTypes: [
          { id: "1", type: "type1" },
          { id: "2", type: "type2" },
        ],
      // countryTypes: [
      //   { id: 1, name: "Teeb al hoor" },
      //   { id: 2, name: "Catherine Dibbert III" },
      //   { id: 3, name: "Second" },
      // ],
        countryTypes: [
          { id: "1", type: "poor" },
          { id: "2", type: "rich" },
        ],
      states: [],

      // crowdStatuses: [
      //   { id: 1, name: "Low" },
      //   { id: 2, name: "Medium" },
      //   { id: 3, name: "Much" },
      // ],

      crowdStatuses: [
        { id: "low", name: "low" },
        { id: "crowd", name: "crowd" },
      ],
      
      companyErrors: "",
      sourcerErrors: "",

      isLoading: false,
      isFetchingStates: false,
      isSubmitting: false,
      title: "",
    };
  },

  validations: {
    supplier: SupplierValidations.validations,

    addedLocations: {
      $each: {
        country_id: Validations.requiredValidation,
        location_type: Validations.requiredValidation,
        crowd_status: Validations.requiredValidation,
        state_id: Validations.requiredValidation,
        location_phone: Validations.phoneValidation,
        notes: { minLength: minLength(3), maxLength: maxLength(500) },
        map_link: { minLength: minLength(3), maxLength: maxLength(500) },
        address: { minLength: minLength(3), maxLength: maxLength(100) },
        contact_staff_name: {
          minLength: minLength(2),
          maxLength: maxLength(100),
        },
      },
    },
  },

  created() {
    this.setSupplierDetails(this.editItems[0]);
    console.log("edit",this.editItems);
  },

  methods: {
    ...mapActions({
      updateItem: "suppliers/updateItem",
    }),

    addLocation() {
      this.addedLocations.push({
        id: "",
        country_id: "",
        location_type: "",
        map_link: "",
        crowd_status: "",
        notes: "",
        state_id: "",
        address: "",
        location_phone: "",
        contact_staff_name: "",
        country_states: [],
      });
    },

    removeLocation(index) {
      this.deletedLocations.push(this.addedLocations[index].id);
      this.addedLocations.splice(index, 1);
    },

    setSupplierDetails(item) {
      const data = this._.clone(item);
      this.title = data?.supplier_name;
      this.supplier = data;
      this.addedLocations = this._.cloneDeep(data?.locations);

      for (let index = 0; index < this.addedLocations.length; index++) {
        this.fetchCountryStates(index, this.addedLocations[index].country_id);
      }
      console.log(this.addedLocations);
      this.selectedCompanies = data?.companies.map((row) => {
        return { id: row.id, name: row.name };
      });
      this.selectedSourcers = data?.sourcers.map((row) => {
        return { id: row.id, name: row.name };
      });
    },

    addCompany() {
      if (!this.company) {
        this.touchCompany();
        return;
      }
      this.selectedCompanies.push(
        this.allCompanies.find((ele) => ele.id === this.company)
      );
      var set = new Set(this.selectedCompanies);
      this.selectedCompanies = Array.from(set);
      this.company = "";
      this.touchCompany();
    },
    removeCompany(index) {
      this.selectedCompanies.splice(index, 1);
      console.log(this.selectedCompanies);
      this.touchCompany();
    },

    touchCompany() {
      this.companyErrors =
        this.selectedCompanies.length === 0
          ? this.$tr("please_select_one_option")
          : "";
    },

    addSourcer() {
      if (!this.sourcer) {
        this.touchSourcer();
        return;
      }
      this.selectedSourcers.push(
        this.allSourcers.find((ele) => ele.id === this.sourcer)
      );
      var set = new Set(this.selectedSourcers);
      this.selectedSourcers = Array.from(set);
      this.sourcer = "";
      this.touchSourcer();
    },
    removeSourcer(index) {
      this.selectedSourcers.splice(index, 1);
      this.touchSourcer();
    },

    touchSourcer() {
      this.sourcerErrors =
        this.selectedSourcers.length === 0
          ? this.$tr("please_select_one_option")
          : "";
    },

    async fetchCountryStates(index, idd = "") {
      this.addedLocations[index].state = "";
      const country_id = !idd ? this.addedLocations[index].country_id : idd;
      this.isFetchingStates = true;
      try {
        const response = await this.$axios.get(
          `states/country-states/${country_id}`
        );
        this.states = response?.data;
        this.addedLocations[index].country_states = response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isFetchingStates = false;
    },

    closeDialog() {
      this.$emit("update:editDialog", false);
      this.$emit("update:isAutoEdit", false);
    },

    async onItemChange(actionType) {
      if (actionType === "next") {
        const index = this.currentIndex + 1;
        if (index < this.editItems.length) {
          this.setSupplierDetails(this.editItems[index]);
          this.currentIndex = index;
        }
      } else if (actionType === "back") {
        const index = this.currentIndex - 1;
        if (index >= 0) {
          this.setSupplierDetails(this.editItems[index]);
          this.currentIndex = index;
        }
      }
    },

    async onSaveAndNext() {
      await this.onSubmit("onSaveAndNext");
    },

    async onSave() {
      await this.onSubmit("can't");
    },

    isStep1Valid() {
      return !this.companyErrors &&
        !this.sourcerErrors &&
        !this.$v.supplier.supply_type.$invalid &&
        !this.$v.supplier.commercial_type.$invalid &&
        !this.$v.supplier.legal_type.$invalid &&
        !this.$v.supplier.country_type.$invalid
        ? true
        : false;
    },
    isStep2Valid() {
      return !this.$v.supplier.supplier_trading_name.$invalid &&
        !this.$v.supplier.supplier_name.$invalid &&
        !this.$v.supplier.main_phone.$invalid &&
        !this.$v.supplier.email.$invalid &&
        !this.$v.supplier.purchase_order_phone.$invalid &&
        !this.$v.supplier.prepration_period.$invalid &&
        !this.$v.supplier.website.$invalid
        ? true
        : false;
    },
    isStep3Valid() {
      return !this.$v.addedLocations.$invalid ? true : false;
    },
    isStep4Valid() {
      return !this.$v.supplier.label_id.$invalid &&
        !this.$v.supplier.note.$invalid
        ? true
        : false;
    },

    async checkValidations(currentStep) {
      this.$v.supplier.$touch();
      this.$refs.supplierUpdateForm.validate();
      switch (currentStep) {
        case 1:
          if (this.isStep1Valid()) {
            this.$refs.editRef.nextForce();
            this.$refs.supplierUpdateForm.resetValidation();
          }
          break;
        case 2:
          if (this.isStep2Valid()) {
            this.$refs.editRef.nextForce();
            this.$refs.supplierUpdateForm.resetValidation();
          }
          break;
        case 3:
          if (this.isStep3Valid()) {
            this.$refs.supplierUpdateForm.resetValidation();
            this.$refs.editRef.nextForce();
          }
          break;

        case 4:
          if (this.isStep4Valid()) {
            this.$refs.editRef.nextForce();
            this.$refs.supplierUpdateForm.resetValidation();
          }
          break;
        default:
          break;
      }
    },

    async onSubmit(canNext = "canNext") {
      this.supplier.companyIds = this.selectedCompanies.map((row) => {
        return row.id;
      });
      this.supplier.sourcerIds = this.selectedSourcers.map((row) => {
        return row.id;
      });
      this.supplier.addedLocations = this.addedLocations;
      this.supplier.deletedLocations = this.deletedLocations;

      this.$refs.supplierUpdateForm.validate();
      this.$v.supplier.$touch();
      if (
        !this.$v.supplier.$invalid &&
        !this.companyErrors &&
        !this.sourcerErrors
      ) {
        await this.updateRecord(this.supplier, canNext);
      } else {
        // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr('please_fill_all_fields_correctly'));
        
      }
    },

    async updateRecord(data, canNext) {
      this.isSubmitting = true;
      try {
        const response = await this.$axios.put("suppliers/id", data);
        this.isSubmitting = false;
        if (response?.status === 200 && response?.data?.result) {
          if (canNext === "onSaveAndNext") {
            if (this.currentIndex < this.editItems.length) {
              Alert.successAlert(this, "successfully_updated");
              this.onItemChange("next");
            }
          } else if (canNext === "canNext") {
            this.$refs.editRef.nextForce();
          } else {
            Alert.successAlert(this, "successfully_updated");
          }
          this.updateItem(response?.data?.data);
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
					this.$toasterNA("orange",this.$tr('something_went_wrong'));

        }
      } catch (error) {
        this.isSubmitting = false;
        HandleException.handleApiException(this, error);
      }
    },

    //  ***********************         Validations       ***********************
    requiredValidations(item, textfield) {
      return [
        (_) =>
          item.required || this.$tr("item_is_required", this.$tr(textfield)),
      ];
    },

    unRequiredValidations(item, textfield) {
      return [
        (_) =>
          item.minLength ||
          this.$tr(
            "min_length",
            this.$tr(textfield),
            item.$params.minLength.min
          ),
        (_) =>
          item.maxLength ||
          this.$tr(
            "max_length",
            this.$tr(textfield),
            item.$params.maxLength.max
          ),
      ];
    },
    locationPhoneRule(item) {
      return [
        (_) =>
          item.required ||
          this.$tr("item_is_required", this.$tr("location_phone")),
        (_) =>
          item.phoneNumber ||
          this.$tr("item_is_invalid", this.$tr("location_phone")),
      ];
    },

    async onChangeTo(step) {
      this.$v.supplier.$touch();
      this.$refs.supplierUpdateForm.validate();
      switch (step) {
        case 1:
          if (this.isStep1Valid()) {
            this.$refs.editRef.setCurrent(step);
            this.$refs.supplierUpdateForm.resetValidation();
          }
          break;
        case 2:
          if (this.isStep1Valid()) {
            this.$refs.editRef.setCurrent(step);
            this.$refs.supplierUpdateForm.resetValidation();
          }
          // if (this.isStep2Valid()) {
          //   this.$refs.editRef.nextForce();
          //   this.$refs.supplierUpdateForm.resetValidation();
          // }
          break;
        case 3:
          if (this.isStep1Valid()) {
            if (this.isStep2Valid()) {
              this.$refs.editRef.setCurrent(step);
              this.$refs.supplierUpdateForm.resetValidation();
            }
          }
          // if (this.isStep3Valid()) {
          //   this.$refs.supplierUpdateForm.resetValidation();
          //   this.$refs.editRef.nextForce();
          // }
          break;

        case 4:
          if (this.isStep1Valid()) {
            if (this.isStep2Valid()) {
              this.$refs.editRef.setCurrent(step);
              this.$refs.supplierUpdateForm.resetValidation();
            }
          }
          // if (this.isStep4Valid()) {
          //   this.$refs.editRef.nextForce();
          //   this.$refs.supplierUpdateForm.resetValidation();
          // }
          break;
        default:
          break;
      }
    },

    // async onSubmit(canNext = "canNext") {
    //   this.supplier.companyIds = this.selectedCompanies.map((row) => {
    //     return row.id;
    //   });
    //   this.supplier.sourcerIds = this.selectedSourcers.map((row) => {
    //     return row.id;
    //   });
    //   this.supplier.addedLocations = this.addedLocations;
    //   this.supplier.deletedLocations = this.deletedLocations;

    //   this.$refs.supplierUpdateForm.validate();
    //   this.$v.supplier.$touch();
    //   if (
    //     !this.$v.supplier.$invalid &&
    //     !this.companyErrors &&
    //     !this.sourcerErrors
    //   ) {
    //     await this.updateRecord(this.supplier, canNext);
    //   } else {
    //     // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
		// 			this.$toasterNA("orange",this.$tr('please_fill_all_fields_correctly'));
        
    //   }
    // },

    // async updateRecord(data, canNext) {
    //   this.isSubmitting = true;
    //   try {
    //     const response = await this.$axios.put("suppliers/id", data);
    //     this.isSubmitting = false;
    //     if (response?.status === 200 && response?.data?.result) {
    //       if (canNext === "onSaveAndNext") {
    //         if (this.currentIndex < this.editItems.length) {
    //           Alert.successAlert(this, "successfully_updated");
    //           this.onItemChange("next");
    //         }
    //       } else if (canNext === "canNext") {
    //         this.$refs.editRef.nextForce();
    //       } else {
    //         Alert.successAlert(this, "successfully_updated");
    //       }
    //       this.updateItem(response?.data?.data);
    //     } else {
    //       // this.$toastr.w(this.$tr("something_went_wrong"));
		// 			this.$toasterNA("orange",this.$tr('something_went_wrong'));

    //     }
    //   } catch (error) {
    //     this.isSubmitting = false;
    //     HandleException.handleApiException(this, error);
    //   }
    // },

    //  ***********************         Validations       ***********************
    requiredValidations(item, textfield) {
      return [
        (_) => item.required || this.$tr("required", this.$tr(textfield)),
      ];
    },

    unRequiredValidations(item, textfield) {
      return [
        (_) =>
          item.minLength ||
          this.$tr("min_length", [
            this.$tr(textfield),
            item.$params.minLength.min,
          ]),
        (_) =>
          item.maxLength ||
          this.$tr("max_length", [
            this.$tr(textfield),
            item.$params.maxLength.max,
          ]),
      ];
    },
    locationPhoneRule(item) {
      return [
        (_) =>
          item.required || this.$tr("required", [this.$tr("location_phone")]),
        (_) =>
          item.phoneNumber ||
          this.$tr("item_is_invalid", this.$tr("location_phone")),
      ];
    },
  },
};
</script>

<style>
.main-Card {
  background-color: #f9fafd !important;
}

.main-Card .title {
  color: #b6c1d2 !important;
}

.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
  display: none !important;
}

.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
  background-color: var(--v-primary-base);
  color: white;
}
</style>
