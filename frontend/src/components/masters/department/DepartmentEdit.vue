<template>
  <v-form
    ref="departmentForm"
    lazy-validation
    id="departmentForm"
    @submit.prevent=""
  >
    <edit
      ref="edit_stepper"
      :headers="headers"
      :editData="autoEditData[this.current - 1]"
      :tableName="tableName"
      :isIcon="false"
      :isLoading="isDataLoaded"
      :isSubmitting="isLoadingOrSubmitting"
      :showTitle="false"
      @close="closeDialog"
      @onValidate="validateStepper"
      @onSubmit="onSubmit"
      @onSave="onSave"
      @onChangeTo="changeTo"
      @OnSaveAndNext="onSaveAndNext"
      :isAutoEdit="isAutoEdit"
      :totals="autoEditData.length"
      :currentIndex="this.current - 1"
      @onItemChange="onItemChange"
    >
      <template v-slot:general>
        <div class="topDiv" style="overflow-x: hidden">
          <HeaderTitle title="General" />
          <FilterCountry
            :key="current"
            ref="filterCountry"
            :countryProps="selectedCountries"
            :companyProps="selectedCompanies"
            topMinus
            isDepartment
            skipDepartment
            :loadCountries="loadCountries"
          />
        </div>
      </template>
      <template v-slot:department>
        <div class="topDiv" style="overflow-x: hidden">
          <HeaderTitle title="Department" />
          <div class="w-full">
            <CustomInput
              :key="current"
              v-model.trim="$v.department.name.$model"
              :rules="
                validate(
                  $v.department.name,
                  $root,
                  $tr('item_name', $tr('department'))
                )
              "
              :label="$tr('item_name_required', $tr('department'))"
              :placeholder="$tr('item_name', $tr('department'))"
              :error-messages="nameErrorMessage"
              type="textfield"
              name="name"
            />
          </div>
          <div class="w-full mt-2">
            <CustomInput
              :key="current"
              v-model.trim="$v.department.business_location_id.$model"
              :rules="
                validate(
                  $v.department.business_location_id,
                  $root,
                  'business_location'
                )
              "
              :items="allBusinessLocations"
              :label="$tr('label_required', $tr('business_location'))"
              :placeholder="$tr('choose_item', $tr('business_location'))"
              item-value="id"
              item-text="name"
              type="autocomplete"
              name="business_location_id"
            />
          </div>
          <div>
            <div class="d-flex flex-column flex-md-row">
              <div class="w-full">
                <CustomInput
                  v-model="selectedIndustry"
                  :items="industries"
                  :label="$tr('label_required', $tr('industry'))"
                  :placeholder="$tr('choose_item', $tr('industry'))"
                  item-value="id"
                  return-object
                  item-text="name"
                  class="me-md-4 mb-md-2"
                  type="autocomplete"
                  :error-messages="industryErrorMessage"
                />
              </div>
              <div class="w-100">
                <FormBtn
                  @click="addSelectedIndustry"
                  :title="'add_plus'"
                  :class="`mt-md-4 mb-2`"
                />
              </div>
            </div>
            <div class="selected d-flex flex-wrap">
              <SelectedItem
                v-for="selectedIndustry in selectedIndustries"
                :key="selectedIndustry.id"
                @close="removeSelectedIndustry(selectedIndustry.id)"
                :title="selectedIndustry.name"
              />
            </div>
          </div>
        </div>
      </template>
      <template v-slot:remark>
        <div class="topDiv" style="overflow-x: hidden">
          <HeaderTitle title="remarks" />
          <LabelRemark
            :key="current"
            ref="remarksRef"
            :item="$v.department"
          />
        </div>
      </template>
      <template v-slot:done>
        <div class="d-flex justify-center">
          <done-message
            :title="$tr('item_all_set', $tr('department'))"
            :subTitle="$tr('you_can_access_your_item', $tr('department'))"
          />
        </div>
      </template>
    </edit>
  </v-form>
</template>

<script>
import CustomInput from "../../design/components/CustomInput.vue";
import { mapActions, mapGetters } from "vuex";
import DepartmentValidations from "../../../validations/department-validations";
import DoneMessage from "../../design/components/DoneMessage.vue";
import Edit from "../../design/Edit.vue";
import HandleException from "../../../helpers/handle-exception";
import HeaderTitle from "../../users/HeaderTitle.vue";
import FilterCountry from "../../users/FilterCountry.vue";
import GlobalRules from "~/helpers/vuelidate";
import LabelRemark from "../../users/LabelRemark.vue";
import FormBtn from "../../design/components/FormBtn";
import SelectedItem from "../../design/components/SelectedItem";

export default {
  components: {
    CustomInput,
    DoneMessage,
    Edit,
    HeaderTitle,
    FilterCountry,
    LabelRemark,
    FormBtn,
    SelectedItem,
  },
  props: {
    isAutoEdit: {
      type: Boolean,
      default: false,
    },
    autoEditData: {
      require: true,
      type: Array,
    },
    options: {
      type: Object,
      require: true,
    },
    tabKey: {
      type: String,
      require: true,
    },
  },
  async created() {
    this.fetchIndustries();
    this.fetchBusinessLocations();
    this.setEditData();
  },
  data() {
    return {
      //*HM* industry section *HM*
      selectedIndustry: null,
      selectedIndustries: [],
      industryErrorMessage: "",

      current: 1,
      validate: GlobalRules.validate,
      nameErrorMessage: "",
      isLoadingOrSubmitting: false,
      isDataLoaded: false,
      allIndustries: [],
      allBusinessLocations: [],
      selectedCompanies: [],
      selectedCountries: [],
      department: {
        name: "",
        company_ids: "",
        industries: "",
        note: "",
        business_location_id: "",
      },
      tableName: this.$tr("department"),
      isNameDublicate: false,
      headers: [
        {
          icon: "fa-info-circle",
          title: "general",
          slotName: "general",
        },
        {
          icon: "fa-lock",
          title: "department",
          slotName: "department",
        },
        {
          icon: "fa-comment-dots",
          title: "remarks",
          slotName: "remark",
        },
        {
          icon: "fa-thumbs-up",
          title: "done",
          slotName: "done",
        },
      ],
      loadCountries: true,
    };
  },
  validations: {
    department: DepartmentValidations.validations.department,
  },
  computed: {
    ...mapGetters({
      countries: "filterData/getCountriesWithCompanies",
      companies: "departments/companies",
      industries: "filterData/getIndustries",
    }),
  },
  methods: {
    ...mapActions({
      fetchDepartments: "departments/fetchDepartments",
      fetchIndustries: "filterData/fetchIndustries",
    }),

    //*HM* Remove selected Industry *HM*
    removeSelectedIndustry(id) {
      if (id) {
        this.selectedIndustries = this.selectedIndustries.filter(
          (item) => item.id !== id
        );
      }
    },

    //*HM* Add the selected industry *HM*
    addSelectedIndustry() {
      if (this.selectedIndustry) {
        this.industryErrorMessage = "";
        const itemId = this.selectedIndustry.id;
        const found = this.selectedIndustries.find(
          (item) => item.id === itemId
        );
        if (found) return;

        this.selectedIndustries.push(this.selectedIndustry);
        this.selectedIndustry = "";
      } else {
        this.industryErrorMessage = this.$tr(
          "item_is_required",
          this.$tr("industry")
        );
      }
    },

    isInArray(arr, search) {
      let res = arr.find((item) => {
        return item.id === search.id;
      });
      return res !== undefined;
    },
    closeDialog() {
      this.$emit("onCloseDialog");
    },

    isFormDataValid() {
      return this.$v.department.$invalid;
    },

    async editRecord(department, isSave = false) {
      try {
        this.isLoadingOrSubmitting = true;
        const response = await this.$axios.put(
          `/departments/${this.department.id}`,
          department
        );
        if (response?.status == 200 && response?.data?.result) {
          this.$toasterNA("green", this.$tr('successfully_updated'));
          // this.fetchDepartments({
          // 	...this.options,
          // 	key: this.tabKey,
          // });
          if (!isSave) {
            this.$refs.edit_stepper.nextForce();
          }
        } else {
          // this.$toastr.e(response?.data);
				this.$toasterNA("red", this.$tr(response?.data));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isLoadingOrSubmitting = false;
      // this.$emit("close");
    },

    setData() {
      const selectedCompanies = this.$refs.filterCountry.selectedCompanies;
      this.department.company_ids = selectedCompanies.map(
        (company) => company.id
      );
    },

    setEditData() {
      const item = JSON.parse(
        JSON.stringify(this.autoEditData[this.current - 1])
      );
      this.selectedIndustries = item.industries;
      item.industries = item.industries.map((item) => item.id);
      this.department = JSON.parse(JSON.stringify(item));
      this.selectedCompanies = this.department.companies;
      let depComp = this.selectedCompanies;
      this.selectedCountries = [];
      for (let i = 0; i < depComp.length; i++) {
        for (let j = 0; j < depComp[i].countries.length; j++) {
          if (
            !this.isInArray(this.selectedCountries, depComp[i].countries[j])
          ) {
            this.selectedCountries.push(depComp[i].countries[j]);
          }
        }
      }
    },

    async onSubmit(isSave = false) {
      this.setData();
      let formData = {
        id: this.department.id,
        name: this.department.name,
        company_ids: this.department.company_ids,
        note: this.department.note,
        industries: this.selectedIndustries.map((item) => item.id),
        business_location_id: this.department.business_location_id,
      };
      this.validateForm();
      if (!this.isFormDataValid()) {
        await this.editRecord(formData, isSave);
        this.$refs.departmentForm.resetValidation();
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    async onSave(isSaveAndNext = false) {
      await this.onSubmit(true);
      if (!isSaveAndNext) {
        this.$refs.edit_stepper.success();
      }
    },

    async onSaveAndNext() {
      await this.onSave(true);
      this.onItemChange("next");
    },

    onItemChange(dir) {
      this.loadCountries = false;
      if (dir == "next" && this.current !== this.autoEditData.length) {
        this.current++;
        this.setEditData();
      } else if (dir == "back" && this.current !== 1) {
        this.current--;
        this.setEditData();
      }
    },

    validateForm() {
      this.$refs.departmentForm.validate();
      this.$v.department.$touch();
    },

    async changeTo(current_index) {
      switch (current_index) {
        case 2:
          if (this.isGenerateValid()) {
            this.$refs.edit_stepper.changeTo(current_index);
          }
          break;
        case 3: {
          if (this.isGenerateValid()) {
            if (await this.isStepTwoDataValid()) {
              this.$refs.edit_stepper.changeTo(current_index);
            }
          }
        }
      }
    },

    async validateStepper(currentStep) {
      this.department.industries = this.selectedIndustries.map(
        (item) => item.id
      );

      switch (currentStep) {
        case 1:
          if (this.isGenerateValid()) {
            this.$refs.edit_stepper?.nextForce();
            this.$refs.departmentForm.resetValidation();
          }
          break;
        case 2:
          if (await this.isStepTwoDataValid()) {
            this.$refs.edit_stepper?.nextForce();
            this.$refs.departmentForm.resetValidation();
          }
          break;
        case 3:
          if (this.isRemarksValid()) {
            this.$refs.edit_stepper?.nextForce();
            this.$refs.departmentForm.resetValidation();
          }
          break;
      }
    },

    isGenerateValid() {
      return this.$refs.filterCountry.checkValidation();
    },

    isRemarksValid() {
      return this.$refs.remarksRef.checkValidation();
    },

    async isStepTwoDataValid() {
      if (this.selectedIndustries.length < 1) {
        this.industryErrorMessage = this.$tr(
          "item_is_required",
          this.$tr("industry")
        );
      }

      this.validateForm();
      let isNameDublicate = false;
      let isStepTwoDataValid =
        this.$v.department.name.$invalid ||
        this.$v.department.industries.$invalid ||
        this.$v.department.business_location_id.$invalid;
      if (!isStepTwoDataValid) {
        this.isDataLoaded = true;
        this.$refs.departmentForm.resetValidation();
        const res = await this.checkNameUniqueness();
        isNameDublicate = res.isNameAlreadyExists;
        if (isNameDublicate) {
          this.nameErrorMessage = this.$tr(
            "item_already_exists",
            this.$tr("department")
          );
        } else {
          this.nameErrorMessage = "";
        }
        this.isDataLoaded = false;
        return !(!isStepTwoDataValid && isNameDublicate);
      } else {
        return !isStepTwoDataValid;
      }
    },

    async checkNameUniqueness() {
      try {
        this.$v.department.$touch();
        const name = this.$v.department?.name.$model;
        const id = this.department.id;
        const columns = { name: name };
        let url = `departments/check-uniqueness?id=${id}`;
        const response = await this.$axios.post(url, columns);
        return {
          isNameAlreadyExists: response?.data?.name,
        };
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },

    async fetchBusinessLocations() {
      try {
        const response = await this.$axios.get("/business-locations", {
          params: { for_select: true },
        });
        this.allBusinessLocations = response?.data?.data;

        return response?.data?.data;
      } catch (error) {
        return null;
      }
    },
  },
};
</script>
